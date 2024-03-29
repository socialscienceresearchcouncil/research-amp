<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode' => [
			'type'    => 'boolean',
			'default' => false,
		],
		'postId'     => [
			'type'    => 'integer',
			'default' => 0,
		],
	],
	'render_callback' => function ( $atts ) {
		return ramp_render_block( 'search-result-teaser', $atts );
	},
];
