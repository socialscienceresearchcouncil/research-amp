<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'isEditMode'    => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems' => [
			'type'    => 'number',
			'default' => 3,
		],
		'selectionType' => [
			'type'    => 'string',
			'default' => 'random',
		],
		'showLoadMore'  => [
			'type'    => 'boolean',
			'default' => false,
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
		'variationType' => [
			'type'    => 'string',
			'default' => 'grid',
			'enum'    => [ 'grid', 'list' ],
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'research-topic-teasers', $atts );
	},
];
