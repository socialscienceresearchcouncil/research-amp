<?php

$research_topic_id = isset( $args['researchTopic'] ) ? $args['researchTopic'] : 'auto';
if ( 'auto' === $research_topic_id ) {
	if ( ! empty( $args['isEditMode'] ) ) {
		$research_topic_id = ramp_get_most_recent_research_topic_id();
	} else {
		$research_topic_id = get_queried_object_id();
	}
} else {
	$research_topic_id = (int) $research_topic_id;
}

$rt_map     = ramp_app()->get_cpttax_map( 'research_topic' );
$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic_id );

$news_items = get_posts(
	[
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'tax_query'      => [
			[
				'taxonomy' => 'ramp_assoc_topic',
				'terms'    => $rt_term_id,
				'field'    => 'term_id',
			],
		],
		'posts_per_page' => 3,
		'fields'         => 'ids',
	]
);

?>

<div class="article-teasers">
	<ul class="item-type-list item-type-list-flex item-type-list-3 item-type-list-articles">
		<?php foreach ( $news_items as $news_item ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/news-item', [ 'id' => $news_item ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
