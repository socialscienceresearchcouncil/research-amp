<?php
$research_topic_id = $args['id'];
?>

<article>
	<h1 class="item-title research-topic-item-title"><a href="<?php echo esc_attr( get_permalink( $research_topic_id ) ); ?>"><?php echo esc_html( get_the_title( $research_topic_id ) ); ?></a></h1>

	<div class="item-excerpt research-topic-item-excerpt"><?php echo wp_kses_post( get_the_excerpt( $research_topic_id ) ); ?></div>
</article>
