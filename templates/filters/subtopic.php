<?php

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_subtopic = isset( $_GET['subtopic'] ) ? wp_unslash( $_GET['subtopic'] ) : null;

$subtopics = \SSRC\RAMP\Profile::get_terms_belonging_to_profiles( 'ramp_focus_tag' );

if ( ! in_array( $requested_subtopic, wp_list_pluck( $subtopics, 'slug' ), true ) ) {
	$requested_subtopic = null;
}

?>

<div class="directory-filter directory-filter-subtopic">
	<label for="subtopic" class="screen-reader-text"><?php esc_html_e( 'Filter by Subtopic', 'ramp' ); ?></label>
	<select id="subtopic" class="pretty-select directory-filter-dropdown" name="subtopic" placeholder="<?php esc_attr_e( 'All Subtopics', 'ramp' ); ?>">
		<option <?php selected( ! $requested_subtopic ); ?> value=""><?php esc_html_e( 'All Subtopics', 'ramp' ); ?></option>
		<?php
		foreach ( $subtopics as $subtopic_term ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $subtopic_term->slug ),
				selected( $subtopic_term->slug, $requested_subtopic, false ),
				esc_html( $subtopic_term->name )
			);
		}
		?>
	</select>
</div>
