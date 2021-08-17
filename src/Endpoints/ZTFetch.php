<?php

namespace SSRC\Disinfo\Endpoints;

/**
 * Zotero Translation fetch.
 */
use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;
use \WP_Error;

use \SSRC\Disinfo\Zotero\TranslationFetcher;
use \SSRC\Disinfo\Zotero\Client;

class ZTFetch extends WP_REST_Controller {
	public function register_routes() {
		$version = '1';
		$namespace = 'disinfo/v' . $version;

		register_rest_route(
			$namespace,
			'/ztfetch',
			[
				[
					'methods' => WP_REST_Server::CREATABLE,
					'callback' => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'create_item_permissions_check' ],
				]
			]
		);
	}

	public function create_item_permissions_check( $request ) {
		$key     = $request->get_param( 'key' );
		$compare = wp_hash( 'ztfetch' );

		return $key === $compare;
	}

	public function create_item( $request ) {
		$citation_id = $request->get_param( 'citationId' );

		$citation = get_post( $citation_id );
		if ( ! $citation || 'ssrc_citation' !== $citation->post_type ) {
			return new WP_Error( 'citation_not_found', 'No citation found by that ID', 500 );
		}

		$item_link = pressforward( 'controller.metas' )->get_post_pf_meta( $citation_id, 'item_link', true );

		$z = new TranslationFetcher();

		// @todo abstract
		$z->set_server_url( 'https://s9jtw4iduk.execute-api.us-east-2.amazonaws.com/Prod/web' );

		$translation = $z->fetch( $item_link );

		if ( $translation ) {
			$zt_data = reset( $translation );
			update_post_meta( $citation_id, 'zt_data', array( $zt_data ) );
		}

		return rest_ensure_response( $translation );
	}
}
