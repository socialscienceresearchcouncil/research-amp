<?php

wp_enqueue_style( 'ramp-directory-filters' );
wp_enqueue_script( 'ramp-directory-filters' );

?>

<div role="region" class="directory-filters">
	<form method="get" class="directory-filter-form" action="">

		<?php ramp_get_template_part( 'filters/search' ); ?>

		<div class="filter-by-legend">
			<?php esc_html_e( 'Filter by:', 'ramp' ); ?>
		</div>

		<?php ramp_get_template_part( 'filters/research-topic' ); ?>
		<?php ramp_get_template_part( 'filters/subtopic' ); ?>
		<?php ramp_get_template_part( 'filters/submit' ); ?>

	</form>
</div>
