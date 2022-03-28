<?php
$r = array_merge(
	[
		'citationText' => '',
		'headingText'  => __( 'Cite This', 'ramp' ),
		'helpText'     => __( 'Copy and paste the text below to cite this item.', 'ramp' ),
		'isEditMode'   => false,
		'postId'       => get_queried_object_id(),
	],
	$args
);

if ( $r['isEditMode'] ) {
	$citation = $r['citationText'];
} else {
	$citation = get_post_meta( $r['postId'], 'formatted_citation', true );
}

if ( ! $citation ) {
	return;
}

$classnames = [
	'wp-block-ramp-cite-this',
	'sidebar-section',
	'sidebar-section-collapsible',
	'section-open',
];

?>

<div class="<?php echo esc_attr( implode( ' ', $classnames ) ); ?>">
	<h3 class="sidebar-section-title"><?php echo esc_html( $r['headingText'] ); ?></h3>

	<div class="section-content">
		<p class="cite-this-help-text"><?php echo wp_kses_post( $r['helpText'] ); ?></p>

		<div class="cite-this-citation"><?php echo esc_html( $citation ); ?></div>
	</div>
</div>
