<div class="wp-block-ramp-nav-search">
	<button class="nav-search-button">
		<span class="screen-reader-text"><?php esc_html_e( 'Click to search site', 'ramp' ); ?></span>
	</button>

	<div class="nav-search-fields">
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
			<label class="screen-reader-text" for="nav-search-input"><?php esc_html_e( 'Search terms', 'ramp' ); ?></label>
			<input name="s" type="text" id="nav-search-input" class="nav-search-input" />

			<input type="submit" class="search-submit" value="<?php echo esc_html_e( 'Submit', 'ramp' ); ?>" />
		</form>
	</div>
</div>
