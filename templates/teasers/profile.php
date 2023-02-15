<?php

$r = array_merge(
	[
		'id'                   => 0,
		'is_edit_mode'         => false,
		'is_featured'          => false,
		'show_item_type_label' => false,
		'title_size'           => 'h-5',
	],
	$args
);

$profile_id = $r['id'];

$is_edit_mode = $r['is_edit_mode'];

$profile_obj = \SSRC\RAMP\Profile::get_instance( $profile_id );

$img_src = $profile_obj->get_avatar_url();

$is_featured = $r['is_featured'];

$background_style = '';
$avatar_class     = 'profile-teaser-avatar profile-avatar-wrapper';
if ( $img_src ) {
	$background_style = 'style="background-image:url(' . esc_attr( $img_src ) . ');"';
	$avatar_class    .= ' has-avatar';
} else {
	$avatar_class    .= ' has-default-avatar';
	$background_style = 'style="background-image:url(' . esc_attr( ramp_get_default_profile_avatar() ) . ');"';
}

$title_class = 'has-' . $r['title_size'] . '-font-size';

?>

<article>
	<div class="profile-teaser">
		<?php if ( $r['show_item_type_label'] ) : ?>
			<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'Profile', 'research-amp' ) ] ); ?>
		<?php endif; ?>

		<div class="teaser-content">
			<div class="teaser-avatar-container">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( ! $is_edit_mode ) : ?>
					<a href="<?php the_permalink( $profile_id ); ?>">
				<?php endif; ?>

				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="<?php echo esc_attr( $avatar_class ); ?>" <?php echo $background_style; ?>>
					<?php if ( $img_src ) : ?>
						<?php // translators: Profile name ?>
						<img class="profile-avatar" alt="<?php echo esc_attr( sprintf( __( 'Profile picture of %s', 'research-amp' ), $profile_obj->get_display_name() ) ); ?>" src="<?php echo esc_attr( $img_src ); ?>" />
					<?php endif; ?>
				</div>

				<?php if ( ! $is_edit_mode ) : ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="profile-teaser-name-and-title">
				<div class="profile-teaser-name">
					<h3 class="<?php echo esc_attr( $title_class ); ?>">
						<?php if ( ! $is_edit_mode ) : ?>
							<a href="<?php the_permalink( $profile_id ); ?>">
						<?php endif; ?>

						<?php echo esc_html( $profile_obj->get_display_name() ); ?>

						<?php if ( ! $is_edit_mode ) : ?>
							</a>
						<?php endif; ?>
					</h3>
				</div>

				<div class="profile-teaser-title">
					<?php echo esc_html( $profile_obj->get_title() ); ?>
				</div>
			</div>
		</div>
	</div>
</article>
