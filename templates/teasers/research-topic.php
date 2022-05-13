<?php

$r = array_merge(
	[
		'id'                   => 0,
		'is_edit_mode'         => false,
		'show_item_type_label' => false,
		'title_size'           => '',
		'variation_type'       => 'grid',
	],
	$args
);

$research_topic_id = $r['id'];

$is_edit_mode = $r['is_edit_mode'];

$variation_type = 'list' === $r['variation_type'] ? 'list' : 'grid';

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

$title_classes = [
	'item-title',
	'research-topic-item-title',
];

if ( $r['title_size'] ) {
	$title_classes[] = 'has-' . $r['title_size'] . '-font-size';
}

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( $r['show_item_type_label'] ) : ?>
		<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'Research Topic', 'research-amp' ) ] ); ?>
	<?php endif; ?>

	<?php if ( 'list' === $variation_type ) : ?>
		<div class="research-topic-teaser-image" style="<?php echo esc_attr( $image_div_style ); ?>">&nbsp;</div>
	<?php endif; ?>

	<div class="research-topic-teaser-contents">
		<h3 class="<?php echo esc_attr( implode( ' ', $title_classes ) ); ?>">

			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_attr( get_permalink( $research_topic_id ) ); ?>">
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $research_topic_id ) ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<div class="item-excerpt research-topic-item-excerpt enforce-reading-width"><?php echo wp_kses_post( get_the_excerpt( $research_topic_id ) ); ?></div>

		<?php if ( 'list' === $variation_type ) : ?>
			<?php if ( $is_edit_mode ) : ?>
				<span class="ramp-arrow-more-link"><?php esc_html_e( 'Learn more', 'research-amp' ); ?></span>
			<?php else : ?>
				<a class="ramp-arrow-more-link" href="<?php echo esc_attr( get_permalink( $research_topic_id ) ); ?>"><?php esc_html_e( 'Learn more', 'research-amp' ); ?></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</article>
