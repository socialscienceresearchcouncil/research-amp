<?php

return [
	'api_version'     => 2,
	'attributes'      => [],
	'render_callback' => function ( $atts, $content, $block ) {
		return ramp_render_block( 'profile-types', $atts, $content, $block );
	},
];
