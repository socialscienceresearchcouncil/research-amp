<?php

/**
 * Miscellaneous functions for use in templates.
 */

function ramp_get_default_profile_avatar() {
	return RAMP_PLUGIN_URL . 'assets/img/default-avatar.png';
}

function ramp_locate_template( $template ) {
	$located = locate_template( $template );

	if ( ! $located ) {
		$located = RAMP_PLUGIN_DIR . 'templates/' . $template;
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

function ramp_render_block( $block_name, $atts, $content = '', $block = null ) {
	$template_args = array_merge(
		$atts,
		[
			'content' => $content,
			'block'   => $block,
		]
	);

	ob_start();
	ramp_get_template_part( 'blocks/' . $block_name, $template_args );
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

function ramp_get_placeholder_count( $query_count, $row_count ) {
	$placeholder_count = $row_count - ( $query_count % $row_count );

	// Account for modulo returning 0.
	if ( $placeholder_count === $row_count ) {
		$placeholder_count = 0;
	}

	return $placeholder_count;
}

function ramp_get_pag_offset( $query_var ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET[ $query_var ] ) ) {
		return 0;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return (int) $_GET[ $query_var ];
}

function ramp_get_default_nav_search_markup() {
	return sprintf(
		'<!-- wp:ramp/nav-search -->
		<div class="wp-block-ramp-nav-search">
			<button class="nav-search-button">
				<span class="screen-reader-text">%s</span>
			</button>

			<div class="nav-search-fields">
				<form action="" method="get">
					<label>
						<span class="screen-reader-text">%s</span>
						<input name="s" type="search" class="search-input" />
					</label>
				</form>
			</div>
		</div>
		<!-- /wp:ramp/nav-search -->',
		esc_html__( 'Click to search site', 'ramp-theme' ),
		esc_html__( 'Search terms', 'ramp-theme' )
	);
}
