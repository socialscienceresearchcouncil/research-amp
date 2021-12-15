<?php

if ( isset( $args['researchTopic'] ) && is_numeric( $args['researchTopic'] ) ) {
	$research_topic_id = intval( $args['researchTopic'] );
} else {
	if ( wp_is_json_request() ) {
		// This is FSE. Pick a random item.
		$research_topic_id = 29;
	} else {
		$research_topic_id = get_queried_object_id();
	}
}

$rt_map     = disinfo_app()->get_cpttax_map( 'research_topic' );
$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic_id );

$news_items = get_posts(
	[
		'post_type'     => 'post',
		'post_status'   => 'publish',
		'tax_query'     => [
			[
				'taxonomy' => 'ssrc_research_topic',
				'terms' => $rt_term_id,
				'field' => 'term_id',
			]
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
