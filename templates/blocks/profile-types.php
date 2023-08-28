<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

$item_id = $args['block']->context['postId'];

$profile_obj   = \SSRC\RAMP\Profile::get_instance( $item_id );
$profile_types = $profile_obj->get_profile_types();

if ( ! $profile_types ) {
	return '';
}

?>

<div class="wp-block-research-amp-profile-types profile-types">
	<?php foreach ( $profile_types as $profile_type ) : ?>
		<?php ramp_get_template_part( 'profile-type-label', [ 'label' => $profile_type ] ); ?>
	<?php endforeach; ?>
</div>
