<?php
$research_topic_id = $args['id'];

$variation_type = isset( $args['variation_type'] ) && 'list' === $args['variation_type'] ? 'list' : 'grid';

$article_classes = [
	'research-topic-teaser',
	'research-topic-teaser-' . $variation_type,
];

if ( 'list' === $variation_type ) {
	$image_div_style = '';
	$thumbnail_id    = get_post_thumbnail_id( $research_topic_id );
	if ( $thumbnail_id ) {
		$img_details = wp_get_attachment_image_src( $thumbnail_id, 'ramp-thumbnail' );
		$img_src     = $img_details[0];

		$image_div_style = sprintf(
			'background-image:url(%s);',
			$img_src
		);
	}
}

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( 'list' === $variation_type ) : ?>
		<div class="research-topic-teaser-image" style="<?php echo esc_attr( $image_div_style ); ?>">&nbsp;</div>
	<?php endif; ?>

	<div class="research-topic-teaser-contents">
		<h3 class="item-title research-topic-item-title"><a href="<?php echo esc_attr( get_permalink( $research_topic_id ) ); ?>"><?php echo esc_html( get_the_title( $research_topic_id ) ); ?></a></h3>

		<div class="item-excerpt research-topic-item-excerpt"><?php echo wp_kses_post( get_the_excerpt( $research_topic_id ) ); ?></div>

		<?php if ( 'list' === $variation_type ) : ?>
			<a class="ramp-arrow-more-link" href="<?php echo esc_attr( get_permalink( $research_topic_id ) ); ?>"><?php esc_html_e( 'Learn more', 'ramp' ); ?></a>
		<?php endif; ?>
	</div>
</article>
