<?php
$r = array_merge(
	[
		'altmetricsEnabled'   => true,
		'altmetricsThreshold' => 20,
		'isEditMode'          => false,
		'postId'              => get_queried_object_id(),
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

$doi = get_post_meta( $r['postId'], 'doi', true );

?>

<div class="<?php echo esc_attr( implode( ' ', $classnames ) ); ?>">
	<div class="social-buttons-links" data-title="<?php echo esc_attr( $post_title ); ?>" data-url="<?php echo esc_attr( $post_url ); ?>">
		<a class="social-button social-button-facebook"><span class="screen-reader-text"><?php esc_html_e( 'Send to Facebook', 'research-amp' ); ?></span></a>
		<a class="social-button social-button-twitter"><span class="screen-reader-text"><?php esc_html_e( 'Send to Twitter', 'research-amp' ); ?></span></a>

		<?php if ( class_exists( 'DKPDF' ) ) : ?>
			<a class="social-button social-button-download" href="<?php echo esc_url( $pdf_url ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Download as PDF', 'research-amp' ); ?></span></a>
		<?php endif; ?>
	</div>

	<span class="altmetrics-wrapper">
		<?php if ( $doi && $r['altmetricsEnabled'] ) : ?>
			<span
				class='altmetric-embed'
				data-badge-type='donut'
				data-badge-popover='left'
				data-doi="<?php echo esc_attr( $doi ); ?>"
				data-hide-less-than="<?php echo esc_attr( $r['altmetricsThreshold'] ); ?>"
			></span>
		<?php endif; ?>
	</span>
</div>
