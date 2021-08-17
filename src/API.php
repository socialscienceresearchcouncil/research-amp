<?php

namespace SSRC\Disinfo;

class API {
	public $endpoints = [];

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	public function register_endpoints() {
		$class_names = [ 'Citation', 'Event', 'NominationStatus', 'ZTFetch' ];

		foreach ( $class_names as $class_name ) {
			$class_name_with_namespace = '\SSRC\Disinfo\Endpoints\\' . $class_name;
			$this->endpoints[ $class_name ] = new $class_name_with_namespace();
			$this->endpoints[ $class_name ]->register_routes();
		}
	}
}

