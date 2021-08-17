<?php

namespace SSRC\Disinfo;

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

		if ( 'ssrc_lit_review' !== $query->get( 'post_type' ) ) {
			return;
		}

		$lit_review = get_page_by_path( $query->get( 'ssrc_lit_review' ), OBJECT, 'ssrc_lit_review' );

		$lr_versions = LitReviews\Version::get( $lit_review->ID );
		if ( ! $lr_versions ) {
			return;
		}

		$latest_version = array_shift( $lr_versions );

		wp_redirect( get_permalink( $latest_version ) );
		die;
	}
}
