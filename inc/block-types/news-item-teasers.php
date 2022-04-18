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
		'showByline'                 => [
			'type'    => 'boolean',
			'default' => true,
		],
		'showFeaturedItem'           => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showImage'                  => [
			'type'    => 'boolean',
			'default' => true,
		],
		'showLoadMore'               => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showPublicationDate'        => [
			'type'    => 'boolean',
			'default' => true,
		],
		'showResearchTopics'         => [
			'type'    => 'boolean',
			'default' => true,
		],
		'showRowRules'               => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showVariationTypeButtons'   => [
			'type'    => 'boolean',
			'default' => true,
		],
		'titleSize'                  => [
			'type'    => 'string',
			'enum'    => [ 'h-1', 'h-2', 'h-3', 'h-4', 'h-5', 'h-6' ],
			'default' => 'h-4',
		],
		'variationType'              => [
			'type'    => 'string',
			'enum'    => [ 'grid', 'list' ],
			'default' => 'grid',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'news-item-teasers', $atts );
	},
];
