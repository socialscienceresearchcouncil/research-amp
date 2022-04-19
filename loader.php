<?php

/*
Plugin Name: Research Area Mapping Platform (RAMP)
Author: Social Science Research Council
Version: 1.0.0-alpha
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RAMP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RAMP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RAMP_VER', '1.0-alpha' );

require __DIR__ . '/autoload.php';

/**
 * Shorthand function to fetch main plugin instance.
 *
 * @since 1.0.0
 */
function ramp_app() {
	static $instance;

	if ( empty( $instance ) ) {
		$schema = new \SSRC\RAMP\Schema();

		$pressforward = new \SSRC\RAMP\PressForward();
		$admin        = new \SSRC\RAMP\Admin( $pressforward );

		$api                 = new \SSRC\RAMP\API();
		$citation_library    = new \SSRC\RAMP\CitationLibrary();
		$the_events_calendar = new \SSRC\RAMP\TheEventsCalendar();
		$user_management     = new \SSRC\RAMP\UserManagement();
		$blocks              = new \SSRC\RAMP\Blocks();
		$homepage_slides     = new \SSRC\RAMP\HomepageSlides();
		$toc                 = new \SSRC\RAMP\TOC();
		$search              = new \SSRC\RAMP\Search();

		$app = new \SSRC\RAMP\App(
			$schema,
			$admin,
			$api,
			$citation_library,
			$the_events_calendar,
			$user_management,
			$blocks,
			$homepage_slides,
			$toc,
			$search
		);

		$app->init();

		$instance = $app;
	}

	return $instance;
}
add_action( 'plugins_loaded', 'ramp_app' );
