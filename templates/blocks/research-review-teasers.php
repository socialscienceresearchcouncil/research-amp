<?php

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'order'                      => 'alphabetical',
		'showLoadMore'               => false,
		'showPublicationDate'        => true,
		'variationType'              => 'grid',
	],
	$args
);

$number_of_items = (int) $r['numberOfItems'];

$content_mode_settings = \SSRC\RAMP\Blocks::get_content_mode_settings_from_template_args( $r );

$order_args = [ 'alphabetical', 'latest', 'random' ];
$order_arg  = in_array( $r['order'], $order_args, true ) ? $r['order'] : 'alphabetical';

$variation_type = 'list' === $r['variationType'] ? 'list' : 'grid';

$post_args = [
	'post_type'      => 'ramp_review',
	'posts_per_page' => $number_of_items,
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

switch ( $order_arg ) {
	case 'alphabetical' :
		$post_args['orderby'] = [ 'title' => 'ASC' ];
	break;

	case 'latest' :
		$post_args['orderby'] = [ 'date' => 'DESC' ];
	break;

	case 'random' :
	default :
		$post_args['orderby'] = 'rand';
	break;
}

$offset_query_var = 'review-pag-offset';
$offset           = (int) $wp_query->get( $offset_query_var );

$post_args['offset'] = $offset;

$research_review_query = new WP_Query( $post_args );

$has_more_pages = ( $offset + $number_of_items ) <= $research_review_query->found_posts;

$list_classes = [
	'item-type-list',
	'item-type-list-research-reviews',
	'load-more-list',
	'research-reviews-' . $variation_type,
];

if ( 'grid' === $variation_type ) {
	$list_classes[] = 'item-type-list-flex';
	$list_classes[] = 'item-type-list-3';
}

$div_classes = [
	'research-review-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var
];

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
		<?php foreach ( $research_review_query->posts as $research_review ) : ?>
			<li>
				<?php
				ramp_get_template_part(
					'teasers/research-review',
					[
						'id'                    => $research_review->ID,
						'is_edit_mode'          => $r['isEditMode'],
						'show_publication_date' => (bool) $r['showPublicationDate'],
						'show_research_topics'  => ! $content_mode_settings['research_topic_id'],
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
