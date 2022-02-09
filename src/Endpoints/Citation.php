<?php

namespace SSRC\RAMP\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;

class Citation extends WP_REST_Controller {
	public function register_routes() {
		$version   = '1';
		$namespace = 'disinfo/v' . $version;

		register_rest_route(
			$namespace,
			'/citation',
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
		$article['post_type']   = 'ramp_citation';
		$article['post_status'] = 'draft';

		$citation_id = wp_insert_post( $article );

		update_post_meta( $article_id, 'citation_id', $citation_id );

		// @todo copy relevant tax terms

		$data = [
			'key'        => wp_hash( 'ztfetch' ),
			'citationId' => $citation_id,
		];

		$posted = wp_remote_post(
			home_url( '/wp-json/disinfo/v1/ztfetch' ),
			[
				'body'        => $data,
				'timeout'     => 10,
				'redirection' => 5,
				'blocking'    => false,
			]
		);

		return rest_ensure_response( $citation_id );
	}
}
