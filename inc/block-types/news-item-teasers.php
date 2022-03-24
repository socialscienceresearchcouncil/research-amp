<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'contentMode'                => [
			'type'    => 'string',
			'enum'    => [ 'auto', 'all', 'advanced' ],
			'default' => 'auto',
		],
		'contentModeProfileId'       => [
			'type'    => 'integer',
			'default' => 0,
		],
		'contentModeResearchTopicId' => [
			'type'    => 'integer',
			'default' => 0,
		],
		'featuredItemId'             => [
			'type'    => 'number',
			'default' => 0,
		],
		'isEditMode'                 => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems'              => [
			'type'    => 'integer',
			'default' => 3,
		],
		'order'                      => [
			'type'    => 'string',
			'default' => 'latest',
		],
		'showFeaturedItem'           => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showLoadMore'               => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showResearchTopics'         => [
			'type'    => 'boolean',
			'default' => true,
		],
		'showPublicationDate'        => [
			'type'    => 'boolean',
			'default' => true,
		],
		'variationType'              => [
			'type'    => 'string', /* WP throws PHP notice if this is not set */
			'enum'    => [ 'grid', 'list' ],
			'default' => 'grid',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'news-item-teasers', $atts );
	},
];
