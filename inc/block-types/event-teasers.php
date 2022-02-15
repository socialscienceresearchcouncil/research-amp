<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'    => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems' => [
			'type'    => 'string',
			'default' => '3',
		],
		'researchTopic' => [
			'type'    => 'string',
			'default' => 'auto',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'event-teasers', $atts );
	},
];
