<?php

namespace SSRC\RAMP;

use SSRC\RAMP\Util\Navigation;

class Install {
	/**
	 * Default focus tags.
	 *
	 * @var array
	 */
	protected $default_focus_tags = [
		'solar energy',
		'Paris Agreement',
		'US Government Policy',
	];

	public function install() {
		$this->install_default_research_topics();
		$this->install_default_profiles();
		$this->install_default_research_reviews();
		$this->install_default_articles();
		$this->install_default_pages();
		$this->install_default_nav_menus();
		$this->install_default_page_on_front();
		$this->install_default_logo();
		$this->install_default_homepage_slide();
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
					'post_content' => $this->convert_text_to_paragraph_blocks( $research_topic_data['post_content'] ),
				]
			);

			if ( ! $research_topic_id ) {
				continue;
			}

			$this->set_featured_image( $research_topic_id, RAMP_PLUGIN_URL . '/assets/img/default-data/research-topics/' . $research_topic_slug . '.jpg' );
		}
	}

	/**
	 * Create some default profiles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function install_default_profiles() {
		$research_topics = get_posts(
			[
				'post_type' => 'ramp_topic',
			]
		);

		$profiles = [
			[
				'name'            => __( 'Jane Doe', 'research-amp' ),
				'sortable_name'   => __( 'Doe, Jane', 'research-amp' ),
				'title'           => __( 'Researcher, University of Research', 'research-amp' ),
				'featured_image'  => RAMP_PLUGIN_URL . '/assets/img/default-data/profiles/jane-doe.png',
				'biography'       => $this->get_lorem_ipsum( 1 ),
				'twitter'         => 'janedoe',
				'website'         => 'https://example.com',
				'research_topics' => [ $research_topics[0]->ID ],
				'focus_tags'      => [ $this->default_focus_tags[0], $this->default_focus_tags[1] ],
			],
			[
				'name'            => __( 'John Doe', 'research-amp' ),
				'sortable_name'   => __( 'Doe, John', 'research-amp' ),
				'title'           => __( 'President, Cultural Institution', 'research-amp' ),
				'featured_image'  => RAMP_PLUGIN_URL . '/assets/img/default-data/profiles/john-doe.png',
				'biography'       => $this->get_lorem_ipsum( 1 ),
				'twitter'         => 'johndoe',
				'website'         => 'https://example.com',
				'research_topics' => [ $research_topics[1]->ID, $research_topics[2]->ID ],
				'focus_tags'      => [ $this->default_focus_tags[1], $this->default_focus_tags[2] ],
			],
		];

		foreach ( $profiles as $profile ) {
			$content = sprintf(
				'<!-- wp:group -->
  <div class="wp-block-group"><!-- wp:research-amp/item-type-label /-->

  <!-- wp:post-title {"level":1} /-->

  <!-- wp:research-amp/profile-title-institution {"content":"%s"} -->
  <div class="wp-block-research-amp-profile-title-institution">%s</div>
  <!-- /wp:research-amp/profile-title-institution --></div>
  <!-- /wp:group -->

  <!-- wp:columns -->
  <div class="wp-block-columns"><!-- wp:column {"width":"33.33%%"} -->
  <div class="wp-block-column" style="flex-basis:33.33%%"><!-- wp:research-amp/profile-photo -->
  <div class="wp-block-research-amp-profile-photo"><!-- wp:post-featured-image {"width":"300px","height":"300px"} /--></div>
  <!-- /wp:research-amp/profile-photo -->

  <!-- wp:group -->
  <div class="wp-block-group"><!-- wp:research-amp/profile-vital-link {"vitalType":"email"} -->
  <div></div>
  <!-- /wp:research-amp/profile-vital-link -->

  <!-- wp:research-amp/profile-vital-link {"value":"%s","vitalType":"twitter"} -->
  <div class="wp-block-research-amp-profile-vital-link ramp-profile-vital-link-twitter"><a href="https://twitter.com/%s" class="ramp-profile-vital-link-text">%s</a></div>
  <!-- /wp:research-amp/profile-vital-link -->

  <!-- wp:research-amp/profile-vital-link {"vitalType":"orcidId"} -->
  <div></div>
  <!-- /wp:research-amp/profile-vital-link -->

  <!-- wp:research-amp/profile-vital-link {"value":"%s","vitalType":"website"} -->
  <div class="wp-block-research-amp-profile-vital-link ramp-profile-vital-link-website"><a href="%s" class="ramp-profile-vital-link-text">%s</a></div>
  <!-- /wp:research-amp/profile-vital-link --></div>
  <!-- /wp:group --></div>
  <!-- /wp:column -->

  <!-- wp:column {"width":"66.66%%"} -->
  <div class="wp-block-column" style="flex-basis:66.66%%"><!-- wp:research-amp/profile-bio -->
  <div class="wp-block-research-amp-profile-bio">
  %s
  </div>
  <!-- /wp:research-amp/profile-bio -->

  <!-- wp:research-amp/item-research-topics /--></div>
  <!-- /wp:column --></div>
  <!-- /wp:columns -->',
				$profile['title'],
				$profile['title'],
				$profile['twitter'],
				$profile['twitter'],
				$profile['twitter'],
				$profile['website'],
				$profile['website'],
				$profile['website'],
				$profile['biography']
			);

			$profile_id = wp_insert_post(
				[
					'post_type'    => 'ramp_profile',
					'post_name'    => sanitize_title_with_dashes( $profile['name'] ),
					'post_title'   => $profile['name'],
					'post_status'  => 'publish',
					'post_content' => $content,
				]
			);

			if ( ! $profile_id ) {
				continue;
			}

			$this->set_featured_image( $profile_id, $profile['featured_image'] );

			add_post_meta( $profile_id, 'alphabetical_name', $profile['sortable_name'] );

			$rt_map      = ramp_app()->get_cpttax_map( 'research_topic' );
			$rt_term_ids = array_map(
				function ( $rt_id ) use ( $rt_map ) {
					return $rt_map->get_term_id_for_post_id( $rt_id );
				},
				$profile['research_topics']
			);

			wp_set_post_terms( $profile_id, $rt_term_ids, 'ramp_assoc_topic' );

			$set = wp_set_post_terms( $profile_id, $profile['focus_tags'], 'ramp_focus_tag' );
		}
	}

	/**
	 * For each default research topic, create a corresponding research review.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function install_default_research_reviews() {
		$research_topics = get_posts(
			[
				'post_type' => 'ramp_topic',
			]
		);

		$profiles = get_posts(
			[
				'post_type' => 'ramp_profile',
			]
		);

		$counter = 0;
		foreach ( $research_topics as $research_topic ) {
			++$counter;

			$review_slug = $research_topic->post_name . '-review';

			// translators: Research topic name.
			$review_title = sprintf( __( '%s Review', 'research-amp' ), $research_topic->post_title );

			$review_text = $this->get_lorem_ipsum( 1 );

			$headings = [
				__( 'Overview', 'research-amp' ),
				__( 'Section One', 'research-amp' ),
				__( 'Section Two', 'research-amp' ),
				__( 'Works Cited', 'research-amp' ),
			];

			foreach ( $headings as $heading ) {
				$review_text .= '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">' . $heading . '</h2><!-- /wp:heading -->' . "\n" . $this->get_lorem_ipsum( 1 );
			}

			$review_text .= sprintf(
				'<!-- wp:research-amp/changelog -->
<div class="wp-block-research-amp-changelog"><div class="changelog-header"><h2 class="has-h-5-font-size">%1$s</h2></div><!-- wp:research-amp/changelog-entry {"dateText":"%2$s"} -->
<div class="wp-block-research-amp-changelog-entry"><p class="changelog-entry-date">%3$s</p><ul><li>%4$s</li></ul></div>
<!-- /wp:research-amp/changelog-entry --></div>
<!-- /wp:research-amp/changelog -->',
				__( 'Changelog', 'research-amp' ),
				gmdate( 'F j, Y' ),
				gmdate( 'F j, Y' ),
				__( 'Initial release.', 'research-amp' )
			);

			$research_review_id = wp_insert_post(
				[
					'post_type'    => 'ramp_review',
					'post_name'    => $review_slug,
					'post_status'  => 'publish',
					'post_title'   => $review_title,
					'post_content' => $review_text,
				]
			);

			if ( ! $research_review_id ) {
				continue;
			}

			// Associate with the research topic.
			$rt_map     = ramp_app()->get_cpttax_map( 'research_topic' );
			$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic->ID );

			wp_set_post_terms( $research_review_id, [ $rt_term_id ], 'ramp_assoc_topic' );

			$this->set_featured_image( $research_review_id, RAMP_PLUGIN_URL . '/assets/img/default-data/research-reviews/' . $review_slug . '.jpg' );

			// Odd numbered reviews should be associated with Jane Doe.
			$profile_id      = $profiles[ $counter % 2 ]->ID;
			$profile_map     = ramp_app()->get_cpttax_map( 'profile' );
			$profile_term_id = $profile_map->get_term_id_for_post_id( $profile_id );

			wp_set_post_terms( $research_review_id, [ $profile_term_id ], 'ramp_assoc_profile' );
		}
	}

	/**
	 * Create some default articles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function install_default_articles() {
		// Create default ramp_article_type terms.
		$article_types = [
			'Interview',
			'Video',
			'Essay Series',
		];

		$article_type_term_ids = [];
		foreach ( $article_types as $article_type ) {
			$article_type_term = wp_insert_term(
				$article_type,
				'ramp_article_type'
			);

			if ( ! is_wp_error( $article_type_term ) ) {
				$article_type_term_ids[] = $article_type_term['term_id'];
			}
		}

		$research_topics = get_posts(
			[
				'post_type' => 'ramp_topic',
			]
		);

		$profiles = get_posts(
			[
				'post_type' => 'ramp_profile',
			]
		);

		$articles = [
			[
				'post_title'      => __( 'Advancements in Solar Panel Efficiency: Illuminating the Path for Sustainable Energy', 'research-amp' ),
				'research_topics' => [ $research_topics[2]->ID ],
				'featured_image'  => RAMP_PLUGIN_URL . '/assets/img/default-data/articles/solar.jpg',
				'article_types'   => [ $article_type_term_ids[0] ],
			],
			[
				'post_title'      => __( 'Analyzing the Paris Agreement: International Cooperation in Combating Climate Crisis', 'research-amp' ),
				'research_topics' => [ $research_topics[1]->ID ],
				'featured_image'  => RAMP_PLUGIN_URL . '/assets/img/default-data/articles/paris.jpg',
				'article_types'   => [ $article_type_term_ids[1] ],
			],
			[
				'post_title'      => __( 'Climate Refugees: Navigating the Complexities of Forced Migration due to Environmental Factors', 'research-amp' ),
				'research_topics' => [ $research_topics[0]->ID, $research_topics[1]->ID ],
				'featured_image'  => RAMP_PLUGIN_URL . '/assets/img/default-data/articles/forced.jpg',
				'article_types'   => [ $article_type_term_ids[2] ],
			],
			[
				'post_title'      => __( 'Migration Patterns in the Era of Climate Change: Interplay Between Environmental and Policy Factors', 'research-amp' ),
				'research_topics' => [ $research_topics[0]->ID, $research_topics[1]->ID ],
				'featured_image'  => RAMP_PLUGIN_URL . '/assets/img/default-data/articles/patterns.jpg',
				'article_types'   => [ $article_type_term_ids[1], $article_type_term_ids[2] ],
			],
		];

		$counter = 0;
		foreach ( $articles as $article ) {
			++$counter;

			$article_id = wp_insert_post(
				[
					'post_type'    => 'ramp_article',
					'post_name'    => sanitize_title_with_dashes( $article['post_title'] ),
					'post_status'  => 'publish',
					'post_title'   => $article['post_title'],
					'post_content' => $this->generate_article_content(),
				]
			);

			if ( ! $article_id ) {
				continue;
			}

			// Associate with the necessary research topics.
			$rt_map      = ramp_app()->get_cpttax_map( 'research_topic' );
			$rt_term_ids = array_map(
				function ( $rt_id ) use ( $rt_map ) {
					return $rt_map->get_term_id_for_post_id( $rt_id );
				},
				$article['research_topics']
			);

			wp_set_post_terms( $article_id, $rt_term_ids, 'ramp_assoc_topic' );

			$this->set_featured_image( $article_id, $article['featured_image'] );

			// Odd numbered reviews should be associated with Jane Doe.
			$profile_id      = $profiles[ $counter % 2 ]->ID;
			$profile_map     = ramp_app()->get_cpttax_map( 'profile' );
			$profile_term_id = $profile_map->get_term_id_for_post_id( $profile_id );

			wp_set_post_terms( $article_id, [ $profile_term_id ], 'ramp_assoc_profile' );

			wp_set_post_terms( $article_id, $article['article_types'], 'ramp_article_type' );
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

			$page_content = $this->convert_text_to_paragraph_blocks( $page_data['post_content'] ) . $this->get_lorem_ipsum( 3 );

			$page_id = wp_insert_post(
				[
					'post_type'    => 'page',
					'post_name'    => $page_slug,
					'post_status'  => 'publish',
					'post_title'   => $page_data['post_title'],
					'post_content' => $page_content,
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

	/**
	 * Installs a default homepage slide.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function install_default_homepage_slide() {
		$post_content = sprintf(
			__( 'Welcome to Research AMP! Visit the <a href="%s">Get Started</a> page to learn more about how to use this theme.', 'research-amp' ),
			home_url( 'get-started' )
		);

		$homepage_slide_id = wp_insert_post(
			[
				'post_type'    => 'ramp_homepage_slide',
				'post_name'    => 'welcome-to-research-amp',
				'post_title'   => __( 'Welcome to Research AMP', 'research-amp' ),
				'post_content' => $post_content,
				'post_status'  => 'publish',
			]
		);

		if ( ! $homepage_slide_id ) {
			return;
		}

		add_post_meta( $homepage_slide_id, 'ramp_slide_meta_text', __( 'Getting Started', 'research-amp' ) );
		add_post_meta( $homepage_slide_id, 'ramp_slide_button_text', __( 'Learn More', 'research-amp' ) );
		add_post_meta( $homepage_slide_id, 'ramp_slide_button_url', home_url( 'get-started' ) );

		$this->set_featured_image( $homepage_slide_id, RAMP_PLUGIN_URL . '/assets/img/default-data/homepage-slide.jpg' );
	}

	/**
	 * Saves a featured image to a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The post ID.
	 * @param string $image_url The URL of the image to save.
	 * @return void
	 */
	protected function set_featured_image( $post_id, $image_url ) {
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

		$attachment_id = media_sideload_image( $image_url, $post_id, '', 'id' );

		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	/**
	 * Converts multiline text block to a string containing wp:paragraph blocks.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The text to convert.
	 * @return string
	 */
	protected function convert_text_to_paragraph_blocks( $text ) {
		$paragraphs = explode( "\n\n", $text );

		$paragraph_blocks = array_map(
			function ( $p ) {
				return '<!-- wp:paragraph --><p>' . $p . '</p><!-- /wp:paragraph -->';
			},
			$paragraphs
		);

		return implode( '', $paragraph_blocks );
	}

	/**
	 * Gets 10 paragraphs of lorem ipsum text.
	 *
	 * @since 1.0.0
	 *
	 * @param int $number_of_paragraphs The number of paragraphs to return.
	 * @return string
	 */
	protected function get_lorem_ipsum( $number_of_paragraphs = 10 ) {
		$paragraphs = [
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. In hac habitasse platea dictumst vestibulum. At in tellus integer feugiat scelerisque varius morbi. Risus commodo viverra maecenas accumsan lacus vel facilisis volutpat est. Consectetur adipiscing elit ut aliquam purus sit amet. Auctor urna nunc id cursus metus. Gravida arcu ac tortor dignissim convallis aenean et. Neque volutpat ac tincidunt vitae semper quis lectus nulla. Non pulvinar neque laoreet suspendisse interdum consectetur libero id. Et odio pellentesque diam volutpat commodo sed egestas egestas fringilla. Tellus elementum sagittis vitae et leo duis ut diam.',
			'Lobortis elementum nibh tellus molestie nunc. Ultrices neque ornare aenean euismod elementum nisi. Mi eget mauris pharetra et ultrices neque ornare aenean euismod. Elementum curabitur vitae nunc sed velit. Enim eu turpis egestas pretium aenean pharetra magna ac. Scelerisque eu ultrices vitae auctor eu augue ut lectus. Tellus orci ac auctor augue mauris. Semper auctor neque vitae tempus quam. Proin sagittis nisl rhoncus mattis rhoncus urna neque viverra justo. Sed enim ut sem viverra aliquet. Facilisi etiam dignissim diam quis enim lobortis scelerisque fermentum. Habitant morbi tristique senectus et netus. Praesent semper feugiat nibh sed pulvinar proin. Sit amet dictum sit amet justo. Turpis tincidunt id aliquet risus feugiat in. Eu scelerisque felis imperdiet proin fermentum leo vel orci porta. Ut lectus arcu bibendum at varius vel pharetra vel turpis. Dignissim convallis aenean et tortor at risus viverra adipiscing at. Ut sem nulla pharetra diam sit amet nisl suscipit. Fusce ut placerat orci nulla pellentesque.',
			'Mauris augue neque gravida in fermentum et sollicitudin ac orci. Ut consequat semper viverra nam libero justo laoreet sit amet. Porttitor rhoncus dolor purus non enim praesent elementum facilisis. Nunc id cursus metus aliquam eleifend mi. Viverra vitae congue eu consequat ac felis donec et odio. Turpis nunc eget lorem dolor. Nisi quis eleifend quam adipiscing vitae proin. Eu sem integer vitae justo eget magna fermentum. Placerat vestibulum lectus mauris ultrices eros in. Velit euismod in pellentesque massa placerat duis ultricies lacus sed. Eros in cursus turpis massa tincidunt dui ut. Aliquet lectus proin nibh nisl. Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Maecenas pharetra convallis posuere morbi leo urna molestie at. Augue neque gravida in fermentum et sollicitudin ac. Felis imperdiet proin fermentum leo vel orci porta.',
			'At tempor commodo ullamcorper a. Scelerisque in dictum non consectetur a erat nam at lectus. Amet consectetur adipiscing elit duis. Et tortor consequat id porta nibh venenatis cras. Ut morbi tincidunt augue interdum velit. Ullamcorper a lacus vestibulum sed arcu non odio. Lacus sed turpis tincidunt id aliquet risus feugiat. Mollis nunc sed id semper. Amet volutpat consequat mauris nunc congue. Interdum consectetur libero id faucibus nisl. Vitae nunc sed velit dignissim sodales ut eu sem. Ornare lectus sit amet est. Tincidunt vitae semper quis lectus nulla at volutpat diam. Consequat ac felis donec et odio pellentesque diam volutpat. Quam elementum pulvinar etiam non quam. Aliquam purus sit amet luctus venenatis lectus magna.',
			'Egestas sed sed risus pretium quam vulputate dignissim suspendisse. Vitae aliquet nec ullamcorper sit amet risus nullam. At tellus at urna condimentum mattis pellentesque. Integer malesuada nunc vel risus. Feugiat pretium nibh ipsum consequat nisl vel pretium lectus quam. Gravida cum sociis natoque penatibus et magnis dis. Maecenas ultricies mi eget mauris pharetra et ultrices. Morbi quis commodo odio aenean sed adipiscing. Gravida quis blandit turpis cursus. Augue interdum velit euismod in pellentesque massa placerat duis. In eu mi bibendum neque egestas congue quisque egestas diam. Ultrices mi tempus imperdiet nulla malesuada pellentesque.',
			'Mus mauris vitae ultricies leo integer malesuada. Tellus elementum sagittis vitae et leo duis ut diam. Egestas quis ipsum suspendisse ultrices. Purus ut faucibus pulvinar elementum. Enim blandit volutpat maecenas volutpat blandit aliquam etiam erat. Quis blandit turpis cursus in hac habitasse platea. Nec dui nunc mattis enim ut tellus elementum. Habitant morbi tristique senectus et. Malesuada nunc vel risus commodo viverra maecenas accumsan lacus. Sed turpis tincidunt id aliquet risus feugiat. Nulla porttitor massa id neque aliquam vestibulum morbi blandit.',
			'Tincidunt arcu non sodales neque. Molestie at elementum eu facilisis sed odio morbi quis. Luctus venenatis lectus magna fringilla urna. Id faucibus nisl tincidunt eget. Nunc sed id semper risus. Tortor id aliquet lectus proin nibh nisl condimentum. Tincidunt praesent semper feugiat nibh sed pulvinar. Consequat nisl vel pretium lectus quam id. Elementum pulvinar etiam non quam. Tincidunt praesent semper feugiat nibh sed. Eu ultrices vitae auctor eu. In massa tempor nec feugiat nisl pretium fusce id. Arcu risus quis varius quam quisque id diam vel. Auctor urna nunc id cursus metus aliquam eleifend mi in. Id volutpat lacus laoreet non curabitur gravida arcu ac tortor. Id aliquet risus feugiat in ante metus dictum at tempor. Aliquam faucibus purus in massa tempor nec.',
			'Tincidunt eget nullam non nisi est sit amet facilisis. Aliquam faucibus purus in massa tempor nec feugiat nisl pretium. Malesuada fames ac turpis egestas sed tempus urna. Nulla facilisi nullam vehicula ipsum. Sed adipiscing diam donec adipiscing tristique risus nec. Massa sed elementum tempus egestas sed sed risus pretium. Est ante in nibh mauris cursus mattis molestie a iaculis. Magna eget est lorem ipsum dolor sit amet. Scelerisque eu ultrices vitae auctor eu augue ut. Sed sed risus pretium quam vulputate dignissim. Egestas diam in arcu cursus euismod quis viverra nibh.',
			'At tempor commodo ullamcorper a lacus vestibulum sed arcu non. Purus in mollis nunc sed id. Id faucibus nisl tincidunt eget nullam non nisi. Praesent semper feugiat nibh sed pulvinar proin gravida hendrerit. Turpis egestas pretium aenean pharetra magna ac placerat. Et sollicitudin ac orci phasellus. Praesent tristique magna sit amet. Ultrices mi tempus imperdiet nulla malesuada pellentesque. Vulputate odio ut enim blandit volutpat maecenas. Nulla porttitor massa id neque.',
			'Urna porttitor rhoncus dolor purus non enim. Nullam ac tortor vitae purus faucibus ornare suspendisse. At augue eget arcu dictum varius. Egestas maecenas pharetra convallis posuere. Diam sollicitudin tempor id eu nisl nunc mi. Tempus imperdiet nulla malesuada pellentesque elit eget. Eu ultrices vitae auctor eu augue ut lectus arcu bibendum. Dolor purus non enim praesent elementum facilisis leo. Accumsan sit amet nulla facilisi morbi tempus iaculis. Ullamcorper morbi tincidunt ornare massa eget egestas.',
		];

		// Rearrange the paragraphs so that they're not always the same.
		shuffle( $paragraphs );

		$text = implode( "\n\n", array_slice( $paragraphs, 0, $number_of_paragraphs ) );

		return $this->convert_text_to_paragraph_blocks( $text );
	}

	/**
	 * Generates some random article content.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function generate_article_content() {
		$article_content = '';

		$number_of_sections = wp_rand( 3, 5 );

		for ( $i = 0; $i < $number_of_sections; $i++ ) {
			// Section heading should be 3-5 words of lorem ipsum.
			$ipsum_words     = explode( ' ', wp_strip_all_tags( $this->get_lorem_ipsum( 1 ) ) );
			$heading_count   = wp_rand( 3, 5 );
			$section_heading = implode( ' ', array_slice( $ipsum_words, 0, $heading_count ) );

			$article_content .= '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">' . $section_heading . '</h2><!-- /wp:heading -->' . "\n" . $this->get_lorem_ipsum( 3 );
		}

		return $article_content;
	}
}
