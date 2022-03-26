<?php

namespace SSRC\RAMP;

class Router {
	public function init() {
		add_action( 'parse_query', [ $this, 'redirect_to_review_versions' ] );
		add_action( 'pre_get_posts', [ $this, 'route_review_version' ] );
	}

	public function redirect_to_review_versions( $query ) {
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

		$review = get_page_by_path( $query->get( 'ramp_review' ), OBJECT, 'ramp_review' );

		$review_versions = LitReviews\Version::get( $review->ID );
		if ( ! $review_versions ) {
			return;
		}

		$latest_version = array_shift( $review_versions );

		wp_safe_redirect( get_permalink( $latest_version ) );
		die;
	}

	public function route_review_version( $query ) {
		$review_slug = $query->get( 'lr_slug' );
		if ( $review_slug ) {
			$lr_review = get_page_by_path( $review_slug, OBJECT, 'ramp_review' );
			if ( $lr_review ) {
				$query->set( 'post_parent', $lr_review->ID );
			}
		}
	}
}
