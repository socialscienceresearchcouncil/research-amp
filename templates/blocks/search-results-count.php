<?php

$total_count = isset( $GLOBALS['wp_query']->found_posts ) ? $GLOBALS['wp_query']->found_posts : 0;

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$requested_search_term = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$requested_type        = isset( $_GET['search-type'] ) ? sanitize_text_field( wp_unslash( $_GET['search-type'] ) ) : '';
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$types = ramp_get_search_item_types();
if ( isset( $types[ $requested_type ] ) ) {
	$type_label = $types[ $requested_type ];
} else {
	$type_label = __( 'All content types', 'ramp' );
}

if ( $requested_search_term ) {
	$count_text = sprintf(
		// translators: 1. Results count, 2. Search term, 3. Content type
		_n(
			'%1$s Result for "%2$s" in "%3$s"',
			'%1$s Results for "%2$s" in "%3$s"',
			$total_count,
			'ramp'
		),
		number_format_i18n( $total_count ),
		$requested_search_term,
		$type_label
	);
} else {
	$count_text = sprintf(
		// translators: 1. Results count, 2. Content type
		_n(
			'%1$s Result in "%2$s"',
			'%1$s Results in "%2$s"',
			$total_count,
			'ramp'
		),
		number_format_i18n( $total_count ),
		$type_label
	);

}


?>

<div class="wp-block-ramp-search-results-count">
	<?php echo esc_html( $count_text ); ?>
</div>
