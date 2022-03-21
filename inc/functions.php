<?php

/**
 * Miscellaneous functions for use in templates.
 */

function ramp_get_default_profile_avatar() {
	return RAMP_PLUGIN_URL . '/assets/img/default-avatar.png';
}

function ramp_locate_template( $template ) {
	$located = locate_template( $template );

	if ( ! $located ) {
		$located = RAMP_PLUGIN_DIR . '/templates/' . $template;
	}

	return $located;
}

function ramp_get_template_part( $template, $args = [] ) {
	$template_name = $template . '.php';
	$located       = ramp_locate_template( $template_name );

	if ( ! $located ) {
		return;
	}

	load_template( $located, false, $args );
}

function ramp_render_block( $block_name, $atts ) {
	ob_start();
	ramp_get_template_part( 'blocks/' . $block_name, $atts );
	$contents = ob_get_contents();
	ob_end_clean();

	return $contents;
}

function ramp_get_most_recent_research_topic_id() {
	$rts = get_posts(
		[
			'post_type'   => 'ramp_topic',
			'numberposts' => 1,
			'fields'      => 'ids',
		]
	);

	return $rts[0];
}
