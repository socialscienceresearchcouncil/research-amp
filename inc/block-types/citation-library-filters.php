<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'backgroundColor' => [
			'type' => 'string',
		],
		'textColor'       => [
			'type' => 'string',
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		return ramp_render_block( 'citation-library-filters', $atts );
	},
];
