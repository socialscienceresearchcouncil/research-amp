<?php
$news_item_id = $args['id'];
$news_item    = get_post( $news_item_id );

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

$article_classes = [ 'teaser', 'featured-news-item-teaser' ];

$associated_research_topics = get_the_terms( $news_item_id, 'ramp_assoc_topic' );

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content news-item-teaser-content">
		<?php if ( $associated_research_topics ) : ?>
			<div class="research-topic-tags">
				<?php foreach ( $associated_research_topics as $associated_research_topic ) : ?>
					<?php get_template_part( 'template-parts/research-topic-tag', '', [ 'term_id' => $associated_research_topic->term_id ] ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<h1 class="item-title news-item-item-title"><a href="<?php echo esc_attr( get_permalink( $news_item_id ) ); ?>"><?php echo esc_html( get_the_title( $news_item_id ) ); ?></a></h1>

		<div class="news-item-teaser-excerpt">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo get_the_excerpt( $news_item_id ); ?>
		</div>

		<?php if ( $custom_author ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php printf( '<span class="teaser-byline-by">By</span>: %s', esc_html( $custom_author ) ); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $img_src ) : ?>
		<div class="teaser-image" style="background-image:url(<?php echo esc_url( $img_src ); ?>);">&nbsp;</div>
	<?php endif; ?>
</article>
