<?php

$featured_article_id = isset( $args['featuredArticleId'] ) ? (int) $args['featuredArticleId'] : 0;

// If no featured article is specified, choose the most recent article.
if ( ! $featured_article_id ) {
	$featured_article = get_posts(
		[
			'post_type'      => 'ssrc_expref_pt',
			'posts_per_page' => 1,
			'orderby'        => [ 'date' => 'DESC' ],
			'fields'         => 'ids',
		]
	);

	$featured_article_id = $featured_article[0];
}

$post_args = [
	'post_type'      => 'ssrc_expref_pt',
	'posts_per_page' => 3,
	'post__not_in'   => [ $featured_article_id ],
	'orderby'        => [ 'date' => 'DESC' ],
	'fields'         => 'ids',
];

$article_ids = get_posts( $post_args );

$featured_article_part_args = [
	'id'          => $featured_article_id,
	'is_featured' => true,
];

?>

<div class="article-teasers">
	<div class="featured-article-teaser">
		<?php ramp_get_template_part( 'teasers/article', $featured_article_part_args ); ?>

	</div>

	<div class="non-featured-article-teasers">
		<ul class="item-type-list item-type-list-articles">
			<?php foreach ( $article_ids as $article_id ) : ?>
				<li>
					<?php ramp_get_template_part( 'teasers/article', [ 'id' => $article_id ] ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
