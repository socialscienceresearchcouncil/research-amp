<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'contentMode'         => [
			'type'    => 'string',
			'enum'    => [ 'auto', 'all', 'advanced' ],
			'default' => 'auto',
		],
		'featuredItemId'   => [
			'type'    => 'integer',
			'default' => 0,
		],
		'isEditMode'       => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems'       => [
			'type'    => 'string',
			'default' => '3',
		],
		'order'               => [
			'type'    => 'string',
			'default' => 'alphabetical',
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
			'enum'    => [ 'grid', 'home' ],
			'default' => 'grid',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'article-teasers', $atts );
	},
];
