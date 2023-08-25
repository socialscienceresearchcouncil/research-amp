<?php

namespace SSRC\RAMP;

use SSRC\RAMP\Util\Navigation;

class Install {
	public function install() {
		$this->install_default_research_topics();
		$this->install_default_pages();
		$this->install_default_nav_menus();
		$this->install_default_page_on_front();
		$this->install_default_logo();
		$this->set_installed_version();
	}

	protected function set_installed_version() {
		update_option( 'ramp_version', RAMP_VER );
	}

	/**
	 * Installs three default research topics.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function install_default_research_topics() {
		$research_topics_data = [
			'renewable-energy-technology' => [
				'post_title'   => __( 'Renewable Energy Technology', 'research-amp' ),
				'post_content' => __( 'Climate change mitigation will depend, in part, on the development and deployment of renewable energy technologies. This topic comprises research related to new technologies, as well as the debates surrounding the deployment and utility of existing tools.', 'research-amp' ),
			],
			'climate-change-policy'       => [
				'post_title'   => __( 'Climate Change Policy', 'research-amp' ),
				'post_content' => __( 'Governments have a central role to play in the development and implementation of policies that shape consumer and industrial policy related to climate change. This topic will includes such subjects as the Paris Agreement, carbon pricing, and the role of international organizations.', 'research-amp' ),
			],
			'migration-and-climate'       => [
				'post_title'   => __( 'Migration and Climate', 'research-amp' ),
				'post_content' => __( 'Climate change is expected to have a significant impact on human migration patterns.', 'research-amp' ),
			],
		];

		foreach ( $research_topics_data as $research_topic_slug => $research_topic_data ) {
			$existing_query = get_posts(
				[
					'post_type' => 'ramp_topic',
					'slug'      => sanitize_title( $research_topic_data['post_title'] ),
				]
			);

			if ( ! empty( $existing_query->posts ) ) {
				continue;
			}

			$research_topic_id = wp_insert_post(
				[
					'post_type'    => 'ramp_topic',
					'post_name'    => $research_topic_slug,
					'post_status'  => 'publish',
					'post_title'   => $research_topic_data['post_title'],
					'post_content' => '<!-- wp:paragraph --><p>' . $research_topic_data['post_content'] . '</p><!-- /wp:paragraph -->',
				]
			);

			if ( ! $research_topic_id ) {
				continue;
			}

			// Set the featured image.
			if ( ! function_exists( 'media_sideload_image' ) ) {
				require_once ABSPATH . 'wp-admin/includes/media.php';
			}

			if ( ! function_exists( 'download_url' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			if ( ! function_exists( 'wp_read_image_metadata' ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}

			$attachment_id = media_sideload_image( RAMP_PLUGIN_URL . '/assets/img/default-data/' . $research_topic_slug . '.jpg', $research_topic_id, '', 'id' );

			if ( $attachment_id ) {
				set_post_thumbnail( $research_topic_id, $attachment_id );
			}
		}
	}

	protected function install_default_pages() {
		$pages_data = [
			'get-started'          => [
				'post_title'   => __( 'Get Started', 'research-amp' ),
				'post_content' => __( 'Use this page to provide information on how readers can get involved in contributing to your project.', 'research-amp' ),
			],
			'about'                => [
				'post_title'   => __( 'About', 'research-amp' ),
				'post_content' => __( 'Use this page to provide background information on your project.', 'research-amp' ),
			],
			'contact'              => [
				'post_title'   => __( 'Contact', 'research-amp' ),
				'post_content' => __( 'Use this page to contact information for your project or organization. You may decide to use a WordPress plugin to provide a contact form.', 'research-amp' ),
			],
			'terms-and-conditions' => [
				'post_title'   => __( 'Terms and Conditions', 'research-amp' ),
				'post_content' => __( 'Use this page for the Terms and Conditions of your project.', 'research-amp' ),
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
				'post_title'   => __( 'Primary navigation', 'research-amp' ),
				'post_content' => Navigation::get_default_primary_nav_items(),
			],
			'secondary-nav' => [
				'post_title'   => __( 'Secondary navigation', 'research-amp' ),
				'post_content' => Navigation::get_default_secondary_nav_items(),
			],
			'footer-nav'    => [
				'post_title'   => __( 'Footer navigation', 'research-amp' ),
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
					'post_content' => wp_slash( $menu_data['post_content'] ),
				]
			);

			if ( $nav_menu_id ) {
				$ramp_nav_menus[ $menu_slug ] = $nav_menu_id;
			}
		}

		update_option( 'ramp_nav_menus', $ramp_nav_menus );
	}

	protected function install_default_page_on_front() {
		$page_on_front = wp_insert_post(
			[
				'post_title'   => __( 'Home Page', 'research-amp' ),
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

	public function install_default_logo() {
		// Run only in the Dashboard.
		if ( ! function_exists( 'media_sideload_image' ) ) {
			return;
		}

		$attachment_id = media_sideload_image( RAMP_PLUGIN_URL . '/assets/img/research-amp-logo.png', 0, __( 'Research AMP Logo', 'research-amp' ), 'id' );

		if ( $attachment_id ) {
			update_option( 'ramp_default_logo', $attachment_id );
		}

		// Don't overwrite an existing logo.
		$site_logo = get_option( 'site_logo' );
		if ( ! $site_logo && $attachment_id ) {
			update_option( 'site_logo', $attachment_id );
		}
	}
}
