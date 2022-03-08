<?php

$research_topic_id = \SSRC\RAMP\Blocks::get_research_topic_from_template_args( $args );

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : 3;

$query_args = [
	'post_type'      => 'ramp_citation',
	'post_status'    => 'publish',
	'posts_per_page' => $number_of_items,
	'fields'         => 'ids',
	'tax_query'      => [],
];

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_rt = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;

$research_topics = get_posts(
	[
		'post_type'      => 'ramp_topic',
		'posts_per_page' => -1,
		'orderby'        => [ 'title' => 'ASC' ],
	]
);

if ( ! in_array( $requested_rt, wp_list_pluck( $research_topics, 'post_name' ), true ) ) {
	$requested_rt = null;
}

$requested_rt_term = null;
if ( $requested_rt ) {
	$requested_rt_term = get_term_by( 'slug', $requested_rt, 'ramp_assoc_topic' );

	$query_args['tax_query']['assoc_topic'] = [
		'taxonomy' => 'ramp_assoc_topic',
		'terms'    => $requested_rt_term->term_id,
		'field'    => 'term_id',
	];
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_search_term = isset( $_GET['search-term'] ) ? wp_unslash( $_GET['search-term'] ) : null;
if ( $requested_search_term ) {
	$query_args['s'] = $requested_search_term;
}

$citations = get_posts( $query_args );

?>

<div class="citation-teasers">
	<ul class="item-type-list item-type-list-citations">
		<?php foreach ( $citations as $citation ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/citation', [ 'id' => $citation ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
