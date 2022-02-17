<?php

$rt_map = ramp_app()->get_cpttax_map( 'research_topic' );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_rt = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;

$research_topics = get_posts(
	[
		'post_type'      => 'ramp_topic',
		'posts_per_page' => -1,
		'orderby'        => [ 'title' => 'ASC' ],
	]
);

if ( ! in_array( $requested_rt, wp_list_pluck( $research_topics, 'slug' ), true ) ) {
	$requested_rt = null;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$toggle_class = empty( $_GET['research-topic'] ) ? 'toggle-closed' : '';

wp_enqueue_style( 'ramp-directory-filters' );
wp_enqueue_script( 'ramp-directory-filters' );

?>

<div class="directory-filters-container no-js <?php echo esc_attr( $toggle_class ); ?>">
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
					<option <?php selected( ! $requested_rt ); ?> value=""><?php esc_html_e( 'All Research Topics', 'ramp' ); ?></option>
					<?php
					foreach ( $research_topics as $research_topic ) {
						$rt_term_id = $rt_map->get_term_id_for_post_id( $research_topic->ID );
						$rt_term    = get_term( $rt_term_id, 'ramp_assoc_topic' );

						printf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $rt_term->slug ),
							selected( $rt_term->slug, $requested_rt, false ),
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
