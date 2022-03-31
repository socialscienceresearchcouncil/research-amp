<?php

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_search_term = isset( $_GET['search-term'] ) ? wp_unslash( $_GET['search-term'] ) : null;

?>

<div class="directory-filter">
	<label for="citation-search-term" class="screen-reader-text"><?php esc_html_e( 'Search term', 'ramp' ); ?></label>
	<input type="text" class="search-input" id="citation-search-term" name="search-term" value="<?php echo esc_attr( $requested_search_term ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'ramp' ); ?>" />
</div>
