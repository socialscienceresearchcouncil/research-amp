<?php

$offset          = isset( $args['offset'] ) ? (int) $args['offset'] : 0;
$number_of_items = (int) $args['number_of_items'];
$query_var       = $args['query_var'];
$is_edit_mode    = ! empty( $args['is_edit_mode'] );

global $wp;
$load_more_href = trailingslashit( home_url( add_query_arg( [], $wp->request ) ) );
$load_more_href = add_query_arg( $query_var, $offset + $number_of_items, $load_more_href );

wp_enqueue_script( 'ramp-load-more' );

?>

<div class="wp-block-button aligncenter is-style-secondary load-more-button">
	<?php if ( ! $is_edit_mode ) : ?>
		<a href="<?php echo esc_url( $load_more_href ); ?>" class="wp-block-button__link" data-query-arg="<?php echo esc_attr( $query_var ); ?>">
	<?php else : ?>
		<span class="wp-block-button__link">
	<?php endif; ?>

	<?php esc_html_e( 'Load More', 'ramp' ); ?>

	<?php if ( ! $is_edit_mode ) : ?>
		</a>
	<?php else : ?>
		</span>
	<?php endif; ?>
</div>

