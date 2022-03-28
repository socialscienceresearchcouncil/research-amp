<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'headingText' => [
			'type'    => 'string',
			'default' => __( 'Table of Contents', 'ramp' ),
		],
		'isEditMode'  => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		wp_enqueue_script( 'ramp-sidebar' );
		/*
		wp_enqueue_script(
			'ramp-review-version-selector-frontend',
			RAMP_PLUGIN_URL . '/assets/js/review-version-selector.js',
			[ 'ramp-select2', 'jquery' ],
			RAMP_VER,
			true
		);

		wp_enqueue_style( 'ramp-select2' );
*/

		return ramp_render_block( 'table-of-contents', $atts, $content, $block );
	},
];
