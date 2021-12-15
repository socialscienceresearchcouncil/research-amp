<?php
$article_id = $args['id'];
$article    = get_post( $article_id );

$is_featured = ! empty( $args['is_featured'] );

$img_src      = '';
$img_alt      = '';
$thumbnail_id = get_post_thumbnail_id( $article );
if ( $thumbnail_id ) {
	$all_sizes = wp_get_registered_image_subsizes();
	$img_size  = 'large';

	$img_details = wp_get_attachment_image_src( $thumbnail_id, $img_size );
	$img_src     = $img_details[0];
	$img_alt     = trim( wp_strip_all_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
}

$article_types = array_map(
	function( $type ) {
		return $type->name;
	},
	get_the_terms( $article_id, 'ssrc_article_type' )
);

$authors = [
	'Jane Doe',
];

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
			<div class="article-type-tag">
				<?php echo esc_html( $article_types[0] ); ?>
			</div>
		<?php endif; ?>

		<h1 class="item-title article-item-title"><a href="<?php echo esc_attr( get_permalink( $article_id ) ); ?>"><?php echo esc_html( get_the_title( $article_id ) ); ?></a></h1>

		<div class="article-teaser-byline teaser-byline">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php printf( '<span class="teaser-byline-by">By</span>: %s', implode( ', ', $authors ) ); ?>
		</div>
	</div>
</article>
