<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'isEditMode' => [
			'type'    => 'boolean',
			'default' => false,
		],
		'postId'      => [
			'type'    => 'integer',
			'default' => 0,
		],
	],
	'render_callback' => function( $atts, $content, $block ) {
		$post_id = ! empty( $atts['postId'] ) ? $atts['postId'] : get_the_ID();

		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$template_args = [
			'id'                    => $post_id,
			'is_edit_mode'          => ! empty( $atts['isEditMode'] ),
			'is_search_result'      => true,
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

				$template_args['show_excerpt'] = false;
				$template_args['show_image']   = false;
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
