<?php

return [
	'api_version'     => 2,
	'render_callback' => function( $atts, $content, $block ) {
		return ramp_render_block( 'nav-search', $atts, $content, $block );
	},
];
