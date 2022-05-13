<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'buttonText'        => [
			'type'    => 'string',
			'default' => __( 'Filter Results', 'research-amp' ),
		],
		'label'             => [
			'type'    => 'string',
			'default' => __( 'Search Results for', 'research-amp' ),
		],
		'placeholder'       => [
			'type'    => 'string',
			'default' => __( 'Enter search terms', 'research-amp' ),
		],
		'typeSelectorLabel' => [
			'type'    => 'string',
			'default' => __( 'Filter by content type:', 'research-amp' ),
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		return ramp_render_block( 'search-form', $atts, $content, $block );
	},
];
