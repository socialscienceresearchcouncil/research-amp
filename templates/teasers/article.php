<?php
$article_id = $args['id'];
$article    = get_post( $article_id );

$is_featured = ! empty( $args['is_featured'] );

$img_src      = '';
$img_alt      = '';
$thumbnail_id = get_post_thumbnail_id( $article );
if ( $thumbnail_id ) {
	$all_sizes = wp_get_registered_image_subsizes();
	$img_size  = 'ramp-thumbnail';

	$img_details = wp_get_attachment_image_src( $thumbnail_id, $img_size );
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

$author_links = \SSRC\RAMP\Profile::get_profile_links_for_post( $article_id );

$article_classes = [ 'teaser' ];
if ( $is_featured ) {
	$article_classes[] = 'featured-article-teaser';
}

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( $img_src ) : ?>
		<div class="teaser-thumb article-teaser-thumb">
			<a href="<?php echo esc_attr( get_permalink( $article ) ); ?>">
				<img class="article-teaser-thumb-img" src="<?php echo esc_attr( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
			</a>
		</div>
	<?php endif; ?>

	<div class="teaser-content article-teaser-content">
		<?php if ( $article_types ) : ?>
			<?php ramp_get_template_part( 'item-type-tag', [ 'label' => $article_types[0] ] ); ?>
		<?php endif; ?>

		<h3 class="has-medium-font-size item-title article-item-title"><a href="<?php echo esc_attr( get_permalink( $article_id ) ); ?>"><?php echo esc_html( get_the_title( $article_id ) ); ?></a></h3>

		<div class="article-teaser-byline teaser-byline">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php printf( '<span class="teaser-byline-by">By</span> %s', implode( ', ', $author_links ) ); ?>
		</div>
	</div>
</article>
