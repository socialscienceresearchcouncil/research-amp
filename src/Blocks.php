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
		$block_types = [
			'article-teasers',
			'article-teasers-with-featured-article',
			'citation-teasers',
			'news-item-teasers',
			'research-review-teasers',
			'research-topic-teasers',
		];

		foreach ( $block_types as $block_type ) {
			$block_file = RAMP_PLUGIN_DIR . '/inc/block-types/' . $block_type . '.php';

			register_block_type_from_metadata(
				RAMP_PLUGIN_DIR . '/assets/src/blocks/' . $block_type . '/block.json',
				require $block_file
			);
		}
	}

	public function register_block_styles() {
	}
}
