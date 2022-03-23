<?php
$news_item_id = $args['id'];
$news_item    = get_post( $news_item_id );

$is_featured = ! empty( $args['is_featured'] );

$article_classes = [ 'teaser' ];

$show_publication_date = ! empty( $args['show_publication_date'] );
$show_research_topics  = ! empty( $args['show_research_topics'] );

$custom_author = '';
if ( function_exists( 'pressforward' ) ) {
	$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'item_author' );
}

if ( $show_publication_date ) {
	$publication_date = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'publication_date' );
	if ( $publication_date ) {
		$formatted_date = gmdate( get_option( 'date_format' ), strtotime( $publication_date ) );
	} else {
		$formatted_date = get_the_date( '', $news_item_id );
	}

	if ( $custom_author ) {
		$byline = sprintf(
			/* translators: 1. author link, 2. publication date */
			esc_html__( 'By %1$s on %2$s', 'ramp' ),
			'<span class="byline-author">' . $custom_author . '</span>',
			'<span class="byline-publication-date">' . $formatted_date . '</span>'
		);
	} else {
		/* translators: publication date */
		$byline = sprintf(
			esc_html__( 'On %s', 'ramp' ),
			'<span class="byline-publication-date">' . $formatted_date . '</span>'
		);
	}
} elseif ( $custom_author ) {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'ramp' ),
		'<span class="byline-author">' . $custom_author . '</span>'
	);
}

$associated_research_topics = get_the_terms( $news_item_id, 'ramp_assoc_topic' );

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content news-item-teaser-content">
		<?php ramp_get_template_part( 'research-topic-tags', [ 'item_id' => $news_item_id ] ); ?>

		<h3 class="has-h-4-font-size item-title news-item-item-title"><a href="<?php echo esc_attr( get_permalink( $news_item_id ) ); ?>"><?php echo esc_html( get_the_title( $news_item_id ) ); ?></a></h3>

		<?php if ( $custom_author ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php echo $byline; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
