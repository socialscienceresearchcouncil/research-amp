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

$byline_args = [];

if ( $custom_author ) {
	$byline_args['author'] = $custom_author;
}

if ( $show_publication_date ) {
	$publication_date = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'publication_date' );
	if ( $publication_date ) {
		$byline_args['date'] = gmdate( get_option( 'date_format' ), strtotime( $publication_date ) );
	} else {
		$byline_args['date'] = get_the_date( '', $news_item_id );
	}
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
				<?php ramp_get_template_part( 'byline', $byline_args ); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $img_src ) : ?>
		<div class="teaser-image" style="background-image:url(<?php echo esc_url( $img_src ); ?>);">&nbsp;</div>
	<?php endif; ?>
</article>
