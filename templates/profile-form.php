<?php

namespace SSRC\RAMP;

// todo - frontend
$profile_id = is_admin() ? $GLOBALS['post_ID'] : 0;

$profile = Profile::get_instance( $profile_id );

?>

<table class="form-table">
	<tr>
		<th scope="row">
			<label for="profile-first-name">First Name</label>
		</th>
		<td>
			<input name="profile-info[first_name]" id="profile-first-name" value="<?php echo esc_attr( $profile->get_first_name() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="profile-last-name">Last Name</label>
		</th>
		<td>
			<input name="profile-info[last_name]" id="profile-last-name" value="<?php echo esc_attr( $profile->get_last_name() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="profile-title">Title / Affiliation</label>
		</th>
		<td>
			<input name="profile-info[title]" id="profile-title" value="<?php echo esc_attr( $profile->get_title() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="profile-homepage-url">Homepage URL</label>
		</th>
		<td>
			<input name="profile-info[homepage_url]" id="profile-homepage-url" value="<?php echo esc_attr( $profile->get_homepage_url() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="profile-homepage-url">ORCID ID</label>
		</th>
		<td>
			<input name="profile-info[orcid_id]" id="profile-orcid-id" value="<?php echo esc_attr( $profile->get_orcid_id() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="profile-email-address">Email</label>
		</th>
		<td>
			<input name="profile-info[email_address]" id="profile-email-address" value="<?php echo esc_attr( $profile->get_email_address() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="profile-twitter-id">Twitter</label>
		</th>
		<td>
			<input name="profile-info[twitter_id]" id="profile-twitter-id" value="<?php echo esc_attr( $profile->get_twitter_id() ); ?>" class="widefat" />
		</td>
	</tr>

	<?php if ( current_user_can( 'create_users' ) ) : ?>
		<tr>
			<th scope="row">
				<label for="profile-is-featured">Featured?</label>
			</th>
			<td>
				<input type="checkbox" name="profile-info[is_featured]" id="profile-is-featured" <?php checked( $profile->get_is_featured() ); ?> /> <label for="profile-is-featured">This Profile should have the 'Featured' status, so that it appears on the home page carousel.</input>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="profile-is-advisory">Advisory Board member?</label>
			</th>
			<td>
				<input type="checkbox" name="profile-info[is_advisory]" id="profile-is-advisory" <?php checked( $profile->get_is_advisory() ); ?> /> <label for="profile-is-advisory">This profile belongs to a membef the Advisory Board, and their information will appear in the Advisory Board Members block.</input>
			</td>
		</tr>
	<?php endif; ?>
</table>

<?php wp_nonce_field( 'profile-info-' . $profile_id, 'profile-info-nonce', false ); ?>
