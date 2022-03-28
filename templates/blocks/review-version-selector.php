<?php
$r = array_merge(
	[
		'headingText' => __( 'Version', 'ramp' ),
	],
	$args
);

$classnames = [
	'wp-block-ramp-review-version-selector',
	'sidebar-section',
];

$version_id = isset( $args['block']->context['postId'] ) ? $args['block']->context['postId'] : 0;

if ( $version_id ) {
	$version  = new \SSRC\RAMP\LitReviews\Version( $version_id );
	$review   = $version->get_parent();
	$versions = \SSRC\RAMP\LitReviews\Version::get( $review->ID );
}

?>

<div class="<?php echo esc_attr( implode( ' ', $classnames ) ); ?>">
	<h3 class="sidebar-section-title"><?php echo esc_html( $r['headingText'] ); ?></h3>

	<label class="screen-reader-text" for="review-version-selector-dropdown"><?php echo esc_html_e( 'Select version', 'ramp' ); ?></label>

	<select id="review-version-selector-dropdown" name="review-version-selector-dropdown" class="review-version-selector-dropdown" autocomplete="off">
		<?php foreach ( $versions as $version ) : ?>
			<option value="<?php echo esc_url( get_permalink( $version->ID ) ); ?>" <?php selected( $version->ID, $version_id ); ?> data-version-name="<?php echo esc_attr( $version->post_name ); ?>"><?php echo esc_html( get_post_meta( $version->ID, 'version_name', true ) ); ?></option>
		<?php endforeach; ?>
	</select>
</div>