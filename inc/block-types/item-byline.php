<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'          => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showPublicationDate' => [
			'type'    => 'boolean',
			'default' => true,
		],
	],
	'render_callback' => function ( $atts, $content, $block ) {
		return ramp_render_block( 'item-byline', $atts, $content, $block );
	},
];
