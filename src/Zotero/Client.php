<?php

namespace SSRC\RAMP\Zotero;

class Client {
	protected $base = 'https://api.zotero.org';
	protected $data = [
		'library_id' => '',
		'api_key'    => '',
	];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $library_id
	 * @param string $apk_key
	 */
	public function __construct( $library_id, $api_key ) {
		$this->data['library_id'] = $library_id;
		$this->data['api_key']    = $api_key;
	}

	protected function get_api_key() {
		return $this->data['api_key'];
	}

	protected function get_library_id() {
		return $this->data['library_id'];
	}

	protected function get_headers() {
		return [
			'Content-Type'       => 'application/json',
			'Authorization'      => 'Bearer ' . $this->get_api_key(),
			'Zotero-API-Version' => 3,
			'Expect'             => '',
		];
	}

	public function get_key_permissions() {
		$url = $this->base . '/keys/' . $this->get_api_key();

		$result = wp_remote_get(
			$url,
			[
				'headers' => $this->get_headers(),
				'timeout' => 10,
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return null;
		}

		return wp_remote_retrieve_body( $result );
	}

	public function get_items( $args = [] ) {
		$defaults = [
			'sort'      => 'dateAdded',
			'limit'     => 5,
			'direction' => 'desc',
			'start'     => 0,
		];

		$query_args = [];
		foreach ( $defaults as $dkey => $dvalue ) {
			$query_args[ $dkey ] = ! empty( $args[ $dkey ] ) ? $args[ $dkey ] : $dvalue;
		}

		$url = $this->base . '/' . $this->get_library_id() . '/items/';
		$url = add_query_arg( $query_args, $url );

		$result = wp_remote_get(
			$url,
			[
				'headers' => $this->get_headers(),
				'timeout' => 10,
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::log( $response_code );
			}
			return false;
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return (array) $json;
	}

	public function get_record( $item_id ) {
		$url = $this->base . '/' . $this->get_library_id() . '/items/' . $item_id;

		$result = wp_remote_get(
			$url,
			[
				'headers' => $this->get_headers(),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return [];
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return (array) $json->data;
	}

	public function post_item( $data ) {
		$url = $this->base . '/' . $this->get_library_id() . '/items';

		$result = wp_remote_post(
			$url,
			[
				'headers' => $this->get_headers(),
				'body'    => wp_json_encode( $data ),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return null;
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		if ( empty( $json->success ) ) {
			return null;
		}

		$success = (array) $json->successful;
		$record  = reset( $success );
		return (array) $record;
	}

	public function get_collections() {
		$url = $this->base . '/' . $this->get_library_id() . '/collections/';

		$result = wp_remote_get(
			$url,
			[
				'headers' => $this->get_headers(),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return [];
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return $json;
	}

	public function get_collection( $collection_id ) {
		$url = $this->base . '/' . $this->get_library_id() . '/collections/' . $collection_id;

		$result = wp_remote_get(
			$url,
			[
				'headers' => $this->get_headers(),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return [];
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return (array) $json->data;
	}

	public function create_collection( $data ) {
		$url = $this->base . '/' . $this->get_library_id() . '/collections';

		$result = wp_remote_post(
			$url,
			[
				'headers' => $this->get_headers(),
				'body'    => wp_json_encode( [ 0 => $data ] ),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return null;
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		if ( empty( $json->success ) ) {
			return null;
		}

		$success       = (array) $json->success;
		$collection_id = reset( $success );
		return $collection_id;
	}

	public function update_collection( $collection_id, $data ) {
		$url = $this->base . '/' . $this->get_library_id() . '/collections/' . $collection_id;

		$result = wp_remote_request(
			$url,
			[
				'method'  => 'PUT',
				'headers' => $this->get_headers(),
				'body'    => wp_json_encode( $data ),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );

		// There seems to be a bug in Zotero that causes a 204 to return on a successful update.
		// So we bail here.
		return;

		// phpcs:disable Squiz.PHP.NonExecutableCode.Unreachable
		if ( 200 !== $response_code ) {
			return null;
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		if ( empty( $json->success ) ) {
			return null;
		}

		$success       = (array) $json->success;
		$collection_id = reset( $success );
		return $collection_id;
		// phpcs:enable Squiz.PHP.NonExecutableCode.Unreachable
	}
}
