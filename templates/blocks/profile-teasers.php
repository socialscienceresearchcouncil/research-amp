<?php

global $wp_query;

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : 3;

$offset = $wp_query->get( 'pag-offset' );

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

global $wp;
$show_load_more = ! empty( $args['showLoadMore'] ) && ( ( $offset + $number_of_items ) <= $profile_query->found_posts );
if ( $show_load_more ) {
	$load_more_href = home_url( add_query_arg( [], $wp->request ) );
	$load_more_href = add_query_arg( 'pag-offset', $offset + $number_of_items, $load_more_href );

	wp_enqueue_script( 'ramp-load-more' );
}

?>

<div class="profile-teasers load-more-container">
	<ul class="item-type-list item-type-list-flex item-type-list-4 item-type-list-profiles load-more-list">
		<?php foreach ( $profile_query->posts as $profile_post ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/profile', [ 'id' => $profile_post->ID ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $show_load_more ) : ?>
		<div class="wp-block-button aligncenter is-style-primary load-more-button">
			<a href="<?php echo esc_url( $load_more_href ); ?>" class="wp-block-button__link"><?php esc_html_e( 'Load More', 'ramp' ); ?></a>
		</div>
	<?php endif; ?>
</div>
