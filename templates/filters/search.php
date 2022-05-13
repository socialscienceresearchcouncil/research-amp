<?php

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$requested_search_term = isset( $_GET['search-term'] ) ? wp_unslash( $_GET['search-term'] ) : null;

?>

<div class="directory-filter directory-filter-search-term">
	<label for="filter-search-term" class="screen-reader-text"><?php esc_html_e( 'Search term', 'research-amp' ); ?></label>
	<input type="text" class="search-input" id="filter-search-term" name="search-term" value="<?php echo esc_attr( $requested_search_term ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'research-amp' ); ?>" />
</div>
