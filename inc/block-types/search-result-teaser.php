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

		$template_args = [
			'id'                    => get_the_ID(),
			'show_byline'           => true,
			'show_item_type_label'  => true,
			'show_publication_date' => true,
			'show_research_topics'  => true,
			'title_size'            => 'h-4',
		];

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

		ob_start();

		echo '<div class="wp-block-ramp-search-result-teaser">';
		ramp_get_template_part( 'teasers/' . $teaser_template, $template_args );
		echo '</div>';

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	},
];
