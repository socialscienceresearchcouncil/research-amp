<?php

namespace SSRC\RAMP;

class Search {
	public function init() {
		// Modify query for search.
		add_action( 'pre_get_posts', [ $this, 'modify_query_for_search' ] );
	}

	public function modify_query_for_search( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( ! $query->is_search() ) {
			return;
		}

		$requested_type = self::get_requested_search_type();

		if ( ! $requested_type ) {
			return;
		}

		$post_types = array_map(
			function ( $type ) {
				switch ( $type ) {
					case 'topic' :
						return 'ramp_topic';

					case 'review' :
						return 'ramp_review';

					case 'article' :
						return 'ramp_article';

					case 'profile' :
						return 'ramp_profile';

					case 'news-item' :
						return 'ramp_news_item';

					case 'citation' :
						return 'ramp_citation';

					case 'event' :
						return 'tribe_events';
				}
			},
			[ $requested_type ]
		);

		if ( $post_types ) {
			$query->set( 'post_type', $post_types );
		}
	}

	public static function get_requested_search_type() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$requested_type = isset( $_GET['search-type'] ) ? wp_unslash( $_GET['search-type'] ) : '';

		$types = self::get_search_item_types();

		if ( ! isset( $types[ $requested_type ] ) ) {
			$requested_type = '';
		}

		return $requested_type;
	}

	public static function get_requested_search_term() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$requested_search_term = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		return $requested_search_term;
	}

	public static function get_search_item_types() {
		$types = [
			'article'   => __( 'Articles', 'research-amp' ),
			'citation'  => __( 'Citations', 'research-amp' ),
			'news-item' => __( 'News Items', 'research-amp' ),
			'profile'   => __( 'Profiles', 'research-amp' ),
			'review'    => __( 'Research Reviews', 'research-amp' ),
			'topic'     => __( 'Research Topics', 'research-amp' ),
		];

		if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
			$types['event'] = __( 'Events', 'research-amp' );
		}

		asort( $types );

		return $types;
	}
}
