<?php

global $wp_query;

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : 3;

$offset_query_var = 'profile-pag-offset';
$offset = (int) $wp_query->get( $offset_query_var );

$research_topic_id = null;
if ( 'auto' === $args['researchTopic'] ) {
	if ( ! empty( $args['isEditMode'] ) ) {
		$research_topic_id = ramp_get_most_recent_research_topic_id();
	} else {
		$research_topic_id = get_queried_object_id();
	}
} elseif ( 'all' !== $args['researchTopic'] ) {
	$research_topic_id = (int) $research_topic_id;
}

$query_args = [
	'post_type'      => 'ramp_profile',
	// phpcs:disable Squiz.PHP.CommentedOutCode.Found
	/*
	'post__in'       => $featured_ids,
	'orderby'        => 'post__in',
	*/
	'offset'         => $offset,
	'posts_per_page' => $number_of_items,
	'orderby'        => 'RAND',
];

if ( $research_topic_id ) {
	$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );

	$query_args['tax_query'] = [
		'research_topic' => [
			'taxonomy' => 'ramp_assoc_topic',
			'terms'    => $rt_map->get_term_id_for_post_id( $research_topic_id ),
			'field'    => 'term_id',
		],
	];
}

$profile_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $profile_query->found_posts;

?>

<div class="profile-teasers load-more-container">
	<ul class="item-type-list item-type-list-flex item-type-list-4 item-type-list-profiles load-more-list">
		<?php foreach ( $profile_query->posts as $profile_post ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/profile', [ 'id' => $profile_post->ID ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( ! empty( $args['showLoadMore'] ) && $has_more_pages ) : ?>
		<?php
		ramp_get_template_part(
			'load-more-button',
			[
				'offset'          => $offset,
				'query_var'       => $offset_query_var,
				'number_of_items' => $number_of_items,
			]
		);
		?>
	<?php endif; ?>
</div>
