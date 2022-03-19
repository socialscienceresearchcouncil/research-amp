<?php
$research_review_id = $args['id'];

$img_src      = '';
$img_alt      = '';
$thumbnail_id = get_post_thumbnail_id( $research_review_id );
if ( $thumbnail_id ) {
	$all_sizes = wp_get_registered_image_subsizes();
	$img_size  = 'ramp-thumbnail';

	$img_details = wp_get_attachment_image_src( $thumbnail_id, $img_size );
	$img_src     = $img_details[0];
	$img_alt     = trim( wp_strip_all_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
}

$article_class = 'teaser';
if ( $img_src ) {
	$article_class .= ' has-featured-image';
}

$show_publication_date = ! empty( $args['show_publication_date'] );
$show_research_topics  = ! empty( $args['show_research_topics'] );

$author_links = \SSRC\RAMP\Profile::get_profile_links_for_post( $research_review_id );
if ( $show_publication_date ) {
	$byline = sprintf(
		/* translators: 1. author link, 2. publication date */
		esc_html__( 'By %1$s on %2$s', 'ramp' ),
		'<span class="byline-author">' . implode( ', ', $author_links ) . '</span>',
		'<span class="byline-publication-date">' . esc_html( get_the_date( '', $research_review_id ) ) . '</span>'
	);
} else {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'ramp' ),
		'<span class="byline-author">' . implode( ', ', $author_links ) . '</span>'
	);
}

?>

<article class="<?php echo esc_attr( $article_class ); ?>">
	<div class="teaser-thumb research-review-teaser-thumb">
		<?php if ( $img_src ) : ?>
			<a href="<?php echo esc_attr( get_permalink( $research_review_id ) ); ?>">
				<img class="research_review-teaser-thumb-img" src="<?php echo esc_attr( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
			</a>
		<?php else : ?>
			&nbsp; <?php /* Force flex to use the available space */ ?>
		<?php endif; ?>
	</div>

	<div class="teaser-content research-review-teaser-content">
		<?php if ( $show_research_topics ) : ?>
			<?php
			ramp_get_template_part(
				'research-topic-tags',
				[
					'display_type' => 'plain',
					'item_id'      => $research_review_id,
				]
			);
			?>
		<?php endif; ?>

		<h3 class="item-title research-review-item-title"><a href="<?php echo esc_attr( get_permalink( $research_review_id ) ); ?>"><?php echo esc_html( get_the_title( $research_review_id ) ); ?></a></h3>

		<div class="item-excerpt research-review-item-excerpt"><?php echo wp_kses_post( get_the_excerpt( $research_review_id ) ); ?></div>

		<div class="teaser-byline research-review-teaser-byline">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $byline; ?>
		</div>
	</div>
</article>
