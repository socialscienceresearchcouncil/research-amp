<?php

$r = array_merge(
	[
		'focusTag'     => '',
		'showLoadMore' => false,
	],
	$args
);

$query_args = [
	'post_type'   => 'any',
	'post_status' => 'publish',
	'tax_query'   => [
		[
			'taxonomy' => 'ramp_focus_tag',
			'field'    => 'slug',
			'terms'    => $r['focusTag'],
		],
	],
];

$content_query = new WP_Query( $query_args );

$has_more_pages = ( $offset + $number_of_items ) <= $content_query->found_posts;

$div_classes = [];

$list_classes = [
	'item-type-list',
];

?>

<div class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>">
	<?php if ( ! empty( $content_query->posts ) ) : ?>
		<div class="">
			<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
				<?php foreach ( $content_query->posts as $content_item ) : ?>
					<li>
						<?php
						ramp_get_template_part(
							'blocks/search-result-teaser',
							[
								'postId' => $content_item->ID,
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
						'is_edit_mode'    => $r['isEditMode'],
						'offset'          => $offset,
						'query_var'       => $offset_query_var,
						'number_of_items' => $number_of_items,
					]
				);
				?>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<?php ramp_get_template_part( 'teasers-no-content' ); ?>
	<?php endif; ?>
</div>
