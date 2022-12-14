<?php

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'horizontalSwipe'            => false,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'order'                      => 'alphabetical',
		'showLoadMore'               => false,
		'showPublicationDate'        => true,
		'showRowRules'               => false,
		'variationType'              => 'grid',
	],
	$args
);

$number_of_items = (int) $r['numberOfItems'];

$content_mode_settings = \SSRC\RAMP\Blocks::get_content_mode_settings_from_template_args( $r );

$order_args = [ 'alphabetical', 'latest', 'random' ];
$order_arg  = in_array( $r['order'], $order_args, true ) ? $r['order'] : 'alphabetical';

$variation_type = 'list' === $r['variationType'] ? 'list' : 'grid';

$query_args = [
	'post_type'      => 'ramp_review',
	'posts_per_page' => $number_of_items,
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

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

$offset_query_var = 'review-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$query_args['offset'] = $offset;

$research_review_query = new WP_Query( $query_args );

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

if ( (bool) $r['showRowRules'] ) {
	$list_classes[] = 'has-row-rules';
} else {
	$list_classes[] = 'has-no-row-rules';
}

$div_classes = [
	'item-type-list-container-' . $variation_type,
	'research-review-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

if ( $r['horizontalSwipe'] ) {
	$div_classes[] = 'allow-horizontal-swipe';
}

// For display reasons, the grid must always contain 3n items.
$placeholder_count = ramp_get_placeholder_count( count( $research_review_query->posts ), 3 );

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( ! empty( $research_review_query->posts ) || ! $r['isEditMode'] ) : ?>
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

			<?php if ( 'list' !== $variation_type ) : ?>
				<?php for ( $i = 0; $i < $placeholder_count; $i++ ) : ?>
					<li aria-hidden=true"></li>
				<?php endfor; ?>
			<?php endif; ?>
		</ul>

		<?php if ( ! empty( $args['showLoadMore'] ) && $has_more_pages ) : ?>
			<?php
			ramp_get_template_part(
				'load-more-button',
				[
					'is_edit_mode'    => $r['isEditMode'],
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
