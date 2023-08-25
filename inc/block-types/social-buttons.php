<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'altmetricsEnabled'   => [
			'type'    => 'boolean',
			'default' => true,
		],
		'altmetricsThreshold' => [
			'type'    => 'integer',
			'default' => 20,
		],
		'isEditMode'          => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function ( $atts, $content, $block ) {
		wp_enqueue_script( 'ramp-altmetrics' );
		wp_enqueue_script( 'ramp-sidebar' );
		return ramp_render_block( 'social-buttons', $atts, $content, $block );
	},
];
