<?php

namespace SSRC\RAMP;

use SSRC\RAMP\Zotero\Client;
use \WP_Query;

class Citation {
	protected $data = [
		'post_id' => null,
	];

	public function __construct( $post_id = null ) {
		if ( $post_id ) {
			$this->data['post_id'] = (int) $post_id;
		}
	}

	public static function get_from_post_id( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'ramp_citation' !== $post->post_type ) {
			return null;
		}

		return new self( $post_id );
	}

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

		$client = new Client();

		$zotero_id = $this->get_zotero_id();
		$data      = $client->get_record( $zotero_id );

		if ( isset( $data['data'] ) ) {
			$data = (array) $data['data'];
		}

		$this->set_cached_zotero_data( $data );

		return $data;
	}

	public function get_zotero_id() {
		return get_post_meta( $this->get_post_id(), 'zotero_id', true );
	}

	/**
	 * Gets a Zotero URL for an item, as situated under a Collection.
	 */
	public function get_zotero_url() {
		// @todo This will need adjustment for multiple libraries.
		$library_url = 'https://www.zotero.org/groups/' . RAMP_ZOTERO_GROUP_ID . '/items/';

		$collection_ids = $this->get_collections_for_zotero();
		if ( $collection_ids ) {
			$zotero_url = $library_url . 'collectionKey/' . reset( $collection_ids ) . '/itemKey/' . $this->get_zotero_id();
		} else {
			$zotero_url = $library_url . 'itemKey/' . $this->get_zotero_id();
		}

		return $zotero_url;
	}

	public function get_preview_url() {
		return get_permalink( $this->get_post_id() );
	}

	public function get_source_url() {
		$data = $this->get_zotero_data();

		if ( isset( $data['url'] ) ) {
			return $data['url'];
		}

		return '';
	}

	public function get_publication_year() {
		$data = $this->get_zotero_data();

		$year = '';
		if ( ! empty( $data['date'] ) ) {
			// Four-digit numbers are years, and shouldn't be put through strtotime().
			if ( preg_match( '/^[0-9]{4}$/', $data['date'] ) ) {
				$year = $data['date'];

				// Here's another helpful citation format from Zotero.
			} elseif ( preg_match( '/^[0-9]{4}\/[0-9]{2}$/', $data['date'] ) ) {
				$year = substr( $data['date'], 0, 4 );
			} else {
				$year = gmdate( 'Y', strtotime( $data['date'] ) );
			}
		}

		return $year;
	}

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

				$author_names[] = sprintf(
					'%s, %s',
					$creator->lastName,
					$creator->firstName
				);
				// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			}
		}

		return $author_names;
	}

	public function set_post_id( $post_id ) {
		$this->data['post_id'] = (int) $post_id;
	}

	public function set_zotero_id( $zotero_id ) {
		update_post_meta( $this->get_post_id(), 'zotero_id', $zotero_id );
	}

	public function set_cached_zotero_data( $data ) {
		update_post_meta( $this->get_post_id(), 'zotero_data', $data );
	}

	public function delete_cached_zotero_data() {
		delete_post_meta( $this->get_post_id(), 'zotero_data' );
	}

	public function get_post_id() {
		return (int) $this->data['post_id'];
	}

	/**
	 * Get title as stored in WordPress.
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
	 */
	public function set_abstract( $abstract ) {
		return wp_update_post(
			[
				'ID'           => $this->get_post_id(),
				'post_content' => $abstract,
			]
		);
	}

	/**
	 * Get the Zotero Collection IDs belonging to an item, based on its WP Research Topics.
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
	 * Sets the Zotero group ID for an item.
	 *
	 * @param string $zotero_group_id
	 */
	public function set_zotero_group_id( $zotero_group_id ) {
		update_post_meta( $this->get_post_id(), 'zotero_group_id', $zotero_group_id );
	}
}
