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

$input_id         = 'wp-block-search__input-' . ++$instance_id;
$input_markup     = '';
$button_markup    = '';

$label_inner_html = wp_kses_post( $r['label'] );

$label_markup = sprintf(
	'<label for="%1$s" class="wp-block-search__label search-form-label has-h-3-font-size">%2$s</label>',
	esc_attr( $input_id ),
	$label_inner_html
);

$input_markup  = sprintf(
	'<input type="search" id="%s" class="wp-block-search__input search-input" name="s" value="%s" placeholder="%s" />',
	$input_id,
	esc_attr( get_search_query() ),
	esc_attr( $r['placeholder'] ),
);

$button_internal_markup = wp_kses_post( $r['buttonText'] );

$button_markup = sprintf(
	'<div class="wp-block-button is-style-primary"><button type="submit" class="wp-block-button__link">%s</button>',
	$button_internal_markup
);

$field_markup = sprintf(
	'<div class="directory-filter">%s</div>',
	$input_markup
);

$requested_type = isset( $_GET['search-type'] ) ? wp_unslash( $_GET['search-type'] ) : '';

$types = ramp_get_search_item_types();

if ( ! isset( $types[ $requested_type ] ) ) {
	$requested_type = '';
}

$form_classes = [
	'wp-block-ramp-search-form',
];

?>

<h2 class="screen-reader-text"><?php echo esc_html( $r['label'] ); ?></h2>

<form class="<?php echo esc_attr( implode( ' ', $form_classes ) ); ?>" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="search-form-top">
		<?php echo $label_markup; ?>
		<?php echo $field_markup; ?>
	</div>

	<div class="search-form-bottom">

		<label class="search-type-container-label" for="search-type-selector"><?php echo esc_html( $r['typeSelectorLabel'] ); ?></label>

		<div class="search-type-select-container">
			<select id="search-type-selector" class="pretty-select" name="search-type">
				<option value="" <?php selected( '', $requested_type ); ?>><?php esc_html_e( 'All content types', 'ramp' ); ?></option>

				<?php foreach ( $types as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $requested_type ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php echo $button_markup; ?>
	</div>
</form>

