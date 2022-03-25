<?php

return [
	'api_version'     => 2,
	'attributes'      => [
		'contentMode'                => [
			'type'    => 'string',
			'enum'    => [ 'auto', 'all', 'advanced' ],
			'default' => 'auto',
		],
		'contentModeResearchTopicId' => [
			'type'    => 'integer',
			'default' => 0,
		],
		'isEditMode'                 => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems'              => [
			'type'    => 'string',
			'default' => '3',
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
			'default' => true,
		],
	],
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'profile-teasers', $atts );
	},
];
