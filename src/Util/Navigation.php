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

		return implode( '', $items );
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

		return implode( '', $nav_links );
	}

	/**
	 * Gets the default navigation items for the footer nav.
	 */
	public static function get_default_footer_nav_items() {
		$research_topics_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'Research Topics', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_topic' ) )
		);

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

		$news_items_link = sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true} /-->',
			esc_attr( __( 'News Items', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'post' ) )
		);

		$items = [
			$research_topics_link,
			$research_reviews_link,
			$articles_link,
			$profiles_link,
			$citations_link,
			$news_items_link,
		];

		return join( '', $items );
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
			$rt_item = get_comment_delimited_block_content(
				'core/navigation-link',
				[
					'label'          => esc_attr( $rt->post_title ),
					'url'            => esc_attr( get_permalink( $rt ) ),
					'id'             => esc_attr( $rt->ID ),
					'type'           => 'ramp_theme',
					'kind'           => 'post-type',
					'isTopLevelLink' => false,
				],
				''
			);

			$research_topics_submenu_items .= $rt_item;
		}

		$research_topics_submenu = sprintf(
			'<!-- wp:navigation-submenu {"label":"%s","type":"","url":"%s","kind":"post-type-archive","isTopLevelItem":true,"className":"research-topic-subnav"} -->' .
			'%s' .
			'<!-- /wp:navigation-submenu -->',
			esc_attr( __( 'Research Topics', 'ramp' ) ),
			esc_url( get_post_type_archive_link( 'ramp_topic' ) ),
			$research_topics_submenu_items
		);

		return $research_topics_submenu;
	}

	public static function replace_research_topics_subnav() {
		$nav_menus = get_option( 'ramp_nav_menus' );
		if ( ! $nav_menus ) {
			return;
		}

		foreach ( $nav_menus as $nav_menu_id ) {
			$nav_post = get_post( $nav_menu_id );
			if ( ! $nav_post ) {
				continue;
			}

			$post_content     = $nav_post->post_content;
			$post_is_modified = false;

			$nav_blocks = parse_blocks( $post_content );
			foreach ( $nav_blocks as $nav_block ) {
				if ( 'core/navigation-submenu' !== $nav_block['blockName'] ) {
					continue;
				}

				if ( empty( $nav_block['attrs']['className'] ) || 'research-topic-subnav' !== $nav_block['attrs']['className'] ) {
					continue;
				}

				$old_block = serialize_block( $nav_block );
				$new_block = self::get_research_topics_submenu_block();

				if ( $old_block === $new_block ) {
					continue;
				}

				$post_content = str_replace( $old_block, $new_block, $post_content );

				$post_is_modified = true;
			}

			if ( $post_is_modified ) {
				$updated = wp_update_post(
					[
						'ID'           => $nav_post->ID,
						'post_content' => wp_slash( $post_content ),
					]
				);
			}
		}
	}
}
