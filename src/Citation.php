<?php
/**
 * Citation class.
 *
 * @package RAMP
 */

namespace SSRC\RAMP;

use SSRC\RAMP\Zotero\Client;
use SSRC\RAMP\Zotero\Library as ZoteroLibrary;
use WP_Query;

/**
 * Citation class.
 */
class Citation {
	/**
	 * Citation data.
	 *
	 * @var array
	 */
	protected $data = [
		'post_id' => null,
	];

	/**
	 * Citation constructor.
	 *
	 * @param int|null $post_id Post ID.
	 * @return void
	 */
	public function __construct( $post_id = null ) {
		if ( $post_id ) {
			$this->data['post_id'] = (int) $post_id;
		}
	}

	/**
	 * Gets a Citation object from a Zotero ID.
	 *
	 * @param string $zotero_id Zotero ID.
	 * @return Citation|null
	 */
	public static function get_from_post_id( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'ramp_citation' !== $post->post_type ) {
			return null;
		}

		return new self( $post_id );
	}

	/**
	 * Gets Zotero data for the current citation.
	 *
	 * @return null|array
	 */
	public function get_zotero_data() {
		$post_id = $this->get_post_id();

		$cached = get_post_meta( $post_id, 'zotero_data', true );
		if ( is_array( $cached ) ) {
			$cached = array_filter( $cached );
		}

		// Refetch if we have corrupt data.
		if ( isset( $cached['data'] ) ) {
			$cached = null;
		}

		if ( $cached ) {
			return $cached;
		}

		$library_id = $this->get_zotero_library_id();
		$library    = $this->get_zotero_library( $library_id );

		$client = new Client( $library_id, $library->get_zotero_api_key() );

		$zotero_id = $this->get_zotero_id();
		$data      = $client->get_record( $zotero_id );

		if ( isset( $data['data'] ) ) {
			$data = (array) $data['data'];
		}

		$this->set_cached_zotero_data( $data );

		return $data;
	}

	/**
	 * Gets a Zotero ID for an item.
	 *
	 * @return string
	 */
	public function get_zotero_id() {
		return get_post_meta( $this->get_post_id(), 'zotero_id', true );
	}

	/**
	 * Gets a Zotero URL for an item, as situated under a Collection.
	 *
	 * @return string
	 */
	public function get_zotero_url() {
		// @todo This will need adjustment for multiple libraries.
		$library = $this->get_zotero_library();
		if ( ! $library ) {
			return '';
		}

		$base_url = $library->get_url();

		$collection_ids = $this->get_collections_for_zotero();
		if ( $collection_ids ) {
			$zotero_url = $base_url . '/collections/' . reset( $collection_ids ) . '/items/' . $this->get_zotero_id();
		} else {
			$zotero_url = $base_url . '/items/' . $this->get_zotero_id();
		}

		return $zotero_url;
	}

	/**
	 * Gets the local URL ("preview") for a citation.
	 *
	 * @return string
	 */
	public function get_preview_url() {
		return get_permalink( $this->get_post_id() );
	}

	/**
	 * Gets the URL for a citation's source item.
	 *
	 * @return string
	 */
	public function get_source_url() {
		$data = $this->get_zotero_data();

		if ( isset( $data['url'] ) ) {
			return $data['url'];
		}

		return '';
	}

	/**
	 * Gets the publication year for a citation.
	 *
	 * @return string
	 */
	public function get_publication_year() {
		$data = $this->get_zotero_data();

		$year = '';
		if ( ! empty( $data['date'] ) ) {
			// Four-digit numbers are years, and shouldn't be put through strtotime().
			if ( preg_match( '/^[0-9]{4}$/', $data['date'] ) ) {
				$year = $data['date'];

			} elseif ( preg_match( '/^[0-9]{4}\/[0-9]{1,2}$/', $data['date'] ) ) {
				// Here's another helpful citation format from Zotero.
				$year = substr( $data['date'], 0, 4 );

			} else {
				$detected_timestamp = strtotime( $data['date'] );
				if ( $detected_timestamp ) {
					$year = gmdate( 'Y', $detected_timestamp );
				}
			}
		}

		return $year;
	}

	/**
	 * Gets the author names for a citation.
	 *
	 * @return array
	 */
	public function get_author_names() {
		$data = $this->get_zotero_data();

		$item_type    = isset( $data['itemType'] ) ? $data['itemType'] : 'journalArticle';
		$author_names = [];

		if ( ! empty( $data['creators'] ) ) {
			foreach ( $data['creators'] as $creator ) {
				$include_in_list = true;

				// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( 'bookSection' === $item_type && isset( $creator->creatorType ) && 'editor' === $creator->creatorType ) {
					$include_in_list = false;
				}

				if ( ! $include_in_list ) {
					continue;
				}

				$first_name = isset( $creator->firstName ) ? $creator->firstName : '';
				$last_name  = isset( $creator->lastName ) ? $creator->lastName : '';

				if ( ! $first_name && ! $last_name ) {
					continue;
				}

				$author_names[] = sprintf(
					'%s, %s',
					$last_name,
					$first_name
				);
				// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			}
		}

		return $author_names;
	}

	/**
	 * Gets whether the citation is featured.
	 *
	 * @return bool
	 */
	public function get_is_featured() {
		return (bool) get_post_meta( $this->get_post_id(), 'is_featured', true );
	}

	/**
	 * Sets whether the citation is featured.
	 *
	 * @param bool $is_featured Whether the citation is featured.
	 * @return void
	 */
	public function set_is_featured( $is_featured ) {
		if ( $is_featured ) {
			update_post_meta( $this->get_post_id(), 'is_featured', '1' );
		} else {
			delete_post_meta( $this->get_post_id(), 'is_featured' );
		}
	}

	/**
	 * Sets the post ID for a citation.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function set_post_id( $post_id ) {
		$this->data['post_id'] = (int) $post_id;
	}

	/**
	 * Sets the Zotero ID for a citation.
	 *
	 * @param string $zotero_id Zotero ID.
	 * @return void
	 */
	public function set_zotero_id( $zotero_id ) {
		update_post_meta( $this->get_post_id(), 'zotero_id', $zotero_id );
	}

	/**
	 * Sets the Zotero data for a citation.
	 *
	 * @param array $data Zotero data.
	 * @return void
	 */
	public function set_cached_zotero_data( $data ) {
		update_post_meta( $this->get_post_id(), 'zotero_data', $data );
	}

	/**
	 * Deletes the Zotero data for a citation.
	 *
	 * @return void
	 */
	public function delete_cached_zotero_data() {
		delete_post_meta( $this->get_post_id(), 'zotero_data' );
	}

	/**
	 * Gets the post ID for a citation.
	 *
	 * @return int
	 */
	public function get_post_id() {
		return (int) $this->data['post_id'];
	}

	/**
	 * Get title as stored in WordPress.
	 *
	 * @return string
	 */
	public function get_title() {
		$post = get_post( $this->get_post_id() );
		if ( ! $post ) {
			return '';
		}

		return $post->post_title;
	}

	/**
	 * Get abstract as stored in WordPress.
	 *
	 * @return string
	 */
	public function get_abstract() {
		$post = get_post( $this->get_post_id() );
		if ( ! $post ) {
			return '';
		}

		return $post->post_content;
	}

	/**
	 * Update title as stored in WordPress.
	 *
	 * @param string $title Title.
	 * @return int|WP_Error
	 */
	public function set_title( $title ) {
		return wp_update_post(
			[
				'ID'         => $this->get_post_id(),
				'post_title' => $title,
			]
		);
	}

	/**
	 * Update abstract as stored in WordPress.
	 *
	 * @param string $abstract_text Text of the abstract.
	 * @return int|WP_Error
	 */
	public function set_abstract( $abstract_text ) {
		return wp_update_post(
			[
				'ID'           => $this->get_post_id(),
				'post_content' => $abstract_text,
			]
		);
	}

	/**
	 * Sets the publication date for an item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $publication_date Publication date.
	 *
	 * @return bool
	 */
	public function set_publication_date( $publication_date ) {
		return (bool) update_post_meta( $this->get_post_id(), 'publication_date', $publication_date );
	}

	/**
	 * Gets the publication date for an item.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_publication_date() {
		return get_post_meta( $this->get_post_id(), 'publication_date', true );
	}

	/**
	 * Get the Zotero Collection IDs belonging to an item, based on its WP Research Topics.
	 *
	 * @return array
	 */
	public function get_collections_for_zotero() {
		$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );

		$collection_ids = [];

		$research_topics_raw = wp_get_object_terms( $this->get_post_id(), 'ramp_assoc_topic' );
		foreach ( $research_topics_raw as $term_data ) {
			$collection_post_id = $rt_map->get_post_id_for_term_id( $term_data->term_id );
			$collection_id      = get_post_meta( $collection_post_id, 'zotero_collection_id', true );
			if ( $collection_id ) {
				$collection_ids[] = $collection_id;
			}
		}

		return $collection_ids;
	}

	/**
	 * Get the Zotero Tags belonging to an item, based on its WP Focus Tags.
	 *
	 * @return array
	 */
	public function get_tags_for_zotero() {
		$tags = [];

		$focus_tags_raw = wp_get_object_terms( $this->get_post_id(), 'ramp_focus_tag' );
		foreach ( $focus_tags_raw as $term_data ) {
			$tags[] = [
				'tag' => $term_data->name,
			];
		}

		return $tags;
	}

	/**
	 * Get the post ID of the Research Topic associated with a Zotero Collection ID.
	 *
	 * @param string $zotero_collection_id Zotero Collection ID.
	 * @return array
	 */
	public function get_post_id_from_zotero_collection_id( $zotero_collection_id ) {
		$existing = new WP_Query(
			[
				'post_type'      => 'ramp_topic',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'meta_query'     => [
					[
						'key'   => 'zotero_collection_id',
						'value' => $zotero_collection_id,
					],
				],
			]
		);

		if ( $existing->posts ) {
			$post_id = reset( $existing->posts );
		} else {
			$post_id = null;
		}

		return $post_id;
	}

	/**
	 * Set Research Topics for an item, based on its Zotero Collection IDs.
	 *
	 * @param array $collection_ids Zotero Collection IDs.
	 * @return void
	 */
	public function set_research_topics_from_collection_ids( $collection_ids ) {
		$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );

		$research_topics = [];
		foreach ( (array) $collection_ids as $collection_id ) {
			$rt_post_id = $this->get_post_id_from_zotero_collection_id( $collection_id );
			if ( ! $rt_post_id ) {
				continue;
			}

			$collection_term_id = $rt_map->get_term_id_for_post_id( $rt_post_id );
			if ( ! $collection_term_id ) {
				// Should never happen.
				continue;
			}

			$research_topics[] = $collection_term_id;
		}
		wp_set_object_terms( $this->get_post_id(), $research_topics, 'ramp_assoc_topic' );
	}

	/**
	 * Set Focus Tags for an item, based on its Zotero Tags.
	 *
	 * @param array $tags Zotero Tags.
	 * @return void
	 */
	public function set_focus_tags_from_tags( $tags ) {
		// Disabling for now.
		// phpcs:disable Squiz.PHP.NonExecutableCode.Unreachable
		return;

		$focus_tags = [];
		foreach ( $tags as $tag ) {
			$focus_tags[] = $tag->tag;
		}

		wp_set_object_terms( $this->get_post_id(), $focus_tags, 'ramp_focus_tag' );
		// phpcs:enable Squiz.PHP.NonExecutableCode.Unreachable
	}

	/**
	 * Gets the Zotero library ID for an item.
	 *
	 * @return string
	 */
	public function get_zotero_library_id() {
		return get_post_meta( $this->get_post_id(), 'zotero_library_id', true );
	}

	/**
	 * Sets the Zotero library ID for an item.
	 *
	 * @param string $zotero_library_id
	 * @return void
	 */
	public function set_zotero_library_id( $zotero_library_id ) {
		update_post_meta( $this->get_post_id(), 'zotero_library_id', $zotero_library_id );
	}

	/**
	 * Gets the Zotero library object for an item.
	 *
	 * @return \SSRC\RAMP\Zotero\Library|null
	 */
	public function get_zotero_library() {
		$library_id = $this->get_zotero_library_id();
		if ( ! $library_id ) {
			return null;
		}

		$library = ZoteroLibrary::get_instance_from_library_id( $library_id );

		return $library;
	}
}
