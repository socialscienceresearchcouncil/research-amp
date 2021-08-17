<?php

/*
Plugin Name: Disinfo
Description: Custom functionality for Disinformation Network
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DISINFO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DISINFO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DISINFO_VER', '1.1' );

require __DIR__ . '/autoload.php';

/**
 * Shorthand function to fetch main plugin instance.
 */
function disinfo_app() {
	static $instance;

	if ( empty( $instance ) ) {
		$schema = new \SSRC\Disinfo\Schema();

		$pressforward = new \SSRC\Disinfo\PressForward();
		$admin = new \SSRC\Disinfo\Admin( $pressforward );

		$api = new \SSRC\Disinfo\API();
		$citation_library = new \SSRC\Disinfo\CitationLibrary();
		$the_events_calendar = new \SSRC\Disinfo\TheEventsCalendar();
		$router = new \SSRC\Disinfo\Router();
		$user_management = new \SSRC\Disinfo\UserManagement();

		$app = new \SSRC\Disinfo\App(
			$schema,
			$admin,
			$api,
			$citation_library,
			$the_events_calendar,
			$router,
			$user_management
		);

		$app->init();

		$instance = $app;
	}

	return $instance;
}
add_action( 'plugins_loaded', 'disinfo_app' );
