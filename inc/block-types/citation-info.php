<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'          => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		return ramp_render_block( 'citation-info', $atts, $content, $block );
	},
];
