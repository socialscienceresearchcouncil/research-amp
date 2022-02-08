<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'isEditMode'    => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems' => [
			'type'    => 'string',
			'default' => '3',
		],
		'order'         => [
			'type'    => 'string',
			'default' => 'alphabetical',
		],
		'researchTopic' => [
			'type'    => 'string',
			'default' => 'auto',
		],
		'variationType' => [
			'enum'    => [ 'teasers', 'horizontal' ],
			'default' => 'teasers',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'research-review-teasers', $atts );
	},
];
