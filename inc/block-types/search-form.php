<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'buttonText'        => [
			'type'    => 'string',
			'default' => __( 'Filter Results', 'ramp' ),
		],
		'label'             => [
			'type'    => 'string',
			'default' => __( 'Search Results for', 'ramp' ),
		],
		'placeholder'       => [
			'type'    => 'string',
			'default' => __( 'Enter search terms', 'ramp' ),
		],
		'typeSelectorLabel' => [
			'type'    => 'string',
			'default' => __( 'Filter by content type:', 'ramp' ),
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		return ramp_render_block( 'search-form', $atts, $content, $block );
	},
];
