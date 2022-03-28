<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode'  => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		wp_enqueue_script( 'ramp-sidebar' );
		return ramp_render_block( 'social-buttons', $atts, $content, $block );
	},
];
