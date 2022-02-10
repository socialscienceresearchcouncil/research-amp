<?php

$research_topic_id = \SSRC\RAMP\Blocks::get_research_topic_from_template_args( $args );

$variation_type = isset( $args['variationType'] ) && in_array( $args['variationType'], [ 'single', 'two', 'three' ], true ) ? $args['variationType'] : 'single';

$featured_item_id = null;
if ( 'three' === $variation_type ) {
	$featured_item_id = ! empty( $args['featuredItemId'] ) ? (int) $args['featuredItemId'] : null;
	if ( ! $featured_item_id ) {
		$variation_type = 'two';
	}
}

$posts_per_page = 'single' === $variation_type ? 3 : 5;

$query_args = [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
];

if ( $featured_item_id ) {
	$query_args['post__not_in'] = [ $featured_item_id ];
}

if ( $research_topic_id ) {
	$rt_map     = ramp_app()->get_cpttax_map( 'research_topic' );
	$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic_id );

	$query_args['tax_query'] = [
		[
			'taxonomy' => 'ramp_assoc_topic',
			'terms'    => $rt_term_id,
			'field'    => 'term_id',
		],
	];
}

$news_items = get_posts( $query_args );

$variation_class = 'single' === $variation_type ? 'item-type-list-3' : 'item-type-list-wrap';

?>

<div class="news-item-teasers">
	<?php if ( $featured_item_id ) : ?>
		<div class="featured-news-item">
			<?php ramp_get_template_part( 'teasers/news-item-featured', [ 'id' => $featured_item_id ] ); ?>
		</div>
	<?php endif; ?>

	<ul class="item-type-list item-type-list-flex <?php echo esc_attr( $variation_class ); ?> item-type-list-news-items">
		<?php $count = 0; ?>
		<?php foreach ( $news_items as $news_item ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/news-item', [ 'id' => $news_item->ID ] ); ?>
			</li>

			<?php
			$count++;
			if ( ( 'two' === $variation_type || 'three' === $variation_type ) && 3 === $count ) {
				echo '<li class="break-row"></li>';
			}
			?>
		<?php endforeach; ?>
	</ul>
</div>
