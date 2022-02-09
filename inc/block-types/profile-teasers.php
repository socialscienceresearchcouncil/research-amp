<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'    => [
			'type'    => 'boolean',
			'default' => false,
		],
		'researchTopic' => [
			'type'    => 'string',
			'default' => 'all',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'profile-teasers', $atts );
	},
];
