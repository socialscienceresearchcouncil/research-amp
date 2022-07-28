<?php

$author = isset( $args['author'] ) ? $args['author'] : '';
$date   = isset( $args['date'] ) ? $args['date'] : '';

$author_html = '';
if ( ! empty( $args['author'] ) ) {
	$author_html = '<span class="byline-author">' . $args['author'] . '</span>';
}

$date_html = '';
if ( ! empty( $args['date'] ) ) {
	$date_html = '<span class="byline-publication-date">' . esc_html( $args['date'] ) . '</span>';
}

if ( $author_html && $date_html ) {
	$byline = sprintf(
		/* translators: 1. author link, 2. publication date */
		esc_html__( 'By %1$s on %2$s', 'research-amp' ),
		$author_html,
		$date_html
	);
} elseif ( $author_html ) {
	$byline = sprintf(
		/* translators: author link */
		esc_html__( 'By %s', 'research-amp' ),
		$author_html
	);
} elseif ( $date_html ) {
	$byline = sprintf(
		/* translators: publication date */
		esc_html__( 'On %s', 'research-amp' ),
		$date_html
	);
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $byline;
