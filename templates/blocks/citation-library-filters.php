<?php

wp_enqueue_style( 'ramp-directory-filters' );
wp_enqueue_script( 'ramp-directory-filters' );

?>

<div class="directory-filters-container">
	<div class="directory-filter-toggle">
		<button id="directory-filter-toggle-button" class="filter-toggle" aria-expanded="false" aria-controls="directory-filters">
			<h3><?php esc_html_e( 'Filter Citations', 'ramp' ); ?></h3>
		</button>
	</div>

	<div id="directory-filters" role="region" aria-labelledby="directory-filter-toggle-button" class="directory-filters clearfix">
		<form method="get" class="directory-filter-form" action="">
			<?php ramp_get_template_part( 'filters/search' ); ?>

			<div class="filter-by-legend">
				<?php esc_html_e( 'Filter by:', 'ramp' ); ?>
			</div>

			<?php ramp_get_template_part( 'filters/research-topic' ); ?>
			<?php ramp_get_template_part( 'filters/submit' ); ?>
		</form>
	</div>
</div>
