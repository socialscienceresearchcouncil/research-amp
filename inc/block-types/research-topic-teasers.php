<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'numberOfItems' => [
			'type'    => 'integer',
			'default' => 3,
		],
		'selectionType' => [
			'type'    => 'string',
			'default' => 'random',
		],
		'slot1'         => [
			'type'    => 'integer',
			'default' => 0,
		],
		'slot2'         => [
			'type'    => 'integer',
			'default' => 0,
		],
		'slot3'         => [
			'type'    => 'integer',
			'default' => 0,
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'research-topic-teasers', $atts );
	},
];
