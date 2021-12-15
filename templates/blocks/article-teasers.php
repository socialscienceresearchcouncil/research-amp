<?php

$research_topic_id = isset( $args['researchTopic'] ) ? $args['researchTopic'] : 'auto';
_b( $args );
if ( 'auto' === $research_topic_id ) {
	if ( ! empty( $args['isEditMode'] ) ) {
		$research_topic_id = ramp_get_most_recent_research_topic_id();
	} else {
		$research_topic_id = get_queried_object_id();
	}
} else {
	$research_topic_id = (int) $research_topic_id;
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
