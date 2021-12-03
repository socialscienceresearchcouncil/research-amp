<?php

/**
 * Miscellaneous functions for use in templates.
 */

function ramp_locate_template( $template ) {
	$located = locate_template( $template );

	if ( ! $located ) {
		$located = RAMP_PLUGIN_DIR . '/templates/' . $template;
	}

	return $located;
}

function ramp_get_template_part( $template, $args ) {
	$template_name = $template . '.php';
	$located       = ramp_locate_template( $template_name );

	if ( ! $located ) {
		return;
	}

	load_template( $located, false, $args );
}
