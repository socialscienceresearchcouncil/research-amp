<?php
$news_item_id = $args['id'];
$news_item    = get_post( $news_item_id );

$is_edit_mode = ! empty( $args['is_edit_mode'] );

$show_publication_date = ! empty( $args['show_publication_date'] );

$custom_author = '';
if ( function_exists( 'pressforward' ) ) {
	$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'item_author' );
}

$img_src      = '';
$img_alt      = '';
$thumbnail_id = get_post_thumbnail_id( $news_item_id );
if ( $thumbnail_id ) {
	$all_sizes = wp_get_registered_image_subsizes();
	$img_size  = 'ramp-thumbnail';

	$img_details = wp_get_attachment_image_src( $thumbnail_id, $img_size );
	$img_src     = $img_details[0];
	$img_alt     = trim( wp_strip_all_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
}

if ( $show_publication_date ) {
	$publication_date = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'publication_date' );
	if ( $publication_date ) {
		$formatted_date = gmdate( get_option( 'date_format' ), strtotime( $publication_date ) );
	} else {
		$formatted_date = get_the_date( '', $news_item_id );
	}

	if ( $custom_author ) {
		$byline = sprintf(
			/* translators: 1. author link, 2. publication date */
			esc_html__( 'By %1$s on %2$s', 'ramp' ),
			'<span class="byline-author">' . $custom_author . '</span>',
			'<span class="byline-publication-date">' . $formatted_date . '</span>'
		);
	} else {
		/* translators: publication date */
		$byline = sprintf(
			esc_html__( 'On %s', 'ramp' ),
			'<span class="byline-publication-date">' . $formatted_date . '</span>'
		);
	}
} elseif ( $custom_author ) {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'ramp' ),
		'<span class="byline-author">' . $custom_author . '</span>'
	);
}

$article_classes = [ 'teaser', 'featured-news-item-teaser' ];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content news-item-teaser-content">
		<?php ramp_get_template_part( 'featured-tag' ); ?>

		<h3 class="has-h-4-font-size item-title news-item-item-title">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_attr( get_permalink( $news_item_id ) ); ?>">
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $news_item_id ) ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<div class="news-item-teaser-excerpt">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo get_the_excerpt( $news_item_id ); ?>
		</div>

		<?php if ( $byline ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $byline; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $img_src ) : ?>
		<div class="teaser-image" style="background-image:url(<?php echo esc_url( $img_src ); ?>);">&nbsp;</div>
	<?php endif; ?>
</article>
