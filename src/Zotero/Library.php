<?php

namespace SSRC\RAMP\Zotero;

class Library {
	protected $data = [
		'id'              => null,
		'zotero_group_id' => '',
		'zotero_api_key'  => '',
	];

	protected static $post_type = 'ssrc_zotero_library';

	/**
	 * Gets the ID of a library.
	 *
	 * @return int
	 */
	public function get_id() {
		return (int) $this->data['id'];
	}

	/**
	 * Gets the group ID of the library.
	 *
	 * @return string
	 */
	public function get_zotero_group_id() {
		return $this->data['zotero_group_id'];
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

		$instance->data['zotero_group_id'] = get_post_meta( $id, 'zotero_group_id', true );
		$instance->data['zotero_api_key']  = get_post_meta( $id, 'zotero_api_key', true );

		return $instance;
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
