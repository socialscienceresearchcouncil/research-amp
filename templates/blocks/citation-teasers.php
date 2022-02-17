<?php

$research_topic_id = \SSRC\RAMP\Blocks::get_research_topic_from_template_args( $args );

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : 3;

$show_filters = ! empty( $args['showFilters'] );
if ( $show_filters ) {
	$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );

	$current_rt = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;

	if ( $current_rt ) {
		$current_rt_term = get_term_by( 'slug', $current_rt, 'ramp_topic' );
		// translators: research topic name
		$current_message = sprintf( __( 'Recently added citations for: "%s"', 'ramp' ), $current_rt_term->name );
	} else {
		$current_message = __( 'Recently added citations for "All Research Topics"', 'ramp' );
	}
}

$query_args = [
	'post_type'      => 'ramp_citation',
	'post_status'    => 'publish',
	'posts_per_page' => $number_of_items,
	'fields'         => 'ids',
];

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

$citations = get_posts( $query_args );

$toggle_class = empty( $_GET['research-topic'] ) ? 'toggle-closed' : '';

$research_topics = get_posts( [
	'post_type'      => 'ramp_topic',
	'posts_per_page' => -1,
	'orderby'        => [ 'title' => 'ASC' ],
] );
?>

<?php if ( $show_filters ) : ?>
	<?php
	wp_enqueue_style( 'ramp-directory-filters' );
	wp_enqueue_script( 'ramp-directory-filters' );
	?>

	<div class="directory-filters-container no-js <?php echo $toggle_class; ?>">
		<div class="directory-filter-toggle">
			<button id="directory-filter-toggle-button" class="filter-toggle" aria-expanded="false" aria-controls="directory-filters">
				<h3><?php esc_html_e( 'Filter Citations', 'ramp' ); ?></h3>
			</button>
		</div>

		<div id="directory-filters" role="region" aria-labelledby="directory-filter-toggle-button" class="directory-filters clearfix">
			<form method="get" action="<?php echo esc_attr( get_permalink( get_queried_object() ) ); ?>">
				<div class="directory-filter">
					<label for="research-topic"><?php esc_html_e( 'Filter Citations:', 'ramp' ); ?></label>
					<select id="research-topic" class="pretty-select directory-filter-dropdown" name="research-topic" placeholder="<?php esc_attr_e( 'All Research Topics', 'ramp' ); ?>">
						<option <?php selected( ! $current_rt ); ?> value=""><?php esc_html_e( 'All Research Topics', 'ramp' ); ?></option>
						<?php
						foreach ( $research_topics as $research_topic ) {
							$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic->ID );
							$rt_term    = get_term( $rt_term_id, 'ramp_assoc_topic' );

							printf(
								'<option value="%s" %s>%s</option>',
								esc_attr( $rt_term->slug ),
								selected( $rt_term->slug, $current_rt, false ),
								esc_html( $rt_term->name )
							);
						}
						?>
					</select>
				</div>
				<div class="directory-filters-submit">
					<input class="mw-button mw-button-primary" type="submit" value="<?php esc_attr_e( 'Apply Filters', 'ramp' ); ?>" />
				</div>
			</form>
		</div>
	</div>
<?php endif; ?>

<div class="citation-teasers">
	<ul class="item-type-list item-type-list-citations">
		<?php foreach ( $citations as $citation ) : ?>
			<li>
				<?php ramp_get_template_part( 'teasers/citation', [ 'id' => $citation ] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
