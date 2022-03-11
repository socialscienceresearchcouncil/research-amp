<?php
$news_item_id = $args['id'];
$news_item    = get_post( $news_item_id );

$is_featured = ! empty( $args['is_featured'] );

$custom_author = '';
if ( function_exists( 'pressforward' ) ) {
	$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'item_author' );
}

$article_classes = [ 'teaser' ];

$associated_research_topics = get_the_terms( $news_item_id, 'ramp_assoc_topic' );

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content news-item-teaser-content">
		<?php ramp_get_template_part( 'research-topic-tags', [ 'item_id' => $news_item_id ] ); ?>

		<h3 class="has-h-4-font-size item-title news-item-item-title"><a href="<?php echo esc_attr( get_permalink( $news_item_id ) ); ?>"><?php echo esc_html( get_the_title( $news_item_id ) ); ?></a></h3>

		<?php if ( $custom_author ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php printf( '<span class="teaser-byline-by">By</span>: %s', esc_html( $custom_author ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</article>
