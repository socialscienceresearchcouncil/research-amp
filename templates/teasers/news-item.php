<?php
$news_item_id = $args['id'];
$news_item    = get_post( $news_item_id );

$is_edit_mode = ! empty( $args['is_edit_mode'] );

$article_classes = [ 'teaser' ];

$show_byline           = ! empty( $args['show_byline'] );
$show_publication_date = ! empty( $args['show_publication_date'] );
$show_research_topics  = ! empty( $args['show_research_topics'] );

$title_size = ! empty( $args['title_size'] ) && in_array( $args['title_size'], [ 'h-4', 'h-5' ], true ) ? $args['title_size'] : 'h-4';

$custom_author = '';
if ( function_exists( 'pressforward' ) ) {
	$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'item_author' );
}

if ( $show_publication_date && function_exists( 'pressforward' ) ) {
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
		$byline = sprintf(
			/* translators: publication date */
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

$title_classes = [
	'item-title',
	'article-item-title',
	'has-' . $title_size . '-font-size',
];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content news-item-teaser-content">
		<?php if ( $show_research_topics ) : ?>
			<?php
			ramp_get_template_part(
				'research-topic-tags',
				[
					'is_edit_mode' => $is_edit_mode,
					'item_id'      => $news_item_id,
				]
			);
			?>
		<?php endif; ?>

		<h3 class="<?php echo esc_attr( implode( ' ', $title_classes ) ); ?>">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_attr( get_permalink( $news_item_id ) ); ?>">
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $news_item_id ) ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<?php if ( $custom_author && $show_byline ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $byline; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
