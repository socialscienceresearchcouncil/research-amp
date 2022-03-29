<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

$item_id = $args['block']->context['postId'];

switch ( $args['block']->context['postType'] ) {
	case 'ramp_review_version' :
		$item_type_label = __( 'Research Review', 'ramp' );
	break;

	case 'ramp_article' :
		$article_types = array_map(
			function( $term ) {
				return $term->name;
			},
			get_the_terms( $item_id, 'ramp_article_type' )
		);

		$item_type_label = $article_types ? $article_types[0] : '';
	break;
}

if ( ! $item_type_label ) {
	return;
}

?>

<div class="wp-block-ramp-item-type-label">
	<?php echo esc_html( $item_type_label ); ?>
</div>
