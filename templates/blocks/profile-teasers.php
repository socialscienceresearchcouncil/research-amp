<?php

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
	'post_type'      => 'ssrc_schprof_pt',
	/*'post__in'       => $featured_ids,
	'orderby'        => 'post__in',*/
	'posts_per_page' => 4,
	'orderby'        => 'RAND',
];

if ( $research_topic_id ) {
	$rt_map = disinfo_app()->get_cpttax_map( 'research_topic' );

	$query_args['tax_query'] = [
		'research_topic' => [
			'taxonomy' => 'ssrc_research_topic',
			'terms'    => $rt_map->get_term_id_for_post_id( $research_topic_id ),
			'field'    => 'term_id',
		],
	];
}

$profile_posts = get_posts( $query_args );

?>

<div class="profile-teasers">
	<ul class="item-type-list item-type-list-flex item-type-list-4 item-type-list-profiles">
		<?php foreach ( $profile_posts as $profile_post ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/profile', [ 'id' => $profile_post->ID ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
