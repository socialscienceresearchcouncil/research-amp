<?php
$r = array_merge(
	[
		'isEditMode' => false,
		'postId'     => get_queried_object_id(),
	],
	$args
);

$pdf_url = add_query_arg( 'pdf', $r['postId'], get_permalink( $r['postId'] ) );

$post_title = get_the_title( $r['postId'] );
$post_url   = get_permalink( $r['postId'] );

$classnames = [
	'wp-block-ramp-social-buttons',
	'sidebar-section',
];

?>

<div class="<?php echo esc_attr( implode( ' ', $classnames ) ); ?>">
	<div class="social-buttons-links" data-title="<?php echo esc_attr( $post_title ); ?>" data-url="<?php echo esc_attr( $post_url ); ?>">
		<a class="social-button social-button-facebook"><span class="screen-reader-text"><?php esc_html_e( 'Send to Facebook', 'ramp' ); ?></span></a>
		<a class="social-button social-button-twitter"><span class="screen-reader-text"><?php esc_html_e( 'Send to Twitter', 'ramp' ); ?></span></a>

		<?php if ( class_exists( 'DKPDF' ) ) : ?>
			<a class="social-button social-button-download" href="<?php echo esc_url( $pdf_url ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Download as PDF', 'ramp' ); ?></span></a>
		<?php endif; ?>
	</div>

	<span class="altmetrics-social-button">
		<?php /*get_template_part( 'parts/altmetrics-badge' ); */?>
	</span>
</div>
