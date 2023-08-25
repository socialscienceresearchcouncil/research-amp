<?php

return [
	'api_version'     => 2,
	'render_callback' => function ( $atts ) {
		return ramp_render_block( 'the-events-calendar', $atts );
	},
];
