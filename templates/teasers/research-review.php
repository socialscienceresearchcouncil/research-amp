<?php
$research_review_id = $args['id'];

$img_src      = '';
$img_alt      = '';
$thumbnail_id = get_post_thumbnail_id( $research_review_id );
if ( $thumbnail_id ) {
	$all_sizes = wp_get_registered_image_subsizes();
	$img_size  = 'large';

	$img_details = wp_get_attachment_image_src( $thumbnail_id, $img_size );
	$img_src     = $img_details[0];
	$img_alt     = trim( wp_strip_all_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
}

$article_class = 'teaser';
if ( $img_src ) {
	$article_class .= ' has-featured-image';
}

?>

<article class="<?php echo esc_attr( $article_class ); ?>">
	<?php if ( $img_src ) : ?>
		<div class="teaser-thumb research-review-teaser-thumb">
			<a href="<?php echo esc_attr( get_permalink( $research_review_id ) ); ?>">
				<img class="research_review-teaser-thumb-img" src="<?php echo esc_attr( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
			</a>
		</div>
	<?php endif; ?>

	<div class="teaser-content research-review-teaser-content">
		<h1 class="item-title research-review-item-title"><a href="<?php echo esc_attr( get_permalink( $research_review_id ) ); ?>"><?php echo esc_html( get_the_title( $research_review_id ) ); ?></a></h1>

		<div class="item-excerpt research-review-item-excerpt"><?php echo wp_kses_post( get_the_excerpt( $research_review_id ) ); ?></div>
	</div>
</article>
