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
		<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'Event', 'research-amp' ) ] ); ?>
	<?php endif; ?>

	<div class="teaser-content event-teaser-content">
		<div class="event-teaser-icon">
			<svg viewBox="0 0 24 27" xmlns="http://www.w3.org/2000/svg">
				<path d="M18.6667 14.6667H12V21.3333H18.6667V14.6667ZM17.3333 0V2.66667H6.66667V0H4V2.66667H2.66667C2.31715 2.66684 1.97111 2.73603 1.6484 2.87027C1.32569 3.0045 1.03267 3.20115 0.786141 3.44891C0.539617 3.69667 0.344445 3.99068 0.211827 4.31406C0.0792081 4.63744 0.0117533 4.98382 0.0133333 5.33333L0 24C0 24.7072 0.280951 25.3855 0.781048 25.8856C1.28115 26.3857 1.95942 26.6667 2.66667 26.6667H21.3333C22.0399 26.6646 22.717 26.3829 23.2166 25.8833C23.7163 25.3836 23.9979 24.7066 24 24V5.33333C23.9979 4.62674 23.7163 3.94969 23.2166 3.45005C22.717 2.95041 22.0399 2.66878 21.3333 2.66667H20V0H17.3333ZM21.3333 24H2.66667V9.33333H21.3333V24Z" />
			</svg>
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
