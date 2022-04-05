<?php

namespace SSRC\RAMP;

use \Tribe__Events__Main;

class TheEventsCalendar {
	public function init() {
		if ( ! defined( 'TRIBE_EVENTS_FILE' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'register_taxonomies_for_events' ], 30 );

		add_filter( 'tribe_events_editor_default_template', [ $this, 'filter_default_event_template' ] );

		add_filter( 'the_content', [ $this, 'remove_blocks_from_post_content' ], 0 );
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
			function( $block ) use ( $exclude_blocks ) {
				return ! in_array( $block[0], $exclude_blocks, true );
			}
		);

		return $new_template;
	}
}
