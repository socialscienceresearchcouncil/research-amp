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
		'isEditMode'                 => [
			'type'    => 'boolean',
			'default' => false,
		],
		'numberOfItems'              => [
			'type'    => 'number',
			'default' => 3,
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
	'render_callback' => function( $atts ) {
		return ramp_render_block( 'citation-teasers', $atts );
	},
];
