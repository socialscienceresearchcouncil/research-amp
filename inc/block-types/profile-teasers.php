<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'contentMode'                => [
			'type'    => 'string',
			'enum'    => [ 'auto', 'all', 'advanced', 'featured' ],
			'default' => 'auto',
		],
		'contentModeResearchTopicId' => [
			'type'    => 'integer',
			'default' => 0,
		],
		'horizontalSwipe'            => [
			'type'    => 'boolean',
			'default' => true,
		],
		'isEditMode'                 => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems'              => [
			'type'    => 'integer',
			'default' => '4',
		],
		'order'                      => [
			'type'    => 'string',
			'default' => 'alphabetical',
		],
		'showLoadMore'               => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showRowRules'               => [
			'type'    => 'boolean',
			'default' => false,
		],
	],
	'render_callback' => function ( $atts ) {
		return ramp_render_block( 'profile-teasers', $atts );
	},
];
