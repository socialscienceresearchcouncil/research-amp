<?php

$the_post_id = ! empty( $args['postId'] ) ? $args['postId'] : get_the_ID();

$the_post = get_post( $the_post_id );
if ( ! $the_post ) {
	return;
}

$template_args = [
	'id'                    => $the_post_id,
	'is_edit_mode'          => ! empty( $atts['isEditMode'] ),
	'is_search_result'      => true,
	'show_byline'           => true,
	'show_item_type_label'  => true,
	'show_publication_date' => true,
	'show_research_topics'  => true,
	'title_size'            => 'h-4',
];

switch ( $the_post->post_type ) {
	case 'ramp_article' :
		$teaser_template = 'article';
	break;

	case 'ramp_citation' :
		$teaser_template = 'citation';
	break;

	case 'tribe_events' :
		$teaser_template = 'event';
	break;

	case 'ramp_news_item' :
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

echo '<div class="wp-block-research-amp-search-result-teaser">';
ramp_get_template_part( 'teasers/' . $teaser_template, $template_args );
echo '</div>';
