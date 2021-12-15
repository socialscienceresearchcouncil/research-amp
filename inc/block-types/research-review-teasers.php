<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'isEditMode' => [
			'type' => 'boolean',
			'default' => false,
		],
		'order'                 => [
			'type'    => 'string',
			'default' => 'alphabetical',
		],
		'researchTopic'         => [
			'type'    => 'string',
			'default' => 'auto',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'research-review-teasers', $atts );
	},
];
