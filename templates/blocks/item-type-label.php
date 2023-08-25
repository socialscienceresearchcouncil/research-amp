<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

$item_id = $args['block']->context['postId'];

switch ( $args['block']->context['postType'] ) {
	case 'ramp_article' :
		$article_types = get_the_terms( $item_id, 'ramp_article_type' );

		if ( is_array( $article_types ) ) {
			$article_types = array_map(
				function ( $term ) {
					return $term->name;
				},
				$article_types
			);
		}

		$item_type_label = $article_types ? $article_types[0] : '';
	break;

	case 'ramp_news_item' :
		$item_type_label = __( 'News Item', 'research-amp' );
	break;

	default :
		$pt_object = get_post_type_object( $args['block']->context['postType'] );

		if ( $pt_object && ! empty( $pt_object->labels->singular_name ) ) {
			$item_type_label = $pt_object->labels->singular_name;
		}
	break;
}

if ( ! $item_type_label ) {
	return;
}

?>

<div class="wp-block-research-amp-item-type-label">
	<?php echo esc_html( $item_type_label ); ?>
</div>
