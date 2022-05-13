<?php

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_rt = isset( $_GET['research-topic'] ) ? wp_unslash( $_GET['research-topic'] ) : null;

$research_topics = \SSRC\RAMP\Profile::get_terms_belonging_to_profiles( 'ramp_assoc_topic' );

if ( ! in_array( $requested_rt, wp_list_pluck( $research_topics, 'slug' ), true ) ) {
	$requested_rt = null;
}

?>

<div class="directory-filter directory-filter-research-topic">
	<label for="research-topic" class="screen-reader-text"><?php esc_html_e( 'Filter by Research Topic', 'research-amp' ); ?></label>
	<select id="research-topic" class="pretty-select directory-filter-dropdown" name="research-topic" placeholder="<?php esc_attr_e( 'All Research Topics', 'research-amp' ); ?>">
		<option <?php selected( ! $requested_rt ); ?> value=""><?php esc_html_e( 'All Research Topics', 'research-amp' ); ?></option>
		<?php
		foreach ( $research_topics as $rt_term ) {
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
