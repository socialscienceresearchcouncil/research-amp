<?php
$event_id = $args['id'];

$start_date = tribe_get_start_date( $event_id, false, 'F j, Y' );
$end_date   = tribe_get_end_date( $event_id, false, 'F j, Y' );
if ( $start_date === $end_date ) {
	$event_date = $start_date;
} else {
	$event_date = sprintf( '%s - %s', $start_date, $end_date );
}

$is_edit_mode = ! empty( $args['is_edit_mode'] );

$article_classes = [ 'teaser' ];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content event-teaser-content">
		<div class="event-teaser-icon">
			<img role="presentation" src="<?php echo esc_url( RAMP_PLUGIN_URL . '/assets/img/event.svg' ); ?>" />
		</div>

		<div class="event-teaser-inner-content">
			<h3 class="has-h-5-font-size item-title event-item-title">
				<?php if ( ! $is_edit_mode ) : ?>
					<a href="<?php echo esc_attr( get_permalink( $event_id ) ); ?>">
				<?php endif; ?>

				<?php echo esc_html( get_the_title( $event_id ) ); ?>

				<?php if ( ! $is_edit_mode ) : ?>
					</a>
				<?php endif; ?>
			</h3>

			<div class="event-meta"><?php echo esc_html( $event_date ); ?></div>
		</div>
	</div>
</article>
