<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

$item_id = $args['block']->context['postId'];

if ( 'ramp_news_item' === get_post_type( $item_id ) && function_exists( 'pressforward' ) ) {
	$date_string   = '';
	$author_string = pressforward( 'controller.metas' )->retrieve_meta( get_the_ID(), 'item_author' );

	if ( 'Author on Source' === $author_string ) {
		$author_string = '';
	}

	$publication_date = get_post_meta( get_queried_object_id(), 'publication_date', true );
	if ( $publication_date ) {
		$date_string = gmdate( 'F j, Y', strtotime( $publication_date ) );
	}
} else {
	$author_links  = \SSRC\RAMP\Profile::get_profile_links_for_post( $item_id );
	$author_string = implode( ', ', $author_links );

	$date_string = get_the_date( '', $item_id );
}

$byline_args = [];
if ( $author_string ) {
	$byline_args['author'] = $author_string;
}

if ( ! empty( $args['showPublicationDate'] ) ) {
	$byline_args['date'] = $date_string;
}

?>

<div class="wp-block-research-amp-item-byline">
	<?php ramp_get_template_part( 'byline', $byline_args ); ?>
</div>
