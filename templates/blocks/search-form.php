<?php

wp_enqueue_script( 'ramp-directory-filters' );
wp_enqueue_style( 'ramp-directory-filters' );

static $instance_id = 0;

$r = array_merge(
	[
		'buttonText'        => __( 'Filter Results', 'ramp' ),
		'label'             => __( 'Search Results for', 'ramp' ),
		'placeholder'       => __( 'Enter search terms', 'ramp' ),
		'typeSelectorLabel' => __( 'Filter by content type', 'ramp' ),
	],
	$args
);

$instance_id++;

$input_id      = 'wp-block-search__input-' . $instance_id;
$input_markup  = '';
$button_markup = '';

$label_inner_html = wp_kses_post( $r['label'] );

$label_markup = sprintf(
	'<label for="%1$s" class="wp-block-search__label search-form-label has-h-3-font-size">%2$s</label>',
	esc_attr( $input_id ),
	$label_inner_html
);

$input_markup = sprintf(
	'<input type="search" id="%s" class="wp-block-search__input search-input" name="s" value="%s" placeholder="%s" />',
	$input_id,
	esc_attr( get_search_query() ),
	esc_attr( $r['placeholder'] )
);

$button_internal_markup = wp_kses_post( $r['buttonText'] );

$button_markup = sprintf(
	'<div class="wp-block-button is-style-primary"><button type="submit" class="wp-block-button__link">%s</button></div>',
	$button_internal_markup
);

$field_markup = sprintf(
	'<div class="directory-filter">%s</div>',
	$input_markup
);

$requested_type = \SSRC\RAMP\Search::get_requested_search_type();
$all_types      = \SSRC\RAMP\Search::get_search_item_types();

$form_classes = [
	'wp-block-ramp-search-form',
];

?>

<h1 class="screen-reader-text"><?php echo esc_html( $r['label'] ); ?></h1>

<form class="<?php echo esc_attr( implode( ' ', $form_classes ) ); ?>" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="search-form-top">
		<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo $label_markup; ?>
		<?php echo $field_markup; ?>
		<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="search-form-bottom">

		<label class="search-type-container-label" for="search-type-selector"><?php echo esc_html( $r['typeSelectorLabel'] ); ?></label>

		<div class="search-type-select-container">
			<select id="search-type-selector" class="pretty-select" name="search-type">
				<option value="" <?php selected( '', $requested_type ); ?>><?php esc_html_e( 'All content types', 'ramp' ); ?></option>

				<?php foreach ( $all_types as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $requested_type ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo $button_markup; ?>
	</div>
</form>

