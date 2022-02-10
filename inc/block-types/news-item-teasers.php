<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'     => [
			'type'    => 'boolean',
			'default' => false,
		],
		'featuredItemId' => [
			'type'    => 'string',
			'default' => '',
		],
		'researchTopic'  => [
			'type'    => 'string',
			'default' => 'auto',
		],
		'variationType'  => [
			'type'    => 'string',
			'enum'    => [ 'single', 'two', 'three' ],
			'default' => 'single',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'news-item-teasers', $atts );
	},
];
