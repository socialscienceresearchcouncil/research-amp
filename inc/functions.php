<?php

/**
 * Miscellaneous functions for use in templates.
 */

/**
 * Gets the default profile avatar image URL.
 *
 * @return string
 */
function ramp_get_default_profile_avatar() {
	/**
	 * Filters the URL of the default user avatar.
	 *
	 * @return string
	 */
	return apply_filters( 'ramp_default_profile_avatar', RAMP_PLUGIN_URL . 'assets/img/default-avatar.png' );
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
		'<!-- wp:research-amp/nav-search -->
		<div class="wp-block-research-amp-nav-search">
			<button class="nav-search-button">
				<span class="screen-reader-text">%s</span>
			</button>

			<div class="nav-search-fields">
				<form action="%s" method="get">
					<label class="screen-reader-text" for="nav-search-input">%s</label>
					<input name="s" type="text" id="nav-search-input" class="nav-search-input" />

					<input type="submit" class="search-submit" value="%s" />
				</form>
			</div>
		</div>
		<!-- /wp:research-amp/nav-search -->',
		esc_html__( 'Click to search site', 'research-amp' ),
		esc_url( home_url( '/' ) ),
		esc_html__( 'Search terms', 'research-amp' ),
		esc_html__( 'Submit', 'research-amp' )
	);
}
