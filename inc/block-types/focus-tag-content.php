<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'focusTag'     => [
			'type'    => 'string',
			'default' => '',
		],
		'showLoadMore' => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function ( $atts ) {
		return ramp_render_block( 'focus-tag-content', $atts );
	},
];
