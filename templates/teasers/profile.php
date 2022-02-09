<?php
$profile_id = $args['id'];

$sp_obj = \SSRC\RAMP\ScholarProfile::get_instance( $profile_id );

$is_featured = ! empty( $args['is_featured'] );

$img_src      = '';
$thumbnail_id = get_post_thumbnail_id( $profile_id );
if ( $thumbnail_id ) {
	$img_details = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
	$img_src     = $img_details[0];
} else {
	$img_src = ramp_get_default_profile_avatar();
}

?>

<article>
	<div class="profile-teaser">
		<a href="<?php the_permalink( $profile_id ); ?>"><div class="profile-teaser-avatar profile-avatar-wrapper"<?php if ( $img_src ) : ?> style="background-image:url(<?php echo esc_attr( $img_src ); ?>);<?php endif; ?>">
			<img class="profile-avatar" alt="<?php echo esc_attr( sprintf( __( 'Profile picture of %s', 'ramp' ), $sp_obj->get_display_name() ) ); ?>" src="<?php echo esc_attr( $img_src ); ?>" />
		</div></a>

		<div class="profile-teaser-name">
			<h1><a href="<?php the_permalink( $profile_id ); ?>"><?php echo esc_html( $sp_obj->get_display_name() ); ?></a></h1>
		</div>

		<div class="profile-teaser-title">
			<?php echo esc_html( $sp_obj->get_title() ); ?>
		</div>
	</div>
</article>
