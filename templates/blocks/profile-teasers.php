<?php

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'horizontalSwipe'            => true,
		'isEditMode'                 => false,
		'numberOfItems'              => 4,
		'order'                      => 'latest',
		'showLoadMore'               => false,
		'showRowRules'               => false,
	],
	$args
);

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$requested_topic  = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;
$requested_search = isset( $_GET['search-term'] ) ? wp_unslash( $_GET['search-term'] ) : null;
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$number_of_items = (int) $args['numberOfItems'];

$offset_query_var = 'profile-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$query_args = [
	'post_type'      => 'ramp_profile',
	'offset'         => $offset,
	'posts_per_page' => $number_of_items,
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

$order_args = [ 'alphabetical', 'latest', 'random' ];
$order_arg  = in_array( $r['order'], $order_args, true ) ? $r['order'] : 'alphabetical';

switch ( $order_arg ) {
	case 'alphabetical' :
		$query_args['meta_key'] = 'alphabetical_name';
		$query_args['orderby']  = [ 'meta_value' => 'ASC' ];
	break;

	case 'latest' :
		$query_args['orderby'] = [ 'date' => 'DESC' ];
	break;

	case 'random' :
	default :
		$query_args['orderby'] = 'rand';
	break;
}

if ( $requested_topic ) {
	$requested_topic_term = get_term_by( 'slug', $requested_topic, 'ramp_assoc_topic' );

	$query_args['tax_query']['assoc_topic'] = [
		'taxonomy' => 'ramp_assoc_topic',
		'terms'    => $requested_topic_term->term_id,
		'field'    => 'term_id',
	];
}

// If we're on a singular item, try to use the profiles associated with the item.
if ( 'auto' === $r['contentMode'] && is_singular() && empty( $query_args['tax_query'] ) ) {
	$profile_terms = get_the_terms( get_queried_object(), 'ramp_assoc_profile' );
	if ( $profile_terms ) {
		$p_map = ramp_app()->get_cpttax_map( 'profile' );

		$query_args['post__in'] = array_map( [ $p_map, 'get_post_id_for_term_id' ], wp_list_pluck( $profile_terms, 'term_id' ) );
	}
} elseif ( 'featured' === $r['contentMode'] ) {
	$query_args['meta_query'] = [
		[
			'key'     => 'is_featured',
			'value'   => '1',
			'compare' => '=',
		],
	];
}

if ( $requested_search ) {
	$query_args['s'] = $requested_search;
}

$profile_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $profile_query->found_posts;

$div_classes = [
	'item-type-list-container-grid',
	'profile-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

if ( $r['horizontalSwipe'] ) {
	$div_classes[] = 'allow-horizontal-swipe';
}

$list_classes = [
	'item-type-list',
	'item-type-list-flex',
	'item-type-list-4',
	'item-type-list-profiles',
	'load-more-list',
];

if ( (bool) $r['showRowRules'] ) {
	$list_classes[] = 'has-row-rules';
} else {
	$list_classes[] = 'has-no-row-rules';
}

// For display reasons, the grid must always contain 4n items.
$placeholder_count = ramp_get_placeholder_count( count( $profile_query->posts ), 4 );

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( is_post_type_archive( 'ramp_profile' ) && empty( $profile_query->posts ) ) : ?>
		<p class="no-results-message"><?php esc_html_e( 'No results. Try a different search term or filter.', 'research-amp' ); ?></p>

	<?php elseif ( ! empty( $profile_query->posts ) || ! $r['isEditMode'] ) : ?>
		<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
			<?php foreach ( $profile_query->posts as $profile_post ) : ?>
				<li>
					<?php
					ramp_get_template_part(
						'teasers/profile',
						[
							'id'           => $profile_post->ID,
							'is_edit_mode' => ! empty( $r['isEditMode'] ),
						]
					);
					?>
				</li>
			<?php endforeach; ?>

			<?php for ( $i = 0; $i < $placeholder_count; $i++ ) : ?>
				<li aria-hidden=true"></li>
			<?php endfor; ?>
		</ul>

		<?php if ( ! empty( $args['showLoadMore'] ) && $has_more_pages ) : ?>
			<?php
			ramp_get_template_part(
				'load-more-button',
				[
					'is_edit_mode'    => ! empty( $r['isEditMode'] ),
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
