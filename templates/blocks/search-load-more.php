<?php

// Because WP_Query reports paged and not offset, we use a trick to generate the next page of results.
$wp_paged = get_query_var( 'paged' );
$new_page = $wp_paged ? $wp_paged : 1;

$url_base = add_query_arg( 's', \SSRC\RAMP\Search::get_requested_search_term(), home_url( '/' ) );

$requested_search_type = \SSRC\RAMP\Search::get_requested_search_type();
if ( $requested_search_type ) {
	$url_base = add_query_arg( 'search-type', $requested_search_type, $url_base );
}

$has_more_pages = $new_page < $GLOBALS['wp_query']->found_posts;

?>

<?php if ( $has_more_pages ) : ?>
	<div class="wp-block-research-amp-search-load-more">
		<?php
		ramp_get_template_part(
			'load-more-button',
			[
				'url_base'        => $url_base,
				'is_edit_mode'    => ! empty( $args['is_edit_mode'] ),
				'offset'          => $new_page,
				'query_var'       => 'paged',
				'number_of_items' => 1,
			]
		);
		?>
	</div>
<?php endif; ?>
