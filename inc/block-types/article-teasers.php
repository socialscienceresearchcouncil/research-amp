<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'featuredItemId'   => [
			'type'    => 'number',
			'default' => 0,
		],
		'isEditMode'       => [
			'type'    => 'boolean',
			'default' => false,
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
			'enum'    => [ 'grid', 'columns' ],
			'default' => 'grid',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'article-teasers', $atts );
	},
];
