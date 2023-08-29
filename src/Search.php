<?php

namespace SSRC\RAMP;

class Search {
	/**
	 * The search clause captured from the 'posts_search' filter.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $current_search_clause = '';

	public function init() {
		// Modify query for search.
		add_action( 'pre_get_posts', [ $this, 'modify_query_for_search' ] );

		// Capture the 'posts_search' clause so that we can use it in the 'posts_clauses' filter
		add_filter( 'posts_search', [ $this, 'capture_posts_search_clause' ] );

		// Modify Citations query to match search term against Author postmeta field.
		add_filter( 'posts_clauses', [ $this, 'modify_citations_query_for_search' ], 10, 2 );
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

	/**
	 * Captures the current search clause so that we can use it in the 'posts_clauses' filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $search The search clause.
	 * @return string
	 */
	public function capture_posts_search_clause( $search ) {
		$this->current_search_clause = $search;
		return $search;
	}

	/**
	 * Modify query clauses when searching Citations to match against Author postmeta field.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $clauses The query clauses.
	 * @param \WP_Query $query   The WP_Query instance.
	 * @return array
	 */
	public function modify_citations_query_for_search( $clauses, $query ) {
		global $wpdb;

		if ( ! $query->is_search() ) {
			return $clauses;
		}

		// If this is not a Citations query, return.
		if ( ! isset( $query->query_vars['post_type'] ) || 'ramp_citation' !== $query->query_vars['post_type'] ) {
			return $clauses;
		}

		$search_term = $query->get( 's' );

		$postmeta_join_clause = " LEFT JOIN {$wpdb->postmeta} author_pm ON {$wpdb->posts}.ID = author_pm.post_id";

		$search_term_like      = '%' . $wpdb->esc_like( $search_term ) . '%';
		$postmeta_where_clause = $wpdb->prepare( "author_pm.meta_key = 'zotero_author' AND author_pm.meta_value LIKE %s", $search_term_like );

		$new_search_clause = preg_replace( '/AND \((.*)\)\s*$/', "AND ($1 OR ( $postmeta_where_clause )) ", $this->current_search_clause );
		$new_where_clause  = str_replace( $this->current_search_clause, $new_search_clause, $clauses['where'] );

		$clauses['join'] .= $postmeta_join_clause;
		$clauses['where'] = $new_where_clause;

		return $clauses;
	}
}
