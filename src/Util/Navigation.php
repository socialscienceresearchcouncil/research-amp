<?php

namespace SSRC\RAMP\Util;

class Navigation {
	/**
	 * Fetches a nav ID.
	 *
	 * @param string $nav Nav name.
	 * @return int
	 */
	public static function get_nav_id( $nav ) {
		$ramp_nav_menus = get_option( 'ramp_nav_menus', [] );
		$nav_id         = isset( $ramp_nav_menus[ $nav ] ) ? (int) $ramp_nav_menus[ $nav ] : 0;
		return $nav_id;
	}

	/**
	 * Gets the default navigation items for the primary nav.
	 */
	public static function get_default_primary_nav_items() {
		$research_reviews_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Research Reviews', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_review' ) )
		);

		$articles_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Articles', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_article' ) )
		);

		$profiles_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Profiles', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_profile' ) )
		);

		$citations_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Citations', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_citation' ) )
		);

		$items = [
			self::get_research_topics_submenu_block(),
			$research_reviews_link,
			$articles_link,
			$profiles_link,
			$citations_link,
		];

		return join( "\n", $items );
	}

	/**
	 * Gets the default navigation items for the secondary nav.
	 */
	public static function get_default_secondary_nav_items() {
		$ramp_pages = get_option( 'ramp_pages' );

		$nav_links = [];

		if ( isset( $ramp_pages['get-started'] ) ) {
			$get_started_page = get_post( $ramp_pages['get-started'] );
			if ( $get_started_page ) {
				$nav_links[] = sprintf(
					'<!-- wp:navigation-link {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type","isTopLevelLink":true} /-->',
					esc_attr( $get_started_page->post_title ),
					(int) $get_started_page->ID,
					esc_url( get_permalink( $get_started_page ) )
				);
			}
		}

		if ( isset( $ramp_pages['contact'] ) ) {
			$contact_page = get_post( $ramp_pages['contact'] );
			if ( $contact_page ) {
				$nav_links[] = sprintf(
					'<!-- wp:navigation-link {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type","isTopLevelLink":true} /-->',
					esc_attr( $contact_page->post_title ),
					(int) $contact_page->ID,
					esc_url( get_permalink( $contact_page ) )
				);
			}
		}

		$nav_links[] = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"page","url":"%s","kind":"post-type","isTopLevelLink":true} /-->',
			esc_attr__( 'Log In', 'ramp' ),
			esc_url( wp_login_url() )
		);

		return implode( "\n", $nav_links );
	}

	/**
	 * Gets the default navigation items for the footer nav.
	 */
	public static function get_default_footer_nav_items() {
		$research_reviews_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Research Reviews', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_review' ) )
		);

		$articles_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Articles', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_article' ) )
		);

		$resources_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Resources', 'ramp' ) ),
			esc_url( '#' )
		);

		$citations_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Citations', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_citation' ) )
		);

		$items = [
			self::get_research_topics_submenu_block(),
			$research_reviews_link,
			$articles_link,
			$resources_link,
			$citations_link,
		];

		return join( "\n", $items );
	}

	/**
	 * Fetches the Research Topics submenu navigation block and its children.
	 *
	 * @todo This works for the initial insertion, but it's not dynamic for newly created RTs.
	 */
	public static function get_research_topics_submenu_block() {
		$rts = get_posts(
			[
				'post_type'      => 'ramp_topic',
				'posts_per_page' => -1,
				'orderby'        => [ 'title' => 'ASC' ],
			]
		);

		// This should probably be its own block.
		$research_topics_submenu_items = '';
		foreach ( $rts as $rt ) {
			$rt_item = sprintf(
				'<!-- wp:navigation-link {"label":"%s","url":"%s","id":"%d","type":"ramp_theme","kind":"post-type","isTopLevelLink":false} /-->',
				esc_attr( $rt->post_title ),
				esc_attr( get_permalink( $rt ) ),
				esc_attr( $rt->ID )
			);

			$research_topics_submenu_items .= $rt_item;
		}

		$research_topics_submenu = sprintf(
			'<!-- wp:navigation-submenu {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} -->' .
			'%s' .
			'<!-- /wp:navigation-submenu -->',
			esc_attr( __( 'Research Topics', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_theme' ) ),
			$research_topics_submenu_items
		);

		return $research_topics_submenu;
	}

}
