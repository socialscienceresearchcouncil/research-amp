<?php

$r = array_merge(
	[
		'id'                   => 0,
		'is_edit_mode'         => false,
		'show_item_type_label' => false,
		'show_research_topics' => false,
	],
	$args
);

$event_id = $r['id'];

$start_date = tribe_get_start_date( $event_id, false, 'F j, Y' );
$end_date   = tribe_get_end_date( $event_id, false, 'F j, Y' );
if ( $start_date === $end_date ) {
	$event_date = $start_date;
} else {
	$event_date = sprintf( '%s - %s', $start_date, $end_date );
}

$is_edit_mode = $r['is_edit_mode'];

$article_classes = [ 'teaser' ];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( $r['show_item_type_label'] ) : ?>
		<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'Event', 'ramp' ) ] ); ?>
	<?php endif; ?>

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

			<?php if ( $r['show_research_topics'] ) : ?>
				<?php
				ramp_get_template_part(
					'research-topic-tags',
					[
						'is_edit_mode' => $is_edit_mode,
						'item_id'      => $event_id,
					]
				);
				?>
			<?php endif; ?>
		</div>
	</div>
</article>
