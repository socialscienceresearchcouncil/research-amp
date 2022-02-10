<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'       => [
			'type'    => 'boolean',
			'default' => false,
		],
		'featuredItemId'   => [
			'type'    => 'number',
			'default' => 0,
		],
		'researchTopic'    => [
			'type'    => 'string',
			'default' => 'auto',
		],
		'showFeaturedItem' => [
			'type'    => 'boolean',
			'default' => false,
		],
		'variationType'    => [
			'type'    => 'string',
			'enum'    => [ 'one', 'two' ],
			'default' => 'one',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'news-item-teasers', $atts );
	},
];
