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

	public function __construct(
		Schema $schema,
		Admin $admin,
		API $api,
		CitationLibrary $citation_library,
		TheEventsCalendar $the_events_calendar,
		Router $router,
		UserManagement $user_management,
		Blocks $blocks
	) {
		$this->schema              = $schema;
		$this->admin               = $admin;
		$this->api                 = $api;
		$this->citation_library    = $citation_library;
		$this->the_events_calendar = $the_events_calendar;
		$this->router              = $router;
		$this->user_management     = $user_management;
		$this->blocks              = $blocks;
	}

	public function init() {
		$this->schema->init();
		$this->api->init();
		$this->citation_library->init();
		$this->the_events_calendar->init();
		$this->router->init();
		$this->user_management->init();
		$this->blocks->init();

		if ( is_admin() ) {
			$this->admin->init();
		}

		require RAMP_PLUGIN_DIR . '/inc/functions.php';
	}

	public function get_cpttax_map( $key ) {
		return $this->schema->get_cpttax_map( $key );
	}
}
