<?php

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'featuredItemId'             => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'order'                      => 'latest',
		'showLoadMore'               => false,
		'showPublicationDate'        => true,
		'showRowRules'               => true,
		'variationType'              => 'grid',
	],
	$args
);

$is_edit_mode = (bool) $r['isEditMode'];

if ( in_array( $r['variationType'], [ 'grid', 'list', 'list-mini', 'featured' ], true ) ) {
	$variation_type = $r['variationType'];
} else {
	$variation_type = 'grid';
}

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

$show_publication_date = $r['showPublicationDate'] && 'list-mini' !== $variation_type;
$show_byline           = true;
$show_image            = 'list-mini' !== $variation_type;

$offset_query_var = 'article-pag-offset';
$offset           = (int) $wp_query->get( $offset_query_var );

$query_args['offset'] = $offset;

$articles_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $articles_query->found_posts;

$teasers_classes = [
	'article-teasers',
	'article-teasers-' . $variation_type,
];

$div_classes = [
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

$list_classes = [
	'item-type-list',
	'item-type-list-articles',
	'load-more-list',
];

if ( 'list' !== $variation_type && 'list-mini' !== $variation_type ) {
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

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<div class="<?php echo esc_attr( implode( ' ', $teasers_classes ) ); ?>">
		<?php if ( 'featured' === $variation_type ) : ?>
			<div class="featured-article-teaser">
				<?php
				if ( $featured_item_id ) {
					ramp_get_template_part(
						'teasers/article',
						[
							'id'                    => $featured_item_id,
							'is_edit_mode'          => $is_edit_mode,
							'is_featured'           => true,
							'show_byline'           => $show_byline,
							'show_image'            => true,
							'show_publication_date' => $show_publication_date,
						]
					);
				} elseif ( $is_edit_mode ) {
					printf(
						'<p class="featured-article-notice">%s</p>',
						esc_html__( 'Use the "Featured Article" setting in the right-hand panel to select the item that will appear in this space.', 'ramp' )
					);
				}
				?>
			</div>
		<?php endif; ?>

		<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
			<?php foreach ( $articles_query->posts as $article ) : ?>
				<li>
					<?php
					$title_size = 'list-mini' === $variation_type ? 'h-5' : 'h-4';

					ramp_get_template_part(
						'teasers/article',
						[
							'id'                    => $article->ID,
							'is_edit_mode'          => $is_edit_mode,
							'show_byline'           => $show_byline,
							'show_image'            => $show_image,
							'show_publication_date' => $show_publication_date,
							'show_research_topics'  => 'list' === $variation_type,
							'title_size'            => $title_size,
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
				'offset'          => $offset,
				'query_var'       => $offset_query_var,
				'number_of_items' => $number_of_items,
			]
		);
		?>
	<?php endif; ?>
</div>
