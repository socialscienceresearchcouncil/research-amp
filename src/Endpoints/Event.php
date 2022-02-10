<?php

namespace SSRC\RAMP\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;

class Event extends WP_REST_Controller {
	public function register_routes() {
		$version   = '1';
		$namespace = 'ramp/v' . $version;

		register_rest_route(
			$namespace,
			'/event',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'create_item_permissions_check' ],
				],
			]
		);
	}

	public function create_item_permissions_check( $request ) {
		return current_user_can( 'edit_others_posts' );
	}

	public function create_item( $request ) {
		$article_id = $request->get_param( 'articleId' );

		$article = get_post( $article_id, ARRAY_A );

		unset( $article['ID'] );
		$article['post_status'] = 'draft';

		// @todo Copy relevant tax terms.
		$event_id = tribe_create_event( $article );

		update_post_meta( $article_id, 'event_id', $event_id );

		return rest_ensure_response( $event_id );
	}
}
