<?php

namespace SSRC\RAMP;

use SSRC\RAMP\App;

class Blocks {
	/**
	 * Initialize Blocks for RAMP.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_block_assets_frontend' ] );

		add_action( 'wp_footer', [ $this, 'load_empty_block_js' ] );

		add_filter( 'block_categories_all', [ $this, 'register_block_category' ] );

		add_action( 'after_setup_theme', [ $this, 'add_image_sizes' ] );

		add_action( 'init', [ $this, 'register_server_side_rendered_blocks' ] );

		add_filter( 'save_post', [ $this, 'save_profile_data_from_blocks' ] );

		add_action( 'save_post', [ __CLASS__, 'mirror_profile_vital_link_to_postmeta' ] );

		add_filter( 'render_block_core/navigation-link', [ '\SSRC\RAMP\Util\Navigation', 'add_current_classes_to_nav_links' ], 10, 2 );
		add_filter( 'render_block_core/navigation-submenu', [ '\SSRC\RAMP\Util\Navigation', 'add_current_classes_to_nav_links' ], 10, 2 );

		add_filter( 'render_block_core/post-featured-image', [ __CLASS__, 'set_profile_fallback_image' ] );
	}

	public function add_image_sizes() {
		// 3:2 ratio
		add_image_size(
			'ramp-thumbnail',
			840,
			560,
			true
		);
	}

	/**
	 * Enqueue block assets on the Dashboard.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_assets() {
		$blocks_dir        = RAMP_PLUGIN_DIR . '/build/';
		$blocks_asset_file = include $blocks_dir . 'index.asset.php';

		// Replace "wp-blockEditor" with "wp-block-editor".
		$blocks_asset_file['dependencies'] = array_replace(
			$blocks_asset_file['dependencies'],
			array_fill_keys(
				array_keys( $blocks_asset_file['dependencies'], 'wp-blockEditor', true ),
				'wp-block-editor'
			)
		);

		wp_enqueue_script(
			'ramp-blocks',
			RAMP_PLUGIN_URL . '/build/index.js',
			$blocks_asset_file['dependencies'],
			$blocks_asset_file['version'],
			true
		);

		$inline_settings = [
			'dkpdfIsEnabled' => class_exists( '\\DKPDF' ),
		];

		wp_add_inline_script(
			'ramp-blocks',
			'const RAMPBlocks = ' . wp_json_encode( $inline_settings ),
			'before'
		);
	}

	/**
	 * Enqueues assets on the front end.
	 */
	public function enqueue_block_assets_frontend() {
		$blocks_dir        = RAMP_PLUGIN_DIR . '/build/';
		$blocks_asset_file = include $blocks_dir . 'index.asset.php';

		// Replace "wp-blockEditor" with "wp-block-editor".
		$blocks_asset_file['dependencies'] = array_replace(
			$blocks_asset_file['dependencies'],
			array_fill_keys(
				array_keys( $blocks_asset_file['dependencies'], 'wp-blockEditor', true ),
				'wp-block-editor'
			)
		);

		wp_enqueue_style(
			'ramp-blocks',
			RAMP_PLUGIN_URL . '/build/frontend.css',
			[],
			$blocks_asset_file['version']
		);

		// @todo - Can't get viewScript to work properly for targeted enqueuing.
		wp_enqueue_script(
			'ramp-changelog',
			RAMP_PLUGIN_URL . '/assets/js/changelog.js',
			[],
			RAMP_VER,
			true
		);

		wp_localize_script(
			'ramp-changelog',
			'RAMPChangelog',
			[
				// translators: Changelog last updated date
				'lastUpdated' => __( '(Last Updated: %s)', 'research-amp' ),
				'showFullLog' => __( 'Show full log', 'research-amp' ),
			]
		);
	}

	/**
	 * Loads JS for hiding empty blocks.
	 *
	 * While teaser blocks are rendered on the server, and thus can be disabled when there
	 * are no items to show, we can't hide larger "blocks" (ie, item-type-block patterns,
	 * or "content blades") in the same way, since they're stored in HTML templates. Thus
	 * we hide them dynamically.
	 */
	public function load_empty_block_js() {
		?>
		<script type="text/javascript">
		document.addEventListener( 'DOMContentLoaded', (event) => {
			var itemTypeBlocks = document.querySelectorAll('.item-type-block');
			itemTypeBlocks.forEach( function( itemTypeBlock ) {
				var blockHasItems = itemTypeBlock.querySelector('.item-type-list')?.querySelectorAll('li').length > 0;
				if ( ! blockHasItems ) {
					itemTypeBlock.hidden = true;
				}
			} )

			// Also hide empty Funders.
			const fundersBlock = document.querySelector( '.footer-funders > .wp-block-group' )
			if ( fundersBlock ) {
				const fundersBlockChildren = fundersBlock.childNodes

				let hideFundersBlock = false
				if ( ! fundersBlockChildren.length ) {
					hideFundersBlock = true
				} else if ( 1 === fundersBlockChildren.length ) {
					hideFundersBlock = 0 === fundersBlockChildren[0].innerHTML.length
				}

				if ( hideFundersBlock ) {
					fundersBlock.closest( '.footer-funders' ).hidden = true
				}
			}
		} )
		</script>
		<?php
	}

	/**
	 * Register the RAMP block category.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $categories
	 * @param WP_Post $post       Current post object.
	 */
	public function register_block_category( $categories ) {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'research-amp',
					'title' => esc_html__( 'Research AMP', 'research-amp' ),
				],
			]
		);
	}

	public function register_server_side_rendered_blocks() {
		$block_types = [
			// Teaser blocks
			'article-teasers',
			'citation-teasers',
			'news-item-teasers',
			'profile-teasers',
			'research-review-teasers',
			'research-topic-teasers',

			// Miscellaneous
			'citation-info',
			'citation-links',
			'citation-library-count',
			'citation-library-filters',
			'cite-this',
			'focus-tag-content',
			'homepage-slides',
			'item-byline',
			'item-research-topics',
			'item-type-label',
			'nav-search',
			'profile-directory-filters',
			'profile-types',
			'search-form',
			'search-load-more',
			'search-result-teaser',
			'search-results-count',
			'social-buttons',
			'suggested-items',
			'the-events-calendar',
		];

		if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
			$block_types[] = 'event-teasers';
		}

		if ( class_exists( 'ezTOC' ) ) {
			$block_types[] = 'table-of-contents';
		}

		$blocks_dir        = RAMP_PLUGIN_DIR . '/build/';
		$blocks_asset_file = include $blocks_dir . 'index.asset.php';

		foreach ( $block_types as $block_type ) {
			$block_file = RAMP_PLUGIN_DIR . 'inc/block-types/' . $block_type . '.php';

			register_block_type_from_metadata(
				RAMP_PLUGIN_DIR . 'assets/src/blocks/' . $block_type . '/block.json',
				require $block_file
			);
		}
	}

	public function save_profile_data_from_blocks( $post ) {
		$post = get_post( $post );

		if ( 'ramp_profile' !== $post->post_type ) {
			return;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( empty( $blocks ) ) {
			return;
		}

		$profile_data = [];
		foreach ( $blocks as $block ) {
			$block_profile_data = $this->recurse_cb_for_profile_data( $block );

			$profile_data = array_merge( $profile_data, $block_profile_data );
		}

		foreach ( $profile_data as $meta_key => $meta_value ) {
			update_post_meta( $post->ID, $meta_key, $meta_value );
		}
	}

	protected function recurse_cb_for_profile_data( $block, $profile_data = [] ) {
		$block_map = [
			'research-amp/profile-title-institution' => [
				'meta_key' => 'ramp_profile_title_institution',
				'callback' => [ __CLASS__, 'get_meta_value_from_content_attribute' ],
			],
			'research-amp/profile-vital-link'        => [
				'callback' => [ __CLASS__, 'get_meta_value_from_vital_link_attributes' ],
			],
		];

		$block_name = $block['blockName'];
		if ( isset( $block_map[ $block_name ] ) ) {
			$processed = call_user_func( $block_map[ $block_name ]['callback'], $block );
			if ( $processed ) {
				if ( isset( $processed['key'] ) ) {
					$meta_key = $processed['key'];
				} elseif ( isset( $block_map[ $block_name ]['meta_key'] ) ) {
					$meta_key = $block_map[ $block_name ]['meta_key'];
				} else {
					return;
				}

				$profile_data[ $meta_key ] = $processed['value'];
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $inner_block ) {
				$inner_block_profile_data = $this->recurse_cb_for_profile_data( $inner_block, $profile_data );

				$profile_data = array_merge( $profile_data, $inner_block_profile_data );
			}
		}

		return $profile_data;
	}

	public static function get_meta_value_from_rendered_block( $block ) {
		return [
			'value' => trim( render_block( $block ) ),
		];
	}

	public static function get_meta_value_from_content_attribute( $block ) {
		return [
			'value' => ! empty( $block['attrs']['content'] ) ? $block['attrs']['content'] : '',
		];
	}

	public static function get_meta_value_from_vital_link_attributes( $block ) {
		if ( ! isset( $block['attrs']['vitalType'] ) ) {
			return null;
		}

		return [
			'key'   => 'ramp_profile_vital_' . $block['attrs']['vitalType'],
			'value' => ! empty( $block['attrs']['value'] ) ? $block['attrs']['value'] : '',
		];
	}

	public static function get_content_mode_settings_from_template_args( $args ) {
		$content_mode = isset( $args['contentMode'] ) ? $args['contentMode'] : 'auto';

		$retval = [
			'mode'              => $content_mode,
			'research_topic_id' => 0,
			'profile_id'        => 0,
		];

		$r = array_merge(
			[
				'contentModeProfileId'       => 0,
				'contentModeResearchTopicId' => 0,
			],
			$args
		);

		switch ( $content_mode ) {
			case 'all' :
				$retval['research_topic_id'] = 0;
				$retval['profile_id']        = 0;
			break;

			case 'advanced' :
				$retval['research_topic_id'] = (int) $r['contentModeResearchTopicId'];
				$retval['profile_id']        = (int) $r['contentModeProfileId'];
			break;

			case 'featured' :
				$retval['research_topic_id'] = 0;
				$retval['profile_id']        = 0;
			break;

			case 'auto' :
			default :
				// For preview, we fall onto the most recent Research Topic.
				if ( ! empty( $args['isEditMode'] ) ) {
					$retval['research_topic_id'] = ramp_get_most_recent_research_topic_id();
				} elseif ( is_singular( 'ramp_topic' ) ) {
					$retval['research_topic_id'] = get_queried_object_id();
				}

				if ( is_singular( 'ramp_profile' ) ) {
					$retval['profile_id'] = get_queried_object_id();
				}
			break;
		}

		return $retval;
	}

	public static function get_content_mode_tax_query_from_template_args( $args ) {
		$content_mode_settings = self::get_content_mode_settings_from_template_args( $args );

		$tax_query = [];
		switch ( $content_mode_settings['mode'] ) {
			case 'auto' :
			case 'advanced' :
				if ( ! empty( $content_mode_settings['research_topic_id'] ) ) {
					$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );

					$tax_query[] = [
						'taxonomy' => 'ramp_assoc_topic',
						'terms'    => [ $rt_map->get_term_id_for_post_id( $content_mode_settings['research_topic_id'] ) ],
						'field'    => 'term_id',
					];
				}

				if ( ! empty( $content_mode_settings['profile_id'] ) ) {
					$p_map = ramp_app()->get_cpttax_map( 'profile' );

					$tax_query[] = [
						'taxonomy' => 'ramp_assoc_profile',
						'terms'    => [ $p_map->get_term_id_for_post_id( $content_mode_settings['profile_id'] ) ],
						'field'    => 'term_id',
					];
				}
			break;
		}

		return $tax_query;
	}

	/**
	 * Get the research topic ID from the args passed to the template.
	 *
	 * @param array $args
	 * @return int|null
	 */
	public static function get_research_topic_from_template_args( $args ) {
		$research_topic_id = null;
		if ( isset( $args['researchTopic'] ) ) {
			if ( 'auto' === $args['researchTopic'] ) {
				if ( ! empty( $args['isEditMode'] ) ) {
					$research_topic_id = ramp_get_most_recent_research_topic_id();
				} elseif ( is_singular( 'ramp_topic' ) ) {
					$research_topic_id = get_queried_object_id();
				}
			} elseif ( 'all' !== $args['researchTopic'] ) {
				$research_topic_id = (int) $research_topic_id;
			}
		}

		return $research_topic_id;
	}

	public static function mirror_profile_vital_link_to_postmeta( $post ) {
		$post = get_post( $post );

		if ( 'ramp_profile' !== $post->post_type ) {
			return;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( empty( $blocks ) ) {
			return;
		}

		$vitals = [];

		foreach ( $blocks as $block ) {
			if ( 'research-amp/profile-vital-link' !== $block['blockName'] ) {
				continue;
			}

			if ( empty( $block['attrs']['value'] ) ) {
				continue;
			}

			$vital_type = $block['attrs']['vitalType'];
			$value      = $block['attrs']['value'];

			$vitals[ $vital_type ] = $value;
		}

		$map = [
			'email'   => 'ramp_vital_email',
			'twitter' => 'ramp_vital_twitter',
			'orcidId' => 'ramp_vital_orcid',
			'webpage' => 'ramp_vital_webpage',
		];

		foreach ( $vitals as $vital_key => $vital_value ) {
			if ( ! isset( $map[ $vital_key ] ) ) {
				continue;
			}

			update_post_meta( $post->ID, $map[ $vital_key ], $vital_value );
		}
	}

	public static function set_profile_fallback_image( $html ) {
		if ( $html ) {
			return $html;
		}

		if ( ! is_singular( 'ramp_profile' ) ) {
			return $html;
		}

		$html = sprintf(
			'<figure style="width:300px;background-image:url(%s);" class="wp-block-post-featured-image has-default-avatar"></figure>',
			esc_url( ramp_get_default_profile_avatar() )
		);

		return $html;
	}
}
