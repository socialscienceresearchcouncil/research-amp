<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

if ( 'ramp_citation' !== $args['block']->context['postType'] ) {
	return '';
}
?>

<div class="wp-block-research-amp-citation-info">
	<?php ramp_get_template_part( 'citation-info', [ 'id' => $args['block']->context['postId'] ] ); ?>
</div>
