<?php

namespace SSRC\RAMP;

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
				array_keys( $blocks_asset_file['dependencies'], 'wp-blockEditor' ),
				'wp-block-editor'
			)
		);

		wp_enqueue_script(
			'ramp-blocks',
			RAMP_PLUGIN_URL . '/build/index.js',
			$blocks_asset_file['dependencies'],
			$blocks_asset_file['version']
		);

		wp_enqueue_style(
			'ramp-blocks',
			RAMP_PLUGIN_URL . '/build/index.css',
			[],
			$blocks_asset_file['version']
		);
	}

	/**
	 * Enqueues assets on the front end.
	 */
	public function enqueue_block_assets_frontend() {
		$blocks_dir = RAMP_PLUGIN_DIR . '/build/';
		$blocks_asset_file = include $blocks_dir . 'index.asset.php';

		// Replace "wp-blockEditor" with "wp-block-editor".
		$blocks_asset_file['dependencies'] = array_replace(
			$blocks_asset_file['dependencies'],
			array_fill_keys(
				array_keys( $blocks_asset_file['dependencies'], 'wp-blockEditor' ),
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
}
