<?php

$r = array_merge(
	[
		'contentMode'                => 'auto',
		'contentModeResearchTopicId' => 0,
		'isEditMode'                 => false,
		'numberOfItems'              => 4,
		'order'                      => 'latest',
		'showLoadMore'               => false,
	],
	$args
);

$number_of_items = (int) $args['numberOfItems'];

$offset_query_var = 'profile-pag-offset';
$offset           = (int) $GLOBALS['wp_query']->get( $offset_query_var );

$query_args = [
	'post_type'      => 'ramp_profile',
	'offset'         => $offset,
	'posts_per_page' => $number_of_items,
	'orderby'        => 'RAND',
	'tax_query'      => \SSRC\RAMP\Blocks::get_content_mode_tax_query_from_template_args( $r ),
];

$profile_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $profile_query->found_posts;

$div_classes = [
	'profile-teasers',
	'load-more-container',
	'uses-query-arg-' . $offset_query_var,
];

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<ul class="item-type-list item-type-list-flex item-type-list-4 item-type-list-profiles load-more-list">
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
