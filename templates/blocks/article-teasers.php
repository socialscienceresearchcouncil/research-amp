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

$rt_obj = \SSRC\RAMP\ResearchTopic::get_instance( $research_topic_id );

$articles = $rt_obj->get_expert_reflections( [ 'posts_per_page' => 3 ] );

?>

<div class="article-teasers">
	<ul class="item-type-list item-type-list-flex item-type-list-3 item-type-list-articles">
		<?php foreach ( $articles as $article ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/article', [ 'id' => $article->ID ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
