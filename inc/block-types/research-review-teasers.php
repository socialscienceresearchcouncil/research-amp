<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'contentMode'    => [
			'type'    => 'string',
			'enum'    => [ 'auto', 'all', 'advanced' ],
			'default' => 'auto',
		],
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
			'type'    => 'string', /* WP throws PHP notice if this is not set */
			'enum'    => [ 'teasers', 'horizontal' ],
			'default' => 'teasers',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'research-review-teasers', $atts );
	},
];
