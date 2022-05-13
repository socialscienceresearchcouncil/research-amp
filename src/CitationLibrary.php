<?php

namespace SSRC\RAMP;

use SSRC\RAMP\Citation;
use SSRC\RAMP\Zotero\Client;
use SSRC\RAMP\Zotero\Library as ZoteroLibrary;

use \WP_Query;

class CitationLibrary {
	public function init() {
		$libraries = ZoteroLibrary::get_libraries();

		add_action( 'save_post_ramp_zotero_library', [ $this, 'maybe_schedule_ingest_events' ], 10, 3 );

		foreach ( $libraries as $library ) {
			add_action( $library->get_ingest_cron_hook_name(), [ $this, 'start_ingest' ] );
			add_action( $library->get_ingest_full_cron_hook_name(), [ $this, 'start_ingest_full' ] );
		}
	}

	/**
	 * Schedules 'ingest' cron events for a Zotero library, if necessary.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 * @param bool    $update
	 */
	public function maybe_schedule_ingest_events( $post_id, $post, $update ) {
		$library = ZoteroLibrary::get_instance_from_id( $post_id );

		$next_ingest = $library->get_next_scheduled_ingest_event();
		if ( ! $next_ingest ) {
			wp_schedule_event(
				time(),
				'hourly',
				$library->get_ingest_cron_hook_name()
			);
		}

		$next_ingest_full = $library->get_next_scheduled_ingest_full_event();
		if ( ! $next_ingest_full ) {
			wp_schedule_event(
				time(),
				'weekly',
				$library->get_ingest_full_cron_hook_name()
			);
		}
	}

	public static function get_post_id_from_zotero_id( $zotero_id, $zotero_library_id ) {
		$existing = new WP_Query(
			[
				'post_type'      => 'ramp_citation',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'meta_query'     => [
					[
						'key'   => 'zotero_id',
						'value' => $zotero_id,
					],
					[
						'key'   => 'zotero_library_id',
						'value' => $zotero_library_id,
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

	// @todo This will eventually support collection searches (Research Topic filter)
	public function get_recently_added_items() {
		$cached = get_transient( 'zotero_recent_items' );
		if ( false !== $cached ) {
			return $cached;
		}

		$client    = new Client();
		$items_raw = $client->get_items();

		if ( false === $items_raw ) {
			// This is an error.
			return [];
		}

		// Load individual post cache and make any necessary updates based on fresh data.
		foreach ( $items_raw as $item ) {
			$citation_id = self::get_post_id_from_zotero_id( $item->key );
			if ( $citation_id ) {
				$this->update_existing_citation( $citation_id, $item );
			} else {
				$this->create_new_citation( $item );
			}
		}

		$items = wp_list_pluck( $items_raw, 'data' );

		// Hardcoding 1 hour. We could discuss whether there needs to be manua flush.
		set_transient( 'zotero_recent_items', $items, 1 * HOUR_IN_SECONDS );

		return $items;
	}

	public function get_recently_added_items_local( $args = [] ) {
		$query_args = [
			'paged'          => 1,
			'post_type'      => 'ramp_citation',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'DESC',
		];

		$r = array_merge( $query_args, $args );

		$found = new WP_Query( $r );

		// Holy hack.
		set_query_var( 'citation_has_more_pages', $found->found_posts > ( $r['paged'] * $r['posts_per_page'] ) );

		$retval = [];
		foreach ( $found->posts as $post_id ) {
			$citation = new Citation( $post_id );
			$retval[] = $citation->get_zotero_data();
		}

		return $retval;
	}

	// @todo Set up cron job.
	/**
	 * Ingest from Zotero.
	 *
	 * @param SSRC\RAMP\Zotero\Library $library
	 * @param bool                     $update_existing
	 * @param int                      $since
	 */
	public function ingest( ZoteroLibrary $library, $update_existing = true, $since = null ) {
		$client = new Client( $library->get_zotero_library_id(), $library->get_zotero_api_key() );

		$chunk_size = 100;
		$start      = 0;

		$default_args = [
			'limit' => $chunk_size,
			'start' => $start,
			'sort'  => 'dateModified',
		];

		if ( null === $since ) {
			$since = $library->get_last_ingest_timestamp();
		}

		// There is a more elegant way but I'm not going to find it today.
		$fetch_more   = false;
		$batch_start  = $start;
		$add_queue    = [];
		$keys_fetched = [];
		do {
			$fetch_more          = false;
			$query_args          = $default_args;
			$query_args['start'] = $batch_start;
			$items               = $client->get_items( $query_args );

			if ( defined( 'WP_CLI' ) ) {
				$item_count = count( $items );
				\WP_CLI::log( 'Fetched ' . $item_count . ' from Zotero using "start" value of ' . $batch_start );
			}

			// This will happen when we reach the end of the library.
			if ( [] === $items ) {
				break;
			}

			foreach ( $items as $item ) {
				$the_key = $item->key;

				// Stop once we hit a single item that's older than 'since'.
				$item_timestamp = strtotime( $item->data->dateModified );
				if ( $item_timestamp < $since ) {
					break 2;
				}

				$add_queue[] = $item;
			}

			$fetch_more   = true;
			$batch_start += $chunk_size;
		} while ( $fetch_more );

		// Nothing to do.
		if ( ! $add_queue ) {
			return;
		}

		$existing_keys = [];
		$create_keys   = [];
		foreach ( $add_queue as $queued_item ) {
			// Skip 'attachments'.
			if ( 'attachment' === $queued_item->data->itemType ) {
				continue;
			}

			// Avoiding doing a bulk lookup for now - may not scale with large imports.
			$existing = self::get_post_id_from_zotero_id( $queued_item->key, $library->get_zotero_library_id() );
			if ( $existing ) {
				$existing_keys[] = $queued_item->key;
				if ( $update_existing ) {
					$this->update_existing_citation( $existing, $queued_item, $library->get_zotero_library_id() );
				}
			} else {
				$create_keys[] = $queued_item->key;
				$this->create_new_citation( $queued_item, $library->get_zotero_library_id() );
			}
		}

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::log( 'Existing: ' . count( $existing_keys ) );
			\WP_CLI::log( 'Created: ' . count( $create_keys ) );
		}
	}

	/**
	 * Cron callback for regular ingest.
	 *
	 * Run regularly, this will look for items that have been created in the Zotero library
	 * since the last ingest event.
	 */
	public function start_ingest() {
		// Identify the currently processed library based on the hook name.
		preg_match_all( '/ramp_ingest_zotero_library_([0-9]+)/', current_action(), $matches );
		if ( empty( $matches[1] ) ) {
			return;
		}

		$library_id = (int) $matches[1][0];
		$library    = ZoteroLibrary::get_instance_from_id( $library_id );

		$this->ingest( $library );

		$library->set_last_ingest_timestamp();
	}

	/**
	 * Cron callback for "full" ingest.
	 *
	 * The purpose of this occasional routine is to find missing items through the entire Zotero
	 * library. It will not update existing items.
	 */
	public function start_ingest_full() {
		// Identify the currently processed library based on the hook name.
		preg_match_all( '/ramp_ingest_full_zotero_library_([0-9]+)/', current_action(), $matches );
		if ( empty( $matches[1] ) ) {
			return;
		}

		$library_id = (int) $matches[1][0];
		$library    = ZoteroLibrary::get_instance_from_id( $library_id );

		$this->ingest( $library, false, 0 );
	}

	protected function update_existing_citation( $citation_id, $zotero_item, $zotero_library_id ) {
		// This is probably a Zotero meta item.
		if ( empty( $zotero_item->data->collections ) ) {
			return false;
		}

		// Update the WP post if one of the following has changed:
		// - title
		// - tags
		// - abstract
		// - Research Topic (collections)
		$citation = Citation::get_from_post_id( $citation_id );

		// If the item is changed in Zotero, trigger a change here.
		$citation->delete_cached_zotero_data();

		if ( $zotero_item->data->title !== $citation->get_title() ) {
			$citation->set_title( $zotero_item->data->title );
		}

		if ( $zotero_item->data->abstractNote !== $citation->get_abstract() ) {
			$citation->set_abstract( $zotero_item->data->abstractNote );
		}

		$item_collections = $citation->get_collections_for_zotero();
		if ( $zotero_item->data->collections !== $item_collections ) {
			$citation->set_research_topics_from_collection_ids( $zotero_item->data->collections );
		}

		$item_tags = $citation->get_tags_for_zotero();
		if ( $zotero_item->data->tags !== $item_tags ) {
			$citation->set_focus_tags_from_tags( $zotero_item->data->tags );
		}

		$citation->set_zotero_library_id( $zotero_library_id );
	}

	protected function create_new_citation( $zotero_item, $zotero_library_id ) {
		// This is probably a Zotero meta item.
		if ( empty( $zotero_item->data->collections ) ) {
			return false;
		}

		$post_content = ! empty( $zotero_item->data->abstractNote ) ? $zotero_item->data->abstractNote : '';

		$post_data = [
			'post_type'     => 'ramp_citation',
			'post_status'   => 'publish',
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( $zotero_item->data->dateAdded ) ),
			'post_title'    => $zotero_item->data->title,
			'post_content'  => $post_content,
		];

		$post_id = wp_insert_post( $post_data );

		$citation = new Citation( $post_id );

		$citation->set_research_topics_from_collection_ids( $zotero_item->data->collections );
		$citation->set_focus_tags_from_tags( $zotero_item->data->tags );
		$citation->set_zotero_library_id( $zotero_library_id );

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( isset( $zotero_item->data->creators ) && is_array( $zotero_item->data->creators ) ) {
			foreach ( $zotero_item->data->creators as $creator ) {
				if ( isset( $creator->firstName ) && isset( $creator->lastName ) ) {
					$creator_name = sprintf( '%s %s', $creator->firstName, $creator->lastName );
					add_post_meta( $post_id, 'zotero_author', $creator_name );
				}
			}
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		$zotero_data = (array) $zotero_item->data;
		$citation->set_cached_zotero_data( $zotero_data );

		update_post_meta( $post_id, 'zotero_id', $zotero_item->key );
		update_post_meta( $post_id, 'imported_from_zotero', gmdate( 'Y-m-d H:i:s' ) );

		// Manually sync to relevanssi.
		if ( function_exists( 'relevanssi_publish' ) ) {
			relevanssi_publish( $post_id, true );
		}
	}

	public static function get_citation_count() {
		$cache_key = 'citation_count_' . wp_cache_get_last_changed( 'posts' );

		$count = wp_cache_get( $cache_key, 'research-amp' );

		if ( false === $count ) {
			$query = new WP_Query(
				[
					'fields'         => 'ids',
					'post_type'      => 'ramp_citation',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
				]
			);

			$count = $query->found_posts;

			wp_cache_set( $cache_key, $count, 'research-amp' );
		}

		return (int) $count;
	}
}
