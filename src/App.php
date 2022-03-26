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
		HomepageSlides $homepage_slides
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
	}

	public function init() {
		$this->schema->init();
		$this->api->init();
		$this->citation_library->init();
		$this->the_events_calendar->init();
		$this->router->init();
		$this->user_management->init();
		$this->blocks->init();
		$this->homepage_slides->init();

		if ( is_admin() ) {
			$this->admin->init();
		}

		if ( defined( 'WP_CLI' ) ) {
			$this->cli = new CLI();
			$this->cli->init();
		}

		require RAMP_PLUGIN_DIR . '/inc/functions.php';

		add_action( 'wp_enqueue_scripts', [ $this, 'register_global_assets' ], 5 );
	}

	public function get_cpttax_map( $key ) {
		return $this->schema->get_cpttax_map( $key );
	}

	public function register_global_assets() {
		wp_register_script(
			'ramp-select2',
			RAMP_PLUGIN_URL . '/lib/select2/select2.min.js',
			[ 'jquery' ],
			RAMP_VER,
			true
		);

		wp_register_style(
			'ramp-select2',
			RAMP_PLUGIN_URL . '/lib/select2/select2.min.css',
			[],
			RAMP_VER
		);
	}
}
