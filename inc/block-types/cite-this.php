<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'headingText' => [
			'type'    => 'string',
			'default' => __( 'Cite This', 'research-amp' ),
		],
		'helpText'    => [
			'type'    => 'string',
			'default' => '',
		],
		'isEditMode'  => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		wp_enqueue_script( 'ramp-sidebar' );
		return ramp_render_block( 'cite-this', $atts, $content, $block );
	},
];
