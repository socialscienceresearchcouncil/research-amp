<?php

namespace SSRC\RAMP\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Request;
use \WP_REST_Server;

use SSRC\RAMP\Zotero\Library;

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

		register_rest_route(
			$namespace,
			'/zotero-library/(?P<library_id>\d+)',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
					'args'                => [
						'library_id' => [
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						],
					],
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
		$library_id = $request->get_param( 'library_id' );

		$now = time();

		$retval = [
			'lastIngest'         => '',
			'lastIngestRelative' => '',
			'nextIngest'         => '',
			'nextIngestRelative' => '',
			'nextIngestFull' => '',
		];

		if ( $library_id ) {
			$library = Library::get_instance_from_id( $library_id );

			$next_ingest = $library->get_next_scheduled_ingest_event();
			if ( $next_ingest ) {
				$retval['nextIngest'] = date( 'Y-m-d H:i:s', $next_ingest );

				$retval['nextIngestRelative'] = sprintf(
					/* translators: %s: Human-readable time difference */
					__( '%s from now', 'ramp' ),
					human_time_diff( $next_ingest )
				);
			}

			$next_ingest_full = $library->get_next_scheduled_ingest_full_event();
			if ( $next_ingest ) {
				$retval['nextIngestFull'] = date( 'Y-m-d H:i:s', $next_ingest_full );
			}

			$last_ingest = $library->get_last_ingest_timestamp();
			if ( $last_ingest ) {
				$retval['lastIngest'] = date( 'Y-m-d H:i:s', $last_ingest );

				$retval['lastIngestRelative'] = sprintf(
					/* translators: %s: Human-readable time difference */
					__( '%s ago', 'ramp' ),
					human_time_diff( $last_ingest )
				);
			}
		}

		return rest_ensure_response( $retval );
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
			'success' => false,
		];

		$library_id = $request->get_param( 'libraryId' );
		$action     = $request->get_param( 'action' );

		switch ( $action ) {
			case 'sync' :
				do_action( 'ramp_ingest_zotero_library-' . $library_id );
				do_action( 'ramp_ingest_full_zotero_library-' . $library_id );
			break;
		}

		return rest_ensure_response( $retval );
	}
}
