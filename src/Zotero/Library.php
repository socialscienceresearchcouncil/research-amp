<?php

namespace SSRC\RAMP\Zotero;

class Library {
	protected $data = [
		'id'                => null,
		'zotero_library_id' => '',
		'zotero_api_key'    => '',
	];

	protected static $post_type = 'ramp_zotero_library';

	/**
	 * Gets the ID of a library.
	 *
	 * @return int
	 */
	public function get_id() {
		return (int) $this->data['id'];
	}

	/**
	 * Gets the name of a library.
	 *
	 * @return string
	 */
	public function get_name() {
		return get_the_title( $this->get_id() );
	}

	/**
	 * Gets the URL of a library.
	 *
	 * @return string
	 */
	public function get_url() {
		return sprintf(
			'https://www.zotero.org/%s/items',
			$this->get_zotero_library_id()
		);
	}

	/**
	 * Gets the Zotero ID of the library.
	 *
	 * Of the format groups/1234567 or users/1234567
	 *
	 * @return string
	 */
	public function get_zotero_library_id() {
		return $this->data['zotero_library_id'];
	}

	/**
	 * Gets the API key associated with the library.
	 *
	 * @return string
	 */
	public function get_zotero_api_key() {
		return $this->data['zotero_api_key'];
	}

	/**
	 * Gets the timestamp of the last ingest for the library.
	 *
	 * @return int
	 */
	public function get_last_ingest_timestamp() {
		return (int) get_post_meta( $this->get_id(), 'ramp_last_zotero_ingest', true );
	}

	/**
	 * Gets next scheduled 'ingest' event timestamp.
	 *
	 * @return int
	 */
	public function get_next_scheduled_ingest_event() {
		return wp_next_scheduled( $this->get_ingest_cron_hook_name() );
	}

	/**
	 * Gets the 'ingest' cron hook name.
	 *
	 * @return string
	 */
	public function get_ingest_cron_hook_name() {
		return 'ramp_ingest_zotero_library_' . $this->get_id();
	}

	/**
	 * Gets next scheduled 'ingest full' event timestamp.
	 *
	 * @return int
	 */
	public function get_next_scheduled_ingest_full_event() {
		return wp_next_scheduled( $this->get_ingest_full_cron_hook_name() );
	}

	/**
	 * Gets the 'ingest full' cron hook name.
	 *
	 * @return string
	 */
	public function get_ingest_full_cron_hook_name() {
		return 'ramp_ingest_full_zotero_library_' . $this->get_id();
	}

	/**
	 * Sets the timestamp of the last ingest for the library.
	 *
	 * @param int $timestamp Optional. Defaults to time().
	 */
	public function set_last_ingest_timestamp( $timestamp = 0 ) {
		if ( ! $timestamp ) {
			$timestamp = time();
		}

		update_post_meta( $this->get_id(), 'ramp_last_zotero_ingest', $timestamp );
	}

	/**
	 * Get a Library instance from the WP post ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id
	 * @return SSRC\RAMP\Zotero\Library
	 */
	public static function get_instance_from_id( $id ) {
		$post = get_post( $id );

		if ( ! $post || self::$post_type !== $post->post_type ) {
			return null;
		}

		$instance = new self();

		$instance->data['id'] = $id;

		$instance->data['zotero_library_id'] = get_post_meta( $id, 'zotero_library_id', true );
		$instance->data['zotero_api_key']    = get_post_meta( $id, 'zotero_api_key', true );

		return $instance;
	}

	/**
	 * Get a Library instance from the library ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $library_id
	 * @return SSRC\RAMP\Zotero\Library
	 */
	public static function get_instance_from_library_id( $library_id ) {
		$posts = get_posts(
			[
				'post_type'      => self::$post_type,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'   => 'zotero_library_id',
						'value' => $library_id,
					],
				],
			]
		);

		if ( ! $posts ) {
			return null;
		}

		return self::get_instance_from_id( $posts[0] );
	}

	/**
	 * Get all Libraries.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_libraries() {
		$posts = get_posts(
			[
				'post_type'      => self::$post_type,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			]
		);

		if ( ! $posts ) {
			return [];
		}

		$libraries = array_map(
			function( $id ) {
				return self::get_instance_from_id( $id );
			},
			$posts
		);

		return array_filter( $libraries );
	}
}
