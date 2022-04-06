<?php

$r = array_merge(
	[
		'id'                    => 0,
		'is_edit_mode'          => false,
		'is_search_result'      => false,
		'show_excerpt'          => true,
		'show_image'            => true,
		'show_item_type_label'  => false,
		'show_publication_date' => false,
		'show_research_topics'  => false,
	],
	$args
);

$research_review_id = $r['id'];

$is_edit_mode = $r['is_edit_mode'];

$img_src      = RAMP_PLUGIN_URL . '/assets/img/empty-image.png';
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

$show_publication_date = $r['show_publication_date'];
$show_research_topics  = $r['show_research_topics'];

$author_links  = \SSRC\RAMP\Profile::get_profile_links_for_post( $research_review_id );
$author_string = implode( ', ', $author_links );
if ( $is_edit_mode ) {
	$author_string = wp_strip_all_tags( $author_string );
}

if ( $show_publication_date ) {
	$byline = sprintf(
		/* translators: 1. author link, 2. publication date */
		esc_html__( 'By %1$s on %2$s', 'ramp' ),
		'<span class="byline-author">' . $author_string . '</span>',
		'<span class="byline-publication-date">' . esc_html( get_the_date( '', $research_review_id ) ) . '</span>'
	);
} else {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'ramp' ),
		'<span class="byline-author">' . $author_string . '</span>'
	);
}

$research_topics_position = $r['is_search_result'] ? 'bottom' : 'top';

?>

<article class="<?php echo esc_attr( $article_class ); ?>">
	<?php if ( $r['show_item_type_label'] ) : ?>
		<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'Research Review', 'ramp' ) ] ); ?>
	<?php endif; ?>

	<?php if ( $r['show_image'] ) : ?>
		<div class="teaser-thumb research-review-teaser-thumb">
			<?php if ( $img_src ) : ?>
				<?php if ( ! $is_edit_mode ) : ?>
					<a href="<?php echo esc_attr( get_permalink( $research_review_id ) ); ?>">
				<?php endif; ?>

				<img class="research_review-teaser-thumb-img" src="<?php echo esc_attr( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />

				<?php if ( ! $is_edit_mode ) : ?>
					</a>
				<?php endif; ?>
			<?php else : ?>
				&nbsp; <?php /* Force flex to use the available space */ ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="teaser-content research-review-teaser-content">
		<?php if ( $show_research_topics && 'top' === $research_topics_position ) : ?>
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

		<h3 class="item-title research-review-item-title">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_attr( get_permalink( $research_review_id ) ); ?>">
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $research_review_id ) ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<?php if ( $r['show_excerpt'] ) : ?>
			<div class="item-excerpt research-review-item-excerpt"><?php echo wp_kses_post( get_the_excerpt( $research_review_id ) ); ?></div>
		<?php endif; ?>

		<div class="teaser-byline research-review-teaser-byline">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $byline; ?>
		</div>

		<?php if ( $show_research_topics && 'bottom' === $research_topics_position ) : ?>
			<?php
			ramp_get_template_part(
				'research-topic-tags',
				[
					'display_type' => 'bubble',
					'item_id'      => $research_review_id,
				]
			);
			?>
		<?php endif; ?>
	</div>
</article>
