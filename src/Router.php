<?php

namespace SSRC\RAMP;

class Router {
	public function init() {
		add_action( 'parse_query', [ $this, 'redirect_to_lr_versions' ] );
	}

	public function redirect_to_lr_versions( $query ) {
		if ( is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( 'ramp_review' !== $query->get( 'post_type' ) ) {
			return;
		}

		if ( $query->is_archive() ) {
			return;
		}

		$lit_review = get_page_by_path( $query->get( 'ramp_review' ), OBJECT, 'ramp_review' );

		$lr_versions = LitReviews\Version::get( $lit_review->ID );
		if ( ! $lr_versions ) {
			return;
		}

		$latest_version = array_shift( $lr_versions );

		wp_safe_redirect( get_permalink( $latest_version ) );
		die;
	}
}
