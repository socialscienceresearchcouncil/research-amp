<?php

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : -1;

$research_topic_id = \SSRC\RAMP\Blocks::get_research_topic_from_template_args( $args );

$event_query_args = [
	'posts_per_page' => $number_events,
	'start_date'     => gmdate( 'Y-m-d', time() - DAY_IN_SECONDS ),
];

if ( $research_topic_id ) {
	$rt_map     = ramp_app()->get_cpttax_map( 'research_topic' );
	$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic_id );

	$query_args['tax_query'] = [
		[
			'taxonomy' => 'ramp_assoc_topic',
			'terms'    => $rt_term_id,
			'field'    => 'term_id',
		],
	];
}

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

<ul class="item-type-list item-type-list-research-reviews item-type-list-flex">
	<?php foreach ( $event_query->posts as $event ) : ?>
		<li>
			<?php
			ramp_get_template_part(
				'teasers/event',
				[
					'id' => $event->ID,
				]
			);
			?>
		</li>
	<?php endforeach; ?>
</ul>
