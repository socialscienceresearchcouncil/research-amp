<?php

$r = array_merge(
	[
		'contentMode'         => 'auto',
		'numberOfItems'       => 3,
		'order'               => 'alphabetical',
		'showPublicationDate' => true,
		'variationType'       => 'grid',
	],
	$args
);

$number_of_items = (int) $r['numberOfItems'];

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

$order_args = [ 'alphabetical', 'latest', 'random' ];
$order_arg  = in_array( $r['order'], $order_args, true ) ? $r['order'] : 'alphabetical';

$variation_type = 'list' === $r['variationType'] ? 'list' : 'grid';

$show_publication_date = (bool) $r['showPublicationDate'];

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

	$research_reviews = $rt->get_research_reviews( $post_args );
} else {
	$query_args = array_merge(
		$post_args,
		[
			'post_type' => 'ramp_review',
		]
	);

	$research_reviews = get_posts( $query_args );
}

$list_classes = [
	'item-type-list',
	'item-type-list-research-reviews',
	'research-reviews-' . $variation_type,
];

if ( 'grid' === $variation_type ) {
	$list_classes[] = 'item-type-list-flex';
	$list_classes[] = 'item-type-list-3';
}
?>

<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
	<?php foreach ( $research_reviews as $research_review ) : ?>
		<li>
			<?php
			ramp_get_template_part(
				'teasers/research-review',
				[
					'id'                    => $research_review->ID,
					'show_publication_date' => $show_publication_date,
					'show_research_topics'  => empty( $research_topic_id ),
				]
			);
			?>
		</li>
	<?php endforeach; ?>
</ul>
