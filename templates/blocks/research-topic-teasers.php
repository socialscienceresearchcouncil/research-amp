<?php

$r = array_merge(
	[
		'horizontalSwipe' => false,
		'isEditMode'      => false,
		'numberOfItems'   => 3,
		'selectionType'   => 'random',
		'showLoadMore'    => false,
		'slot1'           => 0,
		'slot2'           => 0,
		'slot3'           => 0,
		'variationType'   => 'grid',
	],
	$args
);

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$is_edit_mode = $r['isEditMode'];

$number_of_items = (int) $r['numberOfItems'];

$offset_query_var = 'topic-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$selection_type = $r['selectionType'];
if ( ! in_array( $selection_type, [ 'alphabetical', 'latest', 'random', 'specific' ], true ) ) {
	$selection_type = 'random';
}

$variation_type = 'list' === $r['variationType'] ? 'list' : 'grid';

$query_args = [
	'post_type'      => 'ramp_topic',
	'posts_per_page' => $number_of_items,
	'offset'         => $offset,
	'fields'         => 'ids',
];

switch ( $selection_type ) {
	case 'alphabetical' :
		$post_orderby = [ 'title' => 'ASC' ];
	break;

	case 'latest' :
		$post_orderby = [ 'date' => 'DESC' ];
	break;

	case 'specific' :
		$post_orderby = 'post__in';
	break;

	case 'random' :
	default :
		$post_orderby = 'rand';
	break;
}

$post__in = null;
if ( 'specific' === $selection_type ) {
	$post__in = [
		(int) $r['slot1'],
		(int) $r['slot2'],
		(int) $r['slot3'],
	];
}

if ( $post__in ) {
	$query_args['post__in'] = $post__in;
}

$query_args['orderby'] = $post_orderby;

$research_topic_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $research_topic_query->found_posts;

$list_classes = [
	'load-more-list',
	'item-type-list',
	'item-type-list-research-topics',
	'research-topic-teasers-' . $variation_type,
];

if ( 'grid' === $variation_type ) {
	$list_classes[] = 'item-type-list-3';
	$list_classes[] = 'item-type-list-flex';
}

$div_classes = [
	'item-type-list-container-' . $variation_type,
	'research-topic-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

if ( $r['horizontalSwipe'] ) {
	$div_classes[] = 'allow-horizontal-swipe';
}

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( ! empty( $research_topic_query->posts ) || ! $r['isEditMode'] ) : ?>
		<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
			<?php foreach ( $research_topic_query->posts as $research_topic ) : ?>
				<li>
					<?php
					ramp_get_template_part(
						'teasers/research-topic',
						[
							'id'             => $research_topic,
							'is_edit_mode'   => $is_edit_mode,
							'variation_type' => $variation_type,
						]
					);
					?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! empty( $r['showLoadMore'] ) && $has_more_pages ) : ?>
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
