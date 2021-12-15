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

		add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );

		add_action( 'init', [ $this, 'register_server_side_rendered_blocks' ] );
		add_action( 'init', [ $this, 'register_block_styles' ], 20 );
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
	}

	/**
	 * Register the RAMP block category.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $categories
	 * @param WP_Post $post       Current post object.
	 */
	public function register_block_category( $categories, $post ) {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'ramp',
					'title' => esc_html__( 'RAMP', 'ramp' ),
				],
			]
		);
	}

	public function register_server_side_rendered_blocks() {
		register_block_type_from_metadata(
			RAMP_PLUGIN_DIR . '/assets/src/blocks/research-topic-teasers/block.json',
			[
				'api_version'     => 1,
				'attributes'      => [
					'numberOfItems' => [
						'type'    => 'integer',
						'default' => 3,
					],
					'selectionType' => [
						'type'    => 'string',
						'default' => 'random',
					],
					'slot1'         => [
						'type'    => 'integer',
						'default' => 0,
					],
					'slot2'         => [
						'type'    => 'integer',
						'default' => 0,
					],
					'slot3'         => [
						'type'    => 'integer',
						'default' => 0,
					],
				],
				'render_callback' => [ $this, 'render_block_research_topic_teasers' ],
			]
		);

		register_block_type_from_metadata(
			RAMP_PLUGIN_DIR . '/assets/src/blocks/research-review-teasers/block.json',
			[
				'api_version'     => 1,
				'attributes'      => [
					'order'         => [
						'type'    => 'string',
						'default' => 'alphabetical',
					],
					'researchTopic' => [
						'type'    => 'string',
						'default' => 'auto',
					],
				],
				'render_callback' => [ $this, 'render_block_research_review_teasers' ],
			]
		);

		register_block_type_from_metadata(
			RAMP_PLUGIN_DIR . '/assets/src/blocks/article-teasers-with-featured-article/block.json',
			[
				'api_version'     => 1,
				'attributes'      => [
					'featuredArticleId' => [
						'type'    => 'integer',
						'default' => 0,
					],
				],
				'render_callback' => [ $this, 'render_block_article_teasers_with_featured_article' ],
			]
		);

		register_block_type_from_metadata(
			RAMP_PLUGIN_DIR . '/assets/src/blocks/research-topics-nav-submenu/block.json',
			[
				'api_version'     => 1,
				'attributes'      => [],
				'render_callback' => [ $this, 'render_block_research_topics_nav_submenu' ],
			]
		);
	}

	public function render_block_research_topic_teasers( $atts ) {
		return self::get_block_markup( 'research-topic-teasers', $atts );
	}

	public function render_block_research_review_teasers( $atts ) {
		return self::get_block_markup( 'research-review-teasers', $atts );
	}

	public function render_block_article_teasers_with_featured_article( $atts ) {
		return self::get_block_markup( 'article-teasers-with-featured-article', $atts );
	}

	public function render_block_research_topics_nav_submenu( $atts ) {
		return self::get_block_markup( 'research-topics-nav-submenu', $atts );
	}

	public static function get_block_markup( $block_type, $args = [] ) {
		ob_start();
		ramp_get_template_part( 'blocks/' . $block_type, $args );
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	public function register_block_styles() {
	}
}
