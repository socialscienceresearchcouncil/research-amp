<?php

namespace SSRC\Disinfo;

use \Tribe__Events__Main;

class TheEventsCalendar {
	public function init() {
		if ( ! defined( 'TRIBE_EVENTS_FILE' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'register_taxonomies_for_events' ], 30 );
	}

	public function register_taxonomies_for_events() {
		register_taxonomy_for_object_type( 'ssrc_scholar_profile', Tribe__Events__Main::POSTTYPE );
		register_taxonomy_for_object_type( 'ssrc_research_topic', Tribe__Events__Main::POSTTYPE );
		register_taxonomy_for_object_type( 'ssrc_focus_tag', Tribe__Events__Main::POSTTYPE );
	}
}
