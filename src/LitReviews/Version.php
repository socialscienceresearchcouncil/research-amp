<?php

namespace SSRC\Disinfo\LitReviews;

use \WP_Query;

class Version {
	protected $id;
	protected $lr_id;

	public function __construct( $id ) {
		$this->id = (int) $id;

		$post = get_post( $id );
		$this->lr_id = $post->post_parent;
	}

	public function get_parent() {
		return get_post( $this->lr_id );
	}

	public function get_parent_title() {
		return $this->get_parent()->post_title;
	}

	public function is_latest_version() {
		$versions = self::get( $this->lr_id );

		if ( ! $versions ) {
			return true;
		}

		$latest = array_shift( $versions );

		return $this->id === $latest->ID;
	}

	public static function get( $lr_id ) {
		static $fetched;

		if ( $fetched ) {
			return $fetched;
		}

		$query = new WP_Query( [
			'post_type' => 'ssrc_lr_version',
			'post_parent' => $lr_id,
			'orderby' => 'date',
			'order' => 'DESC',
		] );

		$fetched = $query->posts;

		return $fetched;
	}
}
