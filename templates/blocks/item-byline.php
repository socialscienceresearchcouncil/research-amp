<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

$item_id = $args['block']->context['postId'];

if ( 'ramp_review_version' === $args['block']->context['postType'] ) {
	$review_version = new \SSRC\RAMP\LitReviews\Version( $item_id );
	$parent_item_id = $review_version->get_parent()->ID;
} else {
	$parent_item_id = $item_id;
}

$author_links  = \SSRC\RAMP\Profile::get_profile_links_for_post( $parent_item_id );
$author_string = implode( ', ', $author_links );

if ( ! empty( $args['showPublicationDate'] ) ) {
	$byline = sprintf(
		/* translators: 1. author link, 2. publication date */
		esc_html__( 'By %1$s on %2$s', 'ramp' ),
		'<span class="byline-author">' . $author_string . '</span>',
		'<span class="byline-publication-date">' . esc_html( get_the_date( '', $item_id ) ) . '</span>'
	);
} else {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'ramp' ),
		'<span class="byline-author">' . $author_string . '</span>'
	);
}

?>

<div class="wp-block-ramp-item-byline">
	<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
	<?php echo $byline; ?>
</div>
