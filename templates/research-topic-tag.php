<?php
$research_topic_tag = get_term( $args['term_id'] );
?>

<a class="research-topic-tag" href="<?php echo esc_attr( get_term_link( $research_topic_tag ) ); ?>"><?php echo esc_html( $research_topic_tag->name ); ?></a>
