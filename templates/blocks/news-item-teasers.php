<?php

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'order'                      => 'latest',
		'showLoadMore'               => false,
		'showResearchTopics'         => true,
		'showRowRules'               => true,
		'showPublicationDate'        => true,
		'variationType'              => 'grid',
	],
	$args
);

$number_of_items = (int) $r['numberOfItems'];

$content_mode_settings = \SSRC\RAMP\Blocks::get_content_mode_settings_from_template_args( $r );

$order_args = [ 'alphabetical', 'latest', 'random' ];
$order_arg  = in_array( $r['order'], $order_args, true ) ? $r['order'] : 'alphabetical';

$variation_type = in_array( $r['variationType'], [ 'grid', 'list', 'list-mini' ], true ) ? $r['variationType'] : 'grid';

$show_featured_item = ! empty( $args['showFeaturedItem'] );
$featured_item_id   = ! empty( $args['featuredItemId'] ) ? (int) $args['featuredItemId'] : null;

$show_research_topics  = $r['showResearchTopics'] && 'list-mini' !== $variation_type;
$show_publication_date = $r['showPublicationDate'] && 'list-mini' !== $variation_type;
$show_byline           = 'list-mini' !== $variation_type;

$query_args = [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => $number_of_items,
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

if ( $show_featured_item && $featured_item_id ) {
	$query_args['post__not_in'] = [ $featured_item_id ];
}

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

$offset_query_var = 'news-item-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$query_args['offset'] = $offset;

$news_item_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $news_item_query->found_posts;

$div_classes = [
	'news-item-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

$list_classes = [
	'item-type-list',
	'item-type-list-news-items',
	'load-more-list',
	'news-items-' . $variation_type,
];

if ( 'list' !== $variation_type && 'list-mini' !== $variation_type ) {
	$list_classes[] = 'item-type-list-flex';
	$list_classes[] = 'item-type-list-3';
}

if ( (bool) $r['showRowRules'] ) {
	$list_classes[] = 'has-row-rules';
} else {
	$list_classes[] = 'has-no-row-rules';
}

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( $show_featured_item && $featured_item_id ) : ?>
		<div class="featured-news-item">
			<?php
			ramp_get_template_part(
				'teasers/news-item-featured',
				[
					'id'                    => $featured_item_id,
					'is_edit_mode'          => $r['isEditMode'],
					'show_publication_date' => $show_publication_date,
				]
			);
			?>
		</div>
	<?php endif; ?>

	<ul class="<?php echo esc_html( implode( ' ', $list_classes ) ); ?>">
		<?php foreach ( $news_item_query->posts as $news_item ) : ?>
			<li>
				<?php
				$title_size = 'list-mini' === $variation_type ? 'h-5' : 'h-4';

				ramp_get_template_part(
					'teasers/news-item',
					[
						'id'                    => $news_item->ID,
						'is_edit_mode'          => $r['isEditMode'],
						'show_byline'           => $show_byline,
						'show_publication_date' => $show_publication_date,
						'show_research_topics'  => $show_research_topics,
						'title_size'            => $title_size,
					]
				);
				?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( ! empty( $args['showLoadMore'] ) && $has_more_pages ) : ?>
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
