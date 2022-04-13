<?php

$rt_map     = ramp_app()->get_cpttax_map( 'research_topic' );
$rt_post_id = $rt_map->get_post_id_for_term_id( $args['term_id'] );

$research_topic_tag = get_term( $args['term_id'] );

$is_edit_mode = ! empty( $args['is_edit_mode'] );

$tag_classes = [ 'research-topic-tag' ];
if ( ! empty( $args['display_type'] ) && 'plain' === $args['display_type'] ) {
	$tag_classes[] = 'tag-plain';
} else {
	$tag_classes[] = 'tag-bubble';
}

?>

<?php if ( $is_edit_mode ) : ?>
	<span class="<?php echo esc_attr( implode( ' ', $tag_classes ) ); ?>"><?php echo esc_html( $research_topic_tag->name ); ?></span>
<?php else : ?>
	<a class="<?php echo esc_attr( implode( ' ', $tag_classes ) ); ?>" href="<?php echo esc_attr( get_permalink( $rt_post_id ) ); ?>"><?php echo esc_html( $research_topic_tag->name ); ?></a>
<?php endif; ?>
