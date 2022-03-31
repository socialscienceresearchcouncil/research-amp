<?php

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'contentModeProfileId'       => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 3,
		'showRowRules'               => true,
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

$citations = get_posts( $query_args );

$list_classes = [
	'item-type-list',
	'item-type-list-citations',
];

if ( $r['showRowRules'] ) {
	$list_classes[] = 'has-row-rules';
} else {
	$list_classes[] = 'has-no-row-rules';
}

?>

<div class="citation-teasers">
	<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
		<?php foreach ( $citations as $citation ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/citation', [ 'id' => $citation ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
