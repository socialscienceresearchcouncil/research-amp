<?php

$r = array_merge(
	[
		'contentMode'         => 'auto',
		'featuredItemId'      => 0,
		'isEditMode'          => false,
		'numberOfItems'       => 3,
		'order'               => 'latest',
		'researchTopic'       => null,
		'showLoadMore'        => false,
		'showPublicationDate' => true,
		'variationType'       => 'grid',
	],
	$args
);

if ( in_array( $r['variationType'], [ 'grid', 'list', 'featured' ], true ) ) {
	$variation_type = $r['variationType'];
} else {
	$variation_type = 'grid';
}

$query_args = [
	'post_type'      => 'ramp_article',
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
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

	$query_args['posts_per_page'] = 3;
	$query_args['post__not_in']   = [ $featured_item_id ];

	$featured_item = get_post( $featured_item );
} else {
	$query_args['posts_per_page'] = (int) $r['numberOfItems'];
}

$articles_query = new WP_Query( $query_args );

$teasers_classes = [
	'article-teasers',
	'article-teasers-' . $variation_type,
];

$list_classes = [
	'item-type-list',
	'item-type-list-flex',
	'item-type-list-articles',
];

if ( 'featured' === $variation_type ) {
	$list_classes[] = 'non-featured-article-teasers';
}

?>

<div class="<?php echo esc_attr( implode( ' ', $teasers_classes ) ); ?>">
	<?php if ( 'featured' === $variation_type ) : ?>
		<div class="featured-article-teaser">
			<?php
			if ( $featured_item_id ) {
				ramp_get_template_part(
					'teasers/article',
					[
						'id'                    => $featured_item_id,
						'is_featured'           => true,
						'show_publication_date' => (bool) $r['showPublicationDate'],
					]
				);
			} elseif ( $r['isEditMode'] ) {
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
				ramp_get_template_part(
					'teasers/article',
					[
						'id'                    => $article->ID,
						'show_publication_date' => (bool) $r['showPublicationDate'],
					]
				);
				?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
