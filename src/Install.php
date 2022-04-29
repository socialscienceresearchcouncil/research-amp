<?php

namespace SSRC\RAMP;

use \SSRC\RAMP\Util\Navigation;

class Install {
	public function install() {
		$this->install_default_pages();
		$this->install_default_nav_menus();
		$this->install_default_page_on_front();
	}

	protected function install_default_pages() {
		$pages_data = [
			'get-started'          => [
				'post_title'   => __( 'Get Started', 'ramp' ),
				'post_content' => __( 'Use this page to provide information on how readers can get involved in contributing to your project.', 'ramp' ),
			],
			'about'                => [
				'post_title'   => __( 'About', 'ramp' ),
				'post_content' => __( 'Use this page to provide background information on your project.', 'ramp' ),
			],
			'contact'              => [
				'post_title'   => __( 'Contact', 'ramp' ),
				'post_content' => __( 'Use this page to contact information for your project or organization. You may decide to use a WordPress plugin to provide a contact form.', 'ramp' ),
			],
			'terms-and-conditions' => [
				'post_title'   => __( 'Terms and Conditions', 'ramp' ),
				'post_content' => __( 'Use this page for the Terms and Conditions of your project.', 'ramp' ),
			],
		];

		$page_ids = get_option( 'ramp_pages', [] );

		foreach ( $pages_data as $page_slug => $page_data ) {
			// Don't create a page if one already exists.
			if ( isset( $page_ids[ $page_slug ] ) ) {
				continue;
			}

			$page_id = wp_insert_post(
				[
					'post_type'    => 'page',
					'post_name'    => $page_slug,
					'post_status'  => 'publish',
					'post_title'   => $page_data['post_title'],
					'post_content' => $page_data['post_content'],
				]
			);

			$page_ids[ $page_slug ] = $page_id;
		}

		update_option( 'ramp_pages', $page_ids );

		// Privacy Policy should have been created during installation.
		// Publish it so that we can use it in nav menus.
		$privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		if ( $privacy_policy_page_id ) {
			$privacy_policy_page = get_post( $privacy_policy_page_id );

			if ( $privacy_policy_page && 'publish' !== $privacy_policy_page ) {
				wp_publish_post( $privacy_policy_page );
			}
		}
	}

	protected function install_default_nav_menus() {
		$nav_menus = [
			'primary-nav'   => [
				'post_title'   => __( 'Primary navigation', 'ramp' ),
				'post_content' => Navigation::get_default_primary_nav_items(),
			],
			'secondary-nav' => [
				'post_title'   => __( 'Secondary navigation', 'ramp' ),
				'post_content' => Navigation::get_default_secondary_nav_items(),
			],
			'footer-nav'    => [
				'post_title'   => __( 'Footer navigation', 'ramp' ),
				'post_content' => Navigation::get_default_footer_nav_items(),
			],
		];

		$ramp_nav_menus = get_option( 'ramp_nav_menus', [] );

		foreach ( $nav_menus as $menu_slug => $menu_data ) {
			$nav_menu_id = wp_insert_post(
				[
					'post_type'    => 'wp_navigation',
					'post_status'  => 'publish',
					'post_name'    => $menu_slug,
					'post_title'   => $menu_data['post_title'],
					'post_content' => $menu_data['post_content'],
				]
			);

			if ( $nav_menu_id ) {
				$ramp_nav_menus[ $menu_slug ] = $nav_menu_id;
			}
		}

		update_option( 'ramp_nav_menus', $ramp_nav_menus );
	}

	protected function install_default_page_on_front() {
		$news_items_page = wp_insert_post(
			[
				'post_title'   => __( 'News Items', 'ramp' ),
				'post_name'    => 'news-items',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '',
			]
		);

		if ( $news_items_page ) {
			update_post_meta( $news_items_page, '_wp_page_template', 'page-news-items' );
		}

		$page_on_front = wp_insert_post(
			[
				'post_title'   => __( 'Home Page', 'ramp' ),
				'post_name'    => 'home-page',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '',
			]
		);

		if ( $page_on_front ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_on_front );
			update_post_meta( $page_on_front, '_wp_page_template', 'page-home-page' );
		}

	}
}
