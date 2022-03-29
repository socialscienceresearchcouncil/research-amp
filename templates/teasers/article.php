<?php
$article_id = $args['id'];
$article    = get_post( $article_id );

$is_featured  = ! empty( $args['is_featured'] );
$is_edit_mode = ! empty( $args['is_edit_mode'] );

$img_src      = '';
$img_alt      = '';
$thumbnail_id = get_post_thumbnail_id( $article );
if ( $thumbnail_id ) {
	$img_details = wp_get_attachment_image_src( $thumbnail_id, 'ramp-thumbnail' );
	$img_src     = $img_details[0];
	$img_alt     = trim( wp_strip_all_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
}

$article_type_terms = get_the_terms( $article_id, 'ramp_article_type' );
$article_types      = [];
if ( $article_type_terms ) {
	$article_types = array_map(
		function( $type ) {
			return $type->name;
		},
		$article_type_terms
	);
}

$show_publication_date = ! empty( $args['show_publication_date'] );
$show_research_topics  = ! empty( $args['show_research_topics'] );

$author_links  = \SSRC\RAMP\Profile::get_profile_links_for_post( $article_id );
$author_string = implode( ', ', $author_links );
if ( $is_edit_mode ) {
	$author_string = wp_strip_all_tags( $author_string );
}

if ( $show_publication_date ) {
	$byline = sprintf(
		/* translators: 1. author link, 2. publication date */
		esc_html__( 'By %1$s on %2$s', 'ramp' ),
		'<span class="byline-author">' . $author_string . '</span>',
		'<span class="byline-publication-date">' . esc_html( get_the_date( '', $article_id ) ) . '</span>'
	);
} else {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'ramp' ),
		'<span class="byline-author">' . $author_string . '</span>'
	);
}

$article_classes = [ 'teaser' ];
if ( $is_featured ) {
	$article_classes[] = 'featured-article-teaser';
}

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( $is_featured ) : ?>
		<?php ramp_get_template_part( 'featured-tag' ); ?>
	<?php endif; ?>

	<?php if ( $img_src ) : ?>
		<div class="teaser-thumb article-teaser-thumb">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_attr( get_permalink( $article ) ); ?>">
			<?php endif; ?>

			<img class="article-teaser-thumb-img" src="<?php echo esc_attr( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="teaser-content article-teaser-content">
		<?php if ( $article_types ) : ?>
			<?php ramp_get_template_part( 'item-type-tag', [ 'label' => $article_types[0] ] ); ?>
		<?php endif; ?>

		<h3 class="has-h-4-font-size item-title article-item-title">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_attr( get_permalink( $article_id ) ); ?>">
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $article_id ) ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<div class="article-teaser-byline teaser-byline">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $byline; ?>
		</div>

		<?php if ( $show_research_topics ) : ?>
			<?php
			ramp_get_template_part(
				'research-topic-tags',
				[
					'is_edit_mode' => $is_edit_mode,
					'item_id'      => $article_id,
				]
			);
			?>
		<?php endif; ?>
	</div>
</article>
