<?php

return [
	'api_version'     => 1,
	'attributes'      => [
		'isEditMode'    => [
			'type'    => 'boolean',
			'default' => false,
		],
		'researchTopic' => [
			'type'    => 'string',
			'default' => 'auto',
		],
	],
	'render_callback' => function( $atts, $b, $c ) {
		$tags = array_map(
			function( $term ) {
				return sprintf(
					'<a class="ramp-research-topic-tag" href="%s">%s</a>',
					esc_url( get_term_link( $term ) ),
					esc_html( $term->name )
				);
			},
			get_the_terms( get_queried_object(), 'ssrc_research_topic' )
		);

		// How is there no way in WP to do this?
		$style_declarations = [];
		if ( ! empty( $atts['style']['spacing'] ) ) {
			$spacing = $atts['style']['spacing'];
			foreach ( [ 'margin', 'padding' ] as $css_prop ) {
				if ( ! isset( $spacing[ $css_prop ] ) ) {
					continue;
				}

				if ( is_string( $spacing[ $css_prop ] ) ) {
					$style_declarations[] = $css_prop . ':' . esc_attr( $spacing[ $css_prop ] );
				} else {
					foreach ( [ 'top', 'right', 'bottom', 'left' ] as $type ) {
						if ( isset( $spacing[ $css_prop ][ $type ] ) ) {
							$style_declarations[] = $css_prop . '-' . $type . ':' . esc_attr( $spacing[ $css_prop ][ $type ] );
						}
					}
				}

			}
		}

		return sprintf(
			'<div style="%s">%s</div>',
			implode( ';', $style_declarations ),
			implode( '', $tags )
		);
	},
];
