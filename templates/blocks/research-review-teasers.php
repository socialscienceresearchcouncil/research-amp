<?php

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : -1;

$research_topic_id = isset( $args['researchTopic'] ) ? $args['researchTopic'] : 'auto';
if ( 'auto' === $research_topic_id ) {
	if ( ! empty( $args['isEditMode'] ) ) {
		$research_topic_id = ramp_get_most_recent_research_topic_id();
	} else {
		$research_topic_id = get_queried_object_id();
	}
} elseif ( 'all' === $research_topic_id ) {
	$research_topic_id = null;
} else {
	$research_topic_id = (int) $research_topic_id;
}

$order_arg = isset( $args['order'] ) ? $args['order'] : 'alphabetical';
if ( ! in_array( $order_arg, [ 'alphabetical', 'latest', 'random' ], true ) ) {
	$order_arg = 'alphabetical';
}

if ( isset( $args['variationType'] ) && 'teasers' === $args['variationType'] ) {
	$variation_type = 'teasers';
} else {
	$variation_type = 'column';
}

$post_args = [
	'posts_per_page' => $number_of_items,
];

switch ( $order_arg ) {
	case 'alphabetical' :
		$post_args['orderby'] = [ 'title' => 'ASC' ];
	break;

	case 'latest' :
		$post_args['orderby'] = [ 'date' => 'DESC' ];
	break;

	case 'random' :
	default :
		$post_args['orderby'] = 'rand';
	break;
}

if ( $research_topic_id ) {
	$rt = \SSRC\RAMP\ResearchTopic::get_instance( $research_topic_id );

	$research_reviews = $rt->get_literature_reviews( $post_args );
} else {
	$query_args = array_merge(
		$post_args,
		[
			'post_type' => 'ssrc_lit_review',
		]
	);

	$research_reviews = get_posts( $query_args );
}

?>

<ul class="item-type-list item-type-list-research-reviews 
<?php
if ( 'teasers' === $variation_type ) :
	?>
	item-type-list-flex<?php endif; ?>">
	<?php foreach ( $research_reviews as $research_review ) : ?>
		<li>
			<?php
			ramp_get_template_part(
				'teasers/research-review',
				[
					'id'                   => $research_review->ID,
					'show_research_topics' => empty( $research_topic_id ),
				]
			);
			?>
		</li>
	<?php endforeach; ?>
</ul>
