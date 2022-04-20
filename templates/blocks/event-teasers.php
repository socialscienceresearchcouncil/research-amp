<?php

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeProfileId'       => 0,
		'contentModeResearchTopicId' => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'variationType'              => 'grid',
	],
	$args
);

$number_of_items = (int) $r['numberOfItems'];

$variation_type = in_array( $r['variationType'], [ 'grid', 'list' ], true ) ? $r['variationType'] : 'grid';

$event_query_args = [
	'posts_per_page' => $number_of_items,
	'start_date'     => gmdate( 'Y-m-d', time() - DAY_IN_SECONDS ),
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

// This is how we customize the orderby.
$orderby_cb = function() {
	remove_filter( 'posts_orderby', array( 'Tribe__Events__Query', 'posts_orderby' ), 10, 2 );
};
add_action( 'tribe_events_pre_get_posts', $orderby_cb );
$event_query = tribe_get_events(
	$event_query_args,
	true // get the full query object
);
remove_action( 'tribe_events_pre_get_posts', $orderby_cb );

$list_classes = [
	'item-type-list',
	'item-type-list-events',
];

if ( 'grid' === $variation_type ) {
	$list_classes[] = 'item-type-list-flex';
	$list_classes[] = 'item-type-list-3';
};

?>

<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
	<?php if ( ! empty( $event_query->posts ) || ! $r['isEditMode'] ) : ?>
		<?php foreach ( $event_query->posts as $event ) : ?>
			<li>
				<?php
				ramp_get_template_part(
					'teasers/event',
					[
						'id'           => $event->ID,
						'is_edit_mode' => ! empty( $r['isEditMode'] ),
					]
				);
				?>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<?php ramp_get_template_part( 'teasers-no-content' ); ?>
	<?php endif; ?>
</ul>
