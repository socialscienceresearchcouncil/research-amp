<?php
$profile_id = $args['id'];

$profile_obj = \SSRC\RAMP\Profile::get_instance( $profile_id );

$img_src = $profile_obj->get_avatar_url();

$is_featured = ! empty( $args['is_featured'] );

$background_style = '';
$avatar_class     = 'profile-teaser-avatar profile-avatar-wrapper';
if ( $img_src ) {
	$background_style = 'style="background-image:url(' . esc_attr( $img_src ) . ');"';
	$avatar_class    .= ' has-avatar';
} else {
	$avatar_class .= ' has-default-avatar';
}

?>

<article>
	<div class="profile-teaser">
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<a href="<?php the_permalink( $profile_id ); ?>"><div class="<?php echo esc_attr( $avatar_class ); ?>" <?php echo $background_style; ?>>
			<?php if ( $img_src ) : ?>
				<?php // translators: Profile name ?>
				<img class="profile-avatar" alt="<?php echo esc_attr( sprintf( __( 'Profile picture of %s', 'ramp' ), $profile_obj->get_display_name() ) ); ?>" src="<?php echo esc_attr( $img_src ); ?>" />
			<?php endif; ?>
		</div></a>

		<div class="profile-teaser-name">
			<h3 class="has-medium-font-size"><a href="<?php the_permalink( $profile_id ); ?>"><?php echo esc_html( $profile_obj->get_display_name() ); ?></a></h3>
		</div>

		<div class="profile-teaser-title">
			<?php echo esc_html( $profile_obj->get_title() ); ?>
		</div>
	</div>
</article>
