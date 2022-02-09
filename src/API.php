<?php

namespace SSRC\RAMP;

class API {
	public $endpoints = [];

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
		add_action( 'rest_api_init', [ $this, 'register_fields' ] );
	}

	public function register_endpoints() {
		$class_names = [ 'Citation', 'Event', 'NominationStatus', 'ZoteroLibrary', 'ZTFetch' ];

		foreach ( $class_names as $class_name ) {
			$class_name_with_namespace      = '\SSRC\RAMP\Endpoints\\' . $class_name;
			$this->endpoints[ $class_name ] = new $class_name_with_namespace();
			$this->endpoints[ $class_name ]->register_routes();
		}
	}

	/**
	 * Registers additional fields on core WP endpoints.
	 */
	public function register_fields() {
		register_rest_field(
			'ramp_topic',
			'associated_term_id',
			[
				'get_callback' => function( $object ) {
					$rt_map = disinfo_app()->get_cpttax_map( 'research_topic' );
					return $rt_map->get_term_id_for_post_id( $object['id'] );
				},
			]
		);
	}
}

