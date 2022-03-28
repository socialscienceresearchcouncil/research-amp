<?php
$r = array_merge(
	[
		'headingText' => __( 'Table of Contents', 'ramp' ),
	],
	$args
);

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
		<?php echo $toc; ?>
	</div>
</div>
