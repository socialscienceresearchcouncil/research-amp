<?php

namespace SSRC\RAMP;

class App {
	protected $schema;
	protected $admin;
	protected $api;
	protected $citation_library;
	protected $the_events_calendar;
	protected $router;
	protected $user_management;
	protected $blocks;
	protected $homepage_slides;
	protected $toc;

	protected $cli;

	public function __construct(
		Schema $schema,
		Admin $admin,
		API $api,
		CitationLibrary $citation_library,
		TheEventsCalendar $the_events_calendar,
		Router $router,
		UserManagement $user_management,
		Blocks $blocks,
		HomepageSlides $homepage_slides,
		TOC $toc
	) {
		$this->schema              = $schema;
		$this->admin               = $admin;
		$this->api                 = $api;
		$this->citation_library    = $citation_library;
		$this->the_events_calendar = $the_events_calendar;
		$this->router              = $router;
		$this->user_management     = $user_management;
		$this->blocks              = $blocks;
		$this->homepage_slides     = $homepage_slides;
		$this->toc                 = $toc;
	}

	public function init() {
		$this->schema->init();
		$this->api->init();
		$this->citation_library->init();
		$this->router->init();
		$this->user_management->init();
		$this->blocks->init();
		$this->homepage_slides->init();
		$this->toc->init();

		if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
			$this->the_events_calendar->init();
		}

		if ( is_admin() ) {
			$this->admin->init();
		}

		if ( defined( 'WP_CLI' ) ) {
			$this->cli = new CLI();
			$this->cli->init();
		}

		require RAMP_PLUGIN_DIR . '/inc/functions.php';
	}

	public function get_cpttax_map( $key ) {
		return $this->schema->get_cpttax_map( $key );
	}
}
