<?php

return [
	'api_version'     => 2,
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'citation-library-count', $atts );
	},
];
