<?php

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 4,
		'order'                      => 'latest',
		'showLoadMore'               => false,
		'showRowRules'               => false,
	],
	$args
);

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$requested_topic    = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;
$requested_subtopic = isset( $_GET['subtopic'] ) ? wp_unslash( $_GET['subtopic'] ) : null;
$requested_search   = isset( $_GET['search-term'] ) ? wp_unslash( $_GET['search-term'] ) : null;
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$number_of_items = (int) $args['numberOfItems'];

$offset_query_var = 'profile-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$query_args = [
	'post_type'      => 'ramp_profile',
	'offset'         => $offset,
	'posts_per_page' => $number_of_items,
	'orderby'        => 'RAND',
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

if ( $requested_topic ) {
	$requested_topic_term = get_term_by( 'slug', $requested_topic, 'ramp_assoc_topic' );

	$query_args['tax_query']['assoc_topic'] = [
		'taxonomy' => 'ramp_assoc_topic',
		'terms'    => $requested_topic_term->term_id,
		'field'    => 'term_id',
	];
}

if ( $requested_subtopic ) {
	$requested_subtopic_term = get_term_by( 'slug', $requested_subtopic, 'ramp_focus_tag' );

	$query_args['tax_query']['focus_tag'] = [
		'taxonomy' => 'ramp_focus_tag',
		'terms'    => $requested_subtopic_term->term_id,
		'field'    => 'term_id',
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
	<?php if ( ! empty( $profile_query->posts ) || ! $r['isEditMode'] ) : ?>
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
