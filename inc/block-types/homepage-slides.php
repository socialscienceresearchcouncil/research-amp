<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'isEditMode' => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'homepage-slides', $atts );
	},
];
