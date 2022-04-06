<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode' => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		$post = get_post( get_the_ID() );
		if ( ! $post ) {
			return '';
		}

		switch ( $post->post_type ) {
			case 'ramp_article' :
				$teaser_template = 'article';
			break;

			case 'ramp_citation' :
				$teaser_template = 'citation';
			break;

			case 'tribe_events' :
				$teaser_template = 'event';
			break;

			case 'post' :
				$teaser_template = 'news-item';
			break;

			case 'ramp_profile' :
				$teaser_template = 'profile';
			break;

			case 'ramp_review' :
				$teaser_template = 'research-review';
			break;

			case 'ramp_topic' :
				$teaser_template = 'research-topic';
			break;
		}

		if ( empty( $teaser_template ) ) {
			return '';
		}

		$atts['show_item_type_label'] = true;
		$atts['show_research_topics'] = true;
		$atts['title_size']           = 'h-4';

		$template_args = array_merge(
			$atts,
			[
				'content' => $content,
				'block'   => $block,
			]
		);

		ob_start();

		echo '<div class="wp-block-ramp-search-result-teaser">';
		ramp_get_template_part( 'teasers/' . $teaser_template, $template_args );
		echo '</div>';

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	},
];
