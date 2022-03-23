<?php
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
	<a class="<?php echo esc_attr( implode( ' ', $tag_classes ) ); ?>" href="<?php echo esc_attr( get_term_link( $research_topic_tag ) ); ?>"><?php echo esc_html( $research_topic_tag->name ); ?></a>
<?php endif; ?>
