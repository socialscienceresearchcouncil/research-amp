<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'featuredArticleId' => [
			'type'    => 'integer',
			'default' => 0,
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'article-teasers-with-featured-article', $atts );
	},
];
