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
			'type'    => 'integer',
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
			'default' => 'alphabetical',
		],
		'researchTopic'              => [
			'type'    => 'string',
			'default' => 'auto',
		],
		'showFeaturedItem'           => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showLoadMore'               => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showRowRules'               => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showPublicationDate'        => [
			'type'    => 'boolean',
			'default' => true,
		],
		'variationType'              => [
			'type'    => 'string',
			'enum'    => [ 'grid', 'list', 'featured' ],
			'default' => 'grid',
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'article-teasers', $atts );
	},
];
