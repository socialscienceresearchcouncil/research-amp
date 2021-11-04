<?php

namespace SSRC\RAMP\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;

class ZoteroLibrary extends WP_REST_Controller {
	public function register_routes() {
		$version   = '1';
		$namespace = 'ramp/v' . $version;

		register_rest_route(
			$namespace,
			'/zotero-library',
			[
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'edit_item' ],
					'permission_callback' => [ $this, 'edit_item_permissions_check' ],
				],
			]
		);
	}

	/**
	 * Permission check for editing library info.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function edit_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function edit_item( $request ) {
		$retval = [
			'success' => true,
		];

		$library_id = $request->get_param( 'libraryId' );

		$retval = [
			'success' => 1,
		];

		return rest_ensure_response( $retval );
	}
}
