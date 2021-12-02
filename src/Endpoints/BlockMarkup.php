<?php

namespace SSRC\RAMP\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;

class BlockMarkup extends WP_REST_Controller {
	public function register_routes() {
		$version   = '1';
		$namespace = 'ramp/v' . $version;

		register_rest_route(
			$namespace,
			'/block-markup',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
			]
		);
	}

	/**
	 * Permission check for getting library info.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Fetches an item.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function get_item( $request ) {
		$block_type = $request->get_param( 'blockType' );

		$retval = 'Hey there';

		return rest_ensure_response( $retval );
	}

}
