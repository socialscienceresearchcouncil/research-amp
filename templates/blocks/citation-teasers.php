<?php

wp_enqueue_style( 'wp-block-button' );
wp_enqueue_style( 'wp-block-buttons' );

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'order'                      => 'addedDate',
		'showLoadMore'               => false,
		'showRowRules'               => false,
	],
	$args
);

$number_of_items = (int) $r['numberOfItems'];

$query_args = [
	'post_type'      => 'ramp_citation',
	'post_status'    => 'publish',
	'posts_per_page' => $number_of_items,
	'fields'         => 'ids',
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

if ( 'featured' === $r['contentMode'] ) {
	$query_args['meta_query'] = [
		[
			'key'     => 'is_featured',
			'value'   => '1',
			'compare' => '=',
		],
	];
}

if ( 'publicationDate' === $r['order'] ) {
	$query_args['orderby']  = 'meta_value';
	$query_args['meta_key'] = 'publication_date';
	$query_args['order']    = 'DESC';
} else {
	$query_args['orderby'] = 'date';
	$query_args['order']   = 'DESC';
}

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$requested_topic  = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;
$requested_search = isset( $_GET['search-term'] ) ? wp_unslash( $_GET['search-term'] ) : null;
// phpcs:enable WordPress.Security.NonceVerification.Recommended

if ( $requested_topic ) {
	$requested_topic_term = get_term_by( 'slug', $requested_topic, 'ramp_assoc_topic' );

	$query_args['tax_query']['assoc_topic'] = [
		'taxonomy' => 'ramp_assoc_topic',
		'terms'    => $requested_topic_term->term_id,
		'field'    => 'term_id',
	];
}

if ( $requested_search ) {
	$query_args['s'] = $requested_search;
}

$offset_query_var = 'citation-pag-offset';
$offset           = ramp_get_pag_offset( $offset_query_var );

$query_args['offset'] = $offset;

$citation_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $citation_query->found_posts;

$list_classes = [
	'item-type-list',
	'item-type-list-citations',
	'load-more-list',
];

if ( $r['showRowRules'] ) {
	$list_classes[] = 'has-row-rules';
} else {
	$list_classes[] = 'has-no-row-rules';
}

$div_classes = [
	'citation-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( is_post_type_archive( 'ramp_citation' ) && empty( $citation_query->posts ) ) : ?>
		<p class="no-results-message"><?php esc_html_e( 'No results. Try a different search term or filter.', 'research-amp' ); ?></p>
	<?php else : ?>
		<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
			<?php foreach ( $citation_query->posts as $citation ) : ?>
				<li>
					<?php
					ramp_get_template_part(
						'teasers/citation',
						[
							'id'                   => $citation,
							'is_edit_mode'         => $r['isEditMode'],
							'show_research_topics' => true,
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
					'isEditMode'      => $r['isEditMode'],
					'offset'          => $offset,
					'query_var'       => $offset_query_var,
					'number_of_items' => $number_of_items,
				]
			);
			?>
		<?php endif; ?>
	<?php endif; ?>
</div>
