<?php
$research_topics = get_posts(
	[
		'post_type'      => 'ssrc_restop_pt',
		'posts_per_page' => 3,
		'fields'         => 'ids',
	]
);
?>

<ul class="item-type-list item-type-list-flex item-type-list-research-topics">
	<?php foreach ( $research_topics as $research_topic ) : ?>
		<li>
			<?php ramp_get_template_part( 'teasers/research-topic', [ 'id' => $research_topic ] ); ?>
		</li>
	<?php endforeach; ?>
</ul>
