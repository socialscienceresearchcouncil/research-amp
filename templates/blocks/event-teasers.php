<?php

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeProfileId'       => 0,
		'contentModeResearchTopicId' => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
	],
	$args
);

$number_of_items = (int) $args['numberOfItems'];

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

?>

<ul class="item-type-list item-type-list-events item-type-list-flex item-type-list-3">
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
</ul>
