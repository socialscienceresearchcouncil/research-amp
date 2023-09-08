<?php
/**
 * Client class for interacting with the Zotero API.
 *
 * @package SSRC\RAMP
 */

namespace SSRC\RAMP\Zotero;

/**
 * Client class for interacting with the Zotero API.
 *
 * @since 1.0.0
 */
class Client {
	/**
	 * Base URL for the Zotero API.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $base = 'https://api.zotero.org';

	/**
	 * Data for the client.
	 *
	 * @since 1.0.0
	 * @var array
	 */
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
	 * @return void
	 */
	public function __construct( $library_id, $api_key ) {
		$this->data['library_id'] = $library_id;
		$this->data['api_key']    = $api_key;
	}

	/**
	 * Gets the API key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_api_key() {
		return $this->data['api_key'];
	}

	/**
	 * Gets the library ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_library_id() {
		return $this->data['library_id'];
	}

	/**
	 * Gets a set of default parameters for the wp_remote_* functions.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_remote_defaults() {
		$defaults = [
			'headers' => $this->get_headers(),
			'timeout' => 10,
		];

		/**
		 * Filters the default parameters for the wp_remote_* functions in Zotero\Client.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults
		 */
		return apply_filters( 'ramp_zotero_client_remote_defaults', $defaults );
	}

	/**
	 * Gets the headers for the wp_remote_* functions.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_headers() {
		return [
			'Content-Type'       => 'application/json',
			'Authorization'      => 'Bearer ' . $this->get_api_key(),
			'Zotero-API-Version' => 3,
			'Expect'             => '',
		];
	}

	/**
	 * Gets the permissions belonging to the current API key.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_key_permissions() {
		$url = $this->base . '/keys/' . $this->get_api_key();

		$result = wp_remote_get( $url, $this->get_remote_defaults() );

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return null;
		}

		return json_decode( wp_remote_retrieve_body( $result ) );
	}

	/**
	 * Gets the items belonging to the current library.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments to pass to the API.
	 * @return array
	 */
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

		$result = wp_remote_get( $url, $this->get_remote_defaults() );

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

	/**
	 * Gets a Zotero library record.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item_id ID of the item to get.
	 * @return array
	 */
	public function get_record( $item_id ) {
		$url = $this->base . '/' . $this->get_library_id() . '/items/' . $item_id;

		$result = wp_remote_get( $url, $this->get_remote_defaults() );

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return [];
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return (array) $json->data;
	}

	/**
	 * Updates a Zotero library record.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to update.
	 * @return array
	 */
	public function post_item( $data ) {
		$url = $this->base . '/' . $this->get_library_id() . '/items';

		$post_params = array_merge(
			$this->get_remote_defaults(),
			[
				'body' => wp_json_encode( $data ),
			]
		);

		$result = wp_remote_post( $url, $post_params );

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

	/**
	 * Gets a list of the collections belonging to a Zotero library.
	 *
	 * @since 1.0.0
	 *
	 * @return array|null
	 */
	public function get_collections() {
		$url = $this->base . '/' . $this->get_library_id() . '/collections/';

		$result = wp_remote_get( $url, $this->get_remote_defaults() );

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return null;
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return $json;
	}

	/**
	 * Gets a Zotero collection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $collection_id ID of the collection to get.
	 * @return array
	 */
	public function get_collection( $collection_id ) {
		$url = $this->base . '/' . $this->get_library_id() . '/collections/' . $collection_id;

		$result = wp_remote_get( $url, $this->get_remote_defaults() );

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			return [];
		}

		$json = json_decode( wp_remote_retrieve_body( $result ) );
		return (array) $json->data;
	}

	/**
	 * Creates a Zotero collection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to create the collection with.
	 * @return array
	 */
	public function create_collection( $data ) {
		$url = $this->base . '/' . $this->get_library_id() . '/collections';

		$post_params = array_merge(
			$this->get_remote_defaults(),
			[
				'body' => wp_json_encode( $data ),
			]
		);

		$result = wp_remote_post( $url, $post_params );

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

	/**
	 * Updates a Zotero collection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $collection_id ID of the collection to update.
	 * @param array $data Data to update the collection with.
	 * @return array
	 */
	public function update_collection( $collection_id, $data ) {
		$url = $this->base . '/' . $this->get_library_id() . '/collections/' . $collection_id;

		$put_params = array_merge(
			$this->get_remote_defaults(),
			[
				'method' => 'PUT',
				'body'   => wp_json_encode( $data ),
			]
		);

		$result = wp_remote_request( $url, $put_params );

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
