<?php

namespace SSRC\RAMP;

class App {
	protected $schema;
	protected $admin;
	protected $api;
	protected $citation_library;
	protected $the_events_calendar;
	protected $user_management;
	protected $blocks;
	protected $homepage_slides;
	protected $toc;
	protected $search;

	protected $cli;

	public function __construct(
		Schema $schema,
		Admin $admin,
		API $api,
		CitationLibrary $citation_library,
		TheEventsCalendar $the_events_calendar,
		UserManagement $user_management,
		Blocks $blocks,
		HomepageSlides $homepage_slides,
		TOC $toc,
		Search $search
	) {
		$this->schema              = $schema;
		$this->admin               = $admin;
		$this->api                 = $api;
		$this->citation_library    = $citation_library;
		$this->the_events_calendar = $the_events_calendar;
		$this->user_management     = $user_management;
		$this->blocks              = $blocks;
		$this->homepage_slides     = $homepage_slides;
		$this->toc                 = $toc;
		$this->search              = $search;
	}

	public function init() {
		$this->schema->init();
		$this->api->init();
		$this->citation_library->init();
		$this->user_management->init();
		$this->blocks->init();
		$this->homepage_slides->init();
		$this->toc->init();
		$this->search->init();

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

		// Must be loaded early in order to register 'block-templates' support.
		$this->register_theme_directory();

		add_action( 'wp', [ $this, 'maybe_install_update' ] );
	}

	public function get_cpttax_map( $key ) {
		return $this->schema->get_cpttax_map( $key );
	}

	public function maybe_install_update() {
		$version = get_option( 'ramp_version' );

		// @todo Updates
		if ( $version ) {
			return;
		}

		$installer = new Install();
		$installer->install();
	}

	/**
	 * Registers theme directory.
	 *
	 * @since 1.0.0
	 */
	public function register_theme_directory() {
		register_theme_directory( RAMP_PLUGIN_DIR . '/themes' );
	}
}
