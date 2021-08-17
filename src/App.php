<?php

namespace SSRC\Disinfo;

class App {
	protected $schema;
	protected $admin;
	protected $api;
	protected $citation_library;
	protected $the_events_calendar;
	protected $router;
	protected $user_management;

	public function __construct(
		Schema $schema,
		Admin $admin,
		API $api,
		CitationLibrary $citation_library,
		TheEventsCalendar $the_events_calendar,
		Router $router,
		UserManagement $user_management
	) {
		$this->schema = $schema;
		$this->admin = $admin;
		$this->api = $api;
		$this->citation_library = $citation_library;
		$this->the_events_calendar = $the_events_calendar;
		$this->router = $router;
		$this->user_management = $user_management;
	}

	public function init() {
		$this->schema->init();
		$this->api->init();
		$this->citation_library->init();
		$this->the_events_calendar->init();
		$this->router->init();
		$this->user_management->init();

		if ( is_admin() ) {
			$this->admin->init();
		}
	}

	public function get_cpttax_map( $key ) {
		return $this->schema->get_cpttax_map( $key );
	}
}
