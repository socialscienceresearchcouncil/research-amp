<?php

return [
	'api_version'     => 2,
	'attributes'      => [],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'citation-library-filters', $atts );
	},
];
