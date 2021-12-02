<?php
$research_topics = get_posts(
	[
		'post_type'      => 'ssrc_restop_pt',
		'posts_per_page' => 3,
	]
);
?>

<ul class="item-type-list item-type-list-research-topics">
	<?php foreach ( $research_topics as $research_topic ) : ?>
		<li>
			<?php echo esc_html( $research_topic->post_title ); ?>
		</li>
	<?php endforeach; ?>
</ul>
