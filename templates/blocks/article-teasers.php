<?php

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'featuredItemId'             => 0,
		'horizontalSwipe'            => false,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'order'                      => 'latest',
		'showByline'                 => true,
		'showImage'                  => false,
		'showLoadMore'               => false,
		'showPublicationDate'        => true,
		'showResearchTopics'         => true,
		'showRowRules'               => false,
		'titleSize'                  => 'h-4',
		'variationType'              => 'grid',
	],
	$args
);

$is_edit_mode = (bool) $r['isEditMode'];

if ( in_array( $r['variationType'], [ 'grid', 'list', 'featured' ], true ) ) {
	$variation_type = $r['variationType'];
} else {
	$variation_type = 'grid';
}

// We currently only support the display of research topics in List view.
$show_research_topics = $r['showResearchTopics'] && 'list' === $variation_type;

$query_args = [
	'post_type'   => 'ramp_article',
	'post_status' => 'publish',
	'tax_query'   => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),

];

$order_args = [ 'alphabetical', 'latest', 'random' ];
$order_arg  = in_array( $r['order'], $order_args, true ) ? $r['order'] : 'alphabetical';

switch ( $order_arg ) {
	case 'alphabetical' :
		$query_args['orderby'] = [ 'title' => 'ASC' ];
	break;

	case 'latest' :
		$query_args['orderby'] = [ 'date' => 'DESC' ];
	break;

	case 'random' :
	default :
		$query_args['orderby'] = 'rand';
	break;
}

if ( 'featured' === $variation_type ) {
	$featured_item_id = (int) $r['featuredItemId'];
	$number_of_items  = 3;

	$query_args['posts_per_page'] = $number_of_items;
	$query_args['post__not_in']   = [ $featured_item_id ];
} else {
	$number_of_items = (int) $r['numberOfItems'];

	$query_args['posts_per_page'] = $number_of_items;
}

// Load More doesn't work with the Featured variation type.
$show_load_more = $r['showLoadMore'] && 'featured' !== $variation_type;

$offset_query_var = 'article-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$query_args['offset'] = $offset;

$articles_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $articles_query->found_posts;

$teasers_classes = [
	'article-teasers',
	'article-teasers-' . $variation_type,
];

$div_classes = [
	'item-type-list-container-' . $variation_type,
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

if ( $r['horizontalSwipe'] ) {
	$div_classes[] = 'allow-horizontal-swipe';
}

$list_classes = [
	'item-type-list',
	'item-type-list-articles',
	'load-more-list',
];

if ( 'list' !== $variation_type ) {
	$list_classes[] = 'item-type-list-flex';
}

if ( 'featured' === $variation_type ) {
	$list_classes[] = 'non-featured-article-teasers';
} elseif ( 'grid' === $variation_type ) {
	$list_classes[] = 'item-type-list-3';
}

if ( (bool) $r['showRowRules'] ) {
	$list_classes[] = 'has-row-rules';
} else {
	$list_classes[] = 'has-no-row-rules';
}

$featured_article_teaser_classes = [
	'featured-article-teaser',
];

if ( ! empty( $featured_item_id ) ) {
	$featured_article_teaser_classes[] = 'featured-article-not-set';
}

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( ! empty( $articles_query->posts ) || ! $r['isEditMode'] ) : ?>
		<div class="<?php echo esc_attr( implode( ' ', $teasers_classes ) ); ?>">
			<?php if ( 'featured' === $variation_type ) : ?>
				<div class="<?php echo esc_attr( implode( ' ', $featured_article_teaser_classes ) ); ?>">
					<?php
					if ( $featured_item_id ) {
						ramp_get_template_part(
							'teasers/article',
							[
								'id'                    => $featured_item_id,
								'is_edit_mode'          => $is_edit_mode,
								'is_featured'           => true,
								'show_byline'           => true,
								'show_image'            => true,
								'show_publication_date' => $r['showPublicationDate'],
							]
						);
					} elseif ( $is_edit_mode ) {
						printf(
							'<p class="featured-article-notice">%s</p>',
							esc_html__( 'Use the "Featured Article" setting in the right-hand panel to select the item that will appear in this space.', 'research-amp' )
						);
					}
					?>
				</div>
			<?php endif; ?>

			<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
				<?php foreach ( $articles_query->posts as $article ) : ?>
					<li>
						<?php
						ramp_get_template_part(
							'teasers/article',
							[
								'id'                    => $article->ID,
								'is_edit_mode'          => $is_edit_mode,
								'show_byline'           => $r['showByline'],
								'show_image'            => $r['showImage'],
								'show_publication_date' => $r['showPublicationDate'],
								'show_research_topics'  => $show_research_topics,
								'title_size'            => $r['titleSize'],
							]
						);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php if ( $show_load_more && $has_more_pages ) : ?>
			<?php
			ramp_get_template_part(
				'load-more-button',
				[
					'is_edit_mode'    => $is_edit_mode,
					'offset'          => $offset,
					'query_var'       => $offset_query_var,
					'number_of_items' => $number_of_items,
				]
			);
			?>
		<?php endif; ?>
	<?php else : ?>
		<?php ramp_get_template_part( 'teasers-no-content' ); ?>
	<?php endif; ?>
</div>
