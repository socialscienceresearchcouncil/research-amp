<?php

namespace SSRC\RAMP\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;

use \WP_Query;

class NominationStatus extends WP_REST_Controller {
	public function register_routes() {
		$version   = '1';
		$namespace = 'research-amp/v' . $version;

		register_rest_route(
			$namespace,
			'/nomination-status',
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
		);
	}

	public function get_items_permissions_check( $request ) {
		return current_user_can( 'edit_others_posts' );
	}

	public function get_items( $request ) {
		$item_ids = $request->get_param( 'itemIds' );
		$item_ids = array_map( 'intval', $item_ids );

		$retval = [
			'has_citation' => [],
			'has_event'    => [],
		];

		if ( $item_ids ) {
			$query = new WP_Query(
				[
					'post_type'              => 'nomination',
					'post__in'               => $item_ids,
					'update_post_term_cache' => false,
					'update_post_meta_cache' => true,
					'posts_per_page'         => -1,
					'post_status'            => 'any',
				]
			);

			foreach ( $query->posts as $post ) {
				$citation_id = get_post_meta( $post->ID, 'citation_id', true );
				if ( $citation_id ) {
					$retval['has_citation'][ $post->ID ] = $citation_id;
				}

				$event_id = get_post_meta( $post->ID, 'event_id', true );
				if ( $event_id ) {
					$retval['has_event'][ $post->ID ] = $event_id;
				}
			}
		}

		return rest_ensure_response( $retval );
	}
}
