<?php
$r = array_merge(
	[
		'headingText' => __( 'Table of Contents', 'ramp' ),
		'isEditMode'  => false,
		'postId'      => get_queried_object_id(),
	],
	$args
);

if ( $r['isEditMode'] ) {
	// Set up the TOC.
	$toc_post    = get_post( $r['postId'] );
	$toc_content = $toc_post ? $toc_post->content : '';
	$toc_content = \SSRC\RAMP\TOC::process_for_toc( $toc_content, $r['postId'] );
}

$toc = ! empty( $GLOBALS['the_toc'] ) ? $GLOBALS['the_toc'] : '';

if ( ! $toc ) {
	return;
}

$classnames = [
	'wp-block-ramp-table-of-contents',
	'sidebar-section',
	'sidebar-section-collapsible',
	'section-open',
];

?>

<div class="<?php echo esc_attr( implode( ' ', $classnames ) ); ?>">
	<h3 class="sidebar-section-title"><?php echo esc_html( $r['headingText'] ); ?></h3>

	<div class="section-content">
		<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
		<?php echo $toc; ?>
	</div>
</div>
