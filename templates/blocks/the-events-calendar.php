<?php

use Tribe\Events\Views\V2\Template_Bootstrap;

?>

<div class="wp-block-research-amp-the-events-calendar">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo tribe( Template_Bootstrap::class )->get_view_html();
	?>
</div>
