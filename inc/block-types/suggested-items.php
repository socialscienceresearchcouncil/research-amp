<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'headingText'   => [
			'type'    => 'string',
			'default' => '',
		],
		'itemType'      => [
			'type'    => 'string',
			'default' => '',
			'enum'    => [ 'article', 'news-item' ],
		],
		'isEditMode'    => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems' => [
			'type'    => 'integer',
			'default' => 3,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		return ramp_render_block( 'suggested-items', $atts, $content, $block );
	},
];
