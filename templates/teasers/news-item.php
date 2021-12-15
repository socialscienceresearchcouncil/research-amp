<?php
$news_item_id = $args['id'];
$news_item    = get_post( $news_item_id );

$is_featured = ! empty( $args['is_featured'] );

$custom_author = '';
if ( function_exists( 'pressforward' ) ) {
	$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'item_author' );
}

$article_classes = [ 'teaser' ];

$associated_research_topics = get_the_terms( $news_item_id, 'ssrc_research_topic' );

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

		<?php if ( $custom_author ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php printf( '<span class="teaser-byline-by">By</span>: %s', esc_html( $custom_author ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</article>
