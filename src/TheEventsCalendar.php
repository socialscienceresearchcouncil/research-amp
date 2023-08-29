<?php

namespace SSRC\RAMP;

use Tribe__Events__Main;

class TheEventsCalendar {
	public function init() {
		if ( ! defined( 'TRIBE_EVENTS_FILE' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'register_taxonomies_for_events' ], 30 );

		add_filter( 'tribe_events_editor_default_template', [ $this, 'filter_default_event_template' ] );

		add_filter( 'the_content', [ $this, 'remove_blocks_from_post_content' ], 0 );

		add_action( 'admin_init', [ $this, 'check_for_nav_item' ] );
	}

	public function register_taxonomies_for_events() {
		register_taxonomy_for_object_type( 'ramp_assoc_profile', Tribe__Events__Main::POSTTYPE );
		register_taxonomy_for_object_type( 'ramp_assoc_topic', Tribe__Events__Main::POSTTYPE );
		register_taxonomy_for_object_type( 'ramp_focus_tag', Tribe__Events__Main::POSTTYPE );
	}

	/**
	 * Dynamically remove blocks from Event post content on front end.
	 */
	public function remove_blocks_from_post_content( $content ) {
		if ( ! is_singular( 'tribe_events' ) ) {
			return $content;
		}

		$exclude_blocks = [
			'tribe/classic-event-details',
			'tribe/event-datetime',
			'tribe/event-links',
			'tribe/event-venue',
		];

		foreach ( $exclude_blocks as $exclude_block ) {
			$pattern = '|<!-- wp:' . preg_quote( $exclude_block, '|' ) . ' [\/]?/-->|';
			$content = preg_replace( $pattern, '', $content );
		}

		return $content;
	}

	/**
	 * Modify the default template for Events.
	 */
	public function filter_default_event_template( $template ) {
		$exclude_blocks = [ 'tribe/event-links' ];

		$new_template = array_filter(
			$template,
			function ( $block ) use ( $exclude_blocks ) {
				return ! in_array( $block[0], $exclude_blocks, true );
			}
		);

		return $new_template;
	}

	/**
	 * Check for the existence of the "Events" nav item and add it if it doesn't exist.
	 *
	 * Intended to run only once on an installation. If the navigation item is deleted
	 * by the administrator, a flag will be left in the database and the nav item
	 * will not be re-added.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function check_for_nav_item() {
		$added = get_option( 'ramp_events_nav_item_added', false );
		if ( $added ) {
			return;
		}

		$nav_menu_id = Util\Navigation::get_nav_id( 'primary-nav' );
		if ( ! $nav_menu_id ) {
			return;
		}

		$nav_menu = get_post( $nav_menu_id );
		if ( ! $nav_menu ) {
			return;
		}

		$nav_blocks = parse_blocks( $nav_menu->post_content );

		// If the Events archive is already in the menu, we're done.
		$events_archive_url = get_post_type_archive_link( 'tribe_events' );
		foreach ( $nav_blocks as $nav_block ) {
			if ( isset( $nav_block['attrs']['url'] ) && $nav_block['attrs']['url'] === $events_archive_url ) {
				update_option( 'ramp_events_nav_item_added', '1' );
				return;
			}
		}

		$events_block = [
			'blockName'    => 'core/navigation-link',
			'attrs'        => [
				'label'          => __( 'Events', 'research-amp' ),
				'type'           => '',
				'url'            => $events_archive_url,
				'kind'           => 'post-type-archive',
				'isTopLevelItem' => 1,
			],
			'innerBlocks'  => [],
			'innerHTML'    => '',
			'innerContent' => [],
		];

		$nav_blocks[] = $events_block;

		$new_post_content = serialize_blocks( $nav_blocks );

		wp_update_post(
			[
				'ID'           => $nav_menu_id,
				'post_content' => $new_post_content,
			]
		);

		update_option( 'ramp_events_nav_item_added', '1' );
	}
}
