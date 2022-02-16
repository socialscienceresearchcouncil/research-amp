<?php

$research_topic_id = \SSRC\RAMP\Blocks::get_research_topic_from_template_args( $args );

$show_featured_item = ! empty( $args['showFeaturedItem'] );
$featured_item_id   = ! empty( $args['featuredItemId'] ) ? (int) $args['featuredItemId'] : null;

$variation_type = isset( $args['variationType'] ) && in_array( $args['variationType'], [ 'grid', 'columns' ], true ) ? $args['variationType'] : 'grid';

switch ( $variation_type ) {
	case 'columns' :
		$posts_per_page = $show_featured_item && $featured_item_id ? 3 : 4;
	break;

	case 'grid' :
	default :
		$posts_per_page = 3;
	break;
}

$query_args = [
	'post_type'      => 'ramp_article',
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
];

if ( $research_topic_id ) {
	$rt_map     = ramp_app()->get_cpttax_map( 'research_topic' );
	$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic_id );

	$query_args['tax_query'] = [
		[
			'taxonomy' => 'ramp_assoc_topic',
			'terms'    => $rt_term_id,
			'field'    => 'term_id',
		],
	];
}

if ( $show_featured_item && $featured_item_id ) {
	$query_args['post__not_in'] = [ $featured_item_id ];
}

$articles = get_posts( $query_args );

$teasers_classes = [
	'article-teasers',
	'article-teasers-' . $variation_type,
];

$list_classes = [
	'item-type-list',
	'item-type-list-flex',
	'item-type-list-articles',
];

if ( 'columns' === $variation_type ) {
	if ( $show_featured_item && $featured_item_id ) {
		$first_item = get_post( $featured_item_id );
	} else {
		$first_item = array_shift( $articles );
	}

	$list_classes[] = 'non-featured-article-teasers';
}

?>

<div class="<?php echo esc_attr( implode( ' ', $teasers_classes ) ); ?>">
	<?php if ( 'columns' === $variation_type ) : ?>
		<div class="featured-article-teaser">
			<?php
			ramp_get_template_part(
				'teasers/article',
				[
					'id'          => $first_item->ID,
					'is_featured' => true,
				]
			);
			?>
		</div>
	<?php endif; ?>

	<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
		<?php foreach ( $articles as $article ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/article', [ 'id' => $article->ID ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
