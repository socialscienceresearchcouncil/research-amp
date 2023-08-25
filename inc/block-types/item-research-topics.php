<?php

return [
	'api_version'     => 2,
	'render_callback' => function () {
		ob_start();

		echo '<div class="wp-block-item-research-topics">';

		ramp_get_template_part(
			'research-topic-tags',
			[
				'item_id'       => get_queried_object_id(),
				'display_type ' => 'bubble',
			]
		);

		echo '</div>';

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	},
];
