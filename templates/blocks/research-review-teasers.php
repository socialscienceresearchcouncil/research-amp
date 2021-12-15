<?php

$research_topic_id = isset( $args['researchTopic'] ) ? $args['researchTopic'] : 'auto';
if ( 'auto' === $research_topic_id ) {
	if ( wp_is_json_request() ) {
		// This is FSE. Pick a random item.
		$research_topic_id = 29;
	} else {
		$research_topic_id = get_queried_object_id();
	}
} else {
	$research_topic_id = (int) $research_topic_id;
}

$order = isset( $args['order'] ) ? $args['order'] : 'alphabetical';
if ( ! in_array( $order, [ 'alphabetical', 'latest', 'random' ], true ) ) {
	$order = 'alphabetical';
}

$rt = \SSRC\RAMP\ResearchTopic::get_instance( $research_topic_id );

$post_args = [];

switch ( $order ) {
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

$research_reviews = $rt->get_literature_reviews( $post_args );

?>

<ul class="item-type-list item-type-list-flex item-type-list-research-reviews">
	<?php foreach ( $research_reviews as $research_review ) : ?>
		<li>
			<?php ramp_get_template_part( 'teasers/research-review', [ 'id' => $research_review->ID ] ); ?>
		</li>
	<?php endforeach; ?>
</ul>
