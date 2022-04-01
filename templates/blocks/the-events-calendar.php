<?php

use Tribe\Events\Views\V2\Template_Bootstrap;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo tribe( Template_Bootstrap::class )->get_view_html();
