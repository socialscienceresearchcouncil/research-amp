<?php

$r = array_merge(
	[
		'id'                    => 0,
		'is_edit_mode'          => false,
		'is_search_result'      => false,
		'show_byline'           => true,
		'show_item_type_label'  => false,
		'show_publication_date' => true,
		'show_research_topics'  => true,
		'title_size'            => 'h-4',
	],
	$args
);

$news_item_id = $r['id'];
$news_item    = get_post( $news_item_id );

$is_edit_mode = $r['is_edit_mode'];

$article_classes = [ 'teaser' ];

$show_byline           = $r['show_byline'];
$show_publication_date = $r['show_publication_date'];
$show_research_topics  = $r['show_research_topics'];

$title_size = in_array( $r['title_size'], [ 'h-4', 'h-5' ], true ) ? $r['title_size'] : 'h-4';

$byline_args = [];

if ( function_exists( 'pressforward' ) ) {
	$byline_args['author'] = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'item_author' );
}

if ( $show_publication_date && function_exists( 'pressforward' ) ) {
	$publication_date = pressforward( 'controller.metas' )->retrieve_meta( $news_item_id, 'publication_date' );
	if ( $publication_date ) {
		$byline_args['date'] = gmdate( get_option( 'date_format' ), strtotime( $publication_date ) );
	} else {
		$byline_args['date'] = get_the_date( '', $news_item_id );
	}
}

$title_classes = [
	'item-title',
	'article-item-title',
	'has-' . $title_size . '-font-size',
];

$research_topics_position = $r['is_search_result'] ? 'bottom' : 'top';

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( $r['show_item_type_label'] ) : ?>
		<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'News Item', 'research-amp' ) ] ); ?>
	<?php endif; ?>

	<div class="teaser-content news-item-teaser-content">
		<?php if ( $show_research_topics && 'top' === $research_topics_position ) : ?>
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

		<?php if ( $show_byline ) : ?>
			<div class="article-teaser-byline teaser-byline">
				<?php ramp_get_template_part( 'byline', $byline_args ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_research_topics && 'bottom' === $research_topics_position ) : ?>
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
	</div>
</article>
