<?php

namespace SSRC\RAMP\Featured;

class FeaturedItem {
	protected $post_id;

	public static function get_instance( $post_id ) {
		$instance = new self();

		$instance->set_post_id( $post_id );

		return $instance;
	}

	public static function get_currently_featured_posts( $post_type ) {
		$posts = get_posts(
			[
				'post_type'                 => $post_type,
				'post_status'               => 'publish',
				'meta_key'                  => 'disinfo_featured_timestamp',
				'orderby'                   => [ 'meta_value_num' => 'DESC' ],
				'posts_per_page'            => 5, // Hardcoded for the time being.
				'md_bone_duplicate_disable' => 'adp_disable',
			]
		);

		return $posts;
	}

	public static function get_currently_featured_id( $post_type ) {
		$posts = self::get_currently_featured_posts( $post_type );
		return $posts[0]->ID;
	}

	public function get_post_id() {
		return (int) $this->post_id;
	}

	protected function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}

	public function is_currently_featured( $post_type ) {
		$currently_featured_id = self::get_currently_featured_id( $post_type );
		return $currently_featured_id === $this->get_post_id();
	}

	public function get_featured_date() {
		$timestamp = get_post_meta( $this->get_post_id(), 'disinfo_featured_timestamp', true );

		if ( ! $timestamp ) {
			return null;
		}

		return new \DateTime( '@' . $timestamp );
	}

	public function get_feature_link( $redirect_to ) {
		$base = add_query_arg(
			[
				'disinfo-feature' => $this->get_post_id(),
				'redirect_to'     => rawurlencode( $redirect_to ),
			],
			admin_url()
		);

		return wp_nonce_url( $base, 'disinfo-feature-' . $this->get_post_id() );
	}

	public function get_unfeature_link( $redirect_to ) {
		$base = add_query_arg(
			[
				'disinfo-unfeature' => $this->get_post_id(),
				'redirect_to'       => rawurlencode( $redirect_to ),
			],
			admin_url()
		);

		return wp_nonce_url( $base, 'disinfo-unfeature-' . $this->get_post_id() );
	}

	public function mark_featured() {
		update_post_meta( $this->get_post_id(), 'disinfo_featured_timestamp', time() );
	}

	public function mark_unfeatured() {
		delete_post_meta( $this->get_post_id(), 'disinfo_featured_timestamp' );
	}
}
