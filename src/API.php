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
					$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );
					return $rt_map->get_term_id_for_post_id( $object['id'] );
				},
			]
		);

		register_rest_field(
			[ 'ramp_topic', 'ramp_review', 'ramp_article', 'ramp_news_item' ],
			'formatted_date',
			[
				'get_callback' => function( $object ) {
					return get_the_date( '', $object['id'] );
				},
			]
		);

		register_rest_field(
			[ 'ramp_topic', 'ramp_review', 'ramp_article', 'ramp_news_item' ],
			'formatted_citation',
			[
				'get_callback' => function( $object ) {
					return get_post_meta( $object['id'], 'formatted_citation', true );
				},
			]
		);

		register_rest_field(
			'ramp_profile',
			'alphabetical_name',
			[
				'get_callback'    => function( $object ) {
					return get_post_meta( $object['id'], 'alphabetical_name', true );
				},
				'update_callback' => function( $value, $object ) {
					update_post_meta( $object->ID, 'alphabetical_name', $value );
				},
			]
		);
	}
}

