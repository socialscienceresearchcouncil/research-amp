<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'headingText' => [
			'type'    => 'string',
			'default' => __( 'Table of Contents', 'research-amp' ),
		],
		'isEditMode'  => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function ( $atts, $content, $block ) {
		wp_enqueue_script( 'ramp-sidebar' );
		return ramp_render_block( 'table-of-contents', $atts, $content, $block );
	},
];
