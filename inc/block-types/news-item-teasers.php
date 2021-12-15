<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'isEditMode'  => [
			'type'    => 'boolean',
			'default' => false,
		],
		'researchTopic' => [
			'type'    => 'string',
			'default' => 'auto',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'news-item-teasers', $atts );
	},
];
