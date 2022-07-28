<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

$item_id = $args['block']->context['postId'];

$author_links  = \SSRC\RAMP\Profile::get_profile_links_for_post( $item_id );
$author_string = implode( ', ', $author_links );

$byline_args = [];
if ( $author_string ) {
	$byline_args['author'] = $author_string;
}

if ( ! empty( $args['showPublicationDate'] ) ) {
	$byline_args['date'] = get_the_date( '', $item_id );
?>

<div class="wp-block-research-amp-item-byline">
	<?php ramp_get_template_part( 'byline', $byline_args ); ?>
</div>
