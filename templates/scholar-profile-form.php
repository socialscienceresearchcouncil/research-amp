<?php

namespace SSRC\Disinfo;

// todo - frontend
$scholar_profile_id = is_admin() ? $GLOBALS['post_ID'] : 0;

$scholar_profile = ScholarProfile::get_instance( $scholar_profile_id );

?>

<table class="form-table">
	<tr>
		<th scope="row">
			<label for="scholar-profile-first-name">First Name</label>
		</th>
		<td>
			<input name="scholar-profile-info[first_name]" id="scholar-profile-first-name" value="<?php echo esc_attr( $scholar_profile->get_first_name() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="scholar-profile-last-name">Last Name</label>
		</th>
		<td>
			<input name="scholar-profile-info[last_name]" id="scholar-profile-last-name" value="<?php echo esc_attr( $scholar_profile->get_last_name() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="scholar-profile-title">Title / Affiliation</label>
		</th>
		<td>
			<input name="scholar-profile-info[title]" id="scholar-profile-title" value="<?php echo esc_attr( $scholar_profile->get_title() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="scholar-profile-homepage-url">Homepage URL</label>
		</th>
		<td>
			<input name="scholar-profile-info[homepage_url]" id="scholar-profile-homepage-url" value="<?php echo esc_attr( $scholar_profile->get_homepage_url() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="scholar-profile-homepage-url">ORCID ID</label>
		</th>
		<td>
			<input name="scholar-profile-info[orcid_id]" id="scholar-profile-orcid-id" value="<?php echo esc_attr( $scholar_profile->get_orcid_id() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="scholar-profile-email-address">Email</label>
		</th>
		<td>
			<input name="scholar-profile-info[email_address]" id="scholar-profile-email-address" value="<?php echo esc_attr( $scholar_profile->get_email_address() ); ?>" class="widefat" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="scholar-profile-twitter-id">Twitter</label>
		</th>
		<td>
			<input name="scholar-profile-info[twitter_id]" id="scholar-profile-twitter-id" value="<?php echo esc_attr( $scholar_profile->get_twitter_id() ); ?>" class="widefat" />
		</td>
	</tr>

	<?php if ( current_user_can( 'create_users' ) ) : ?>
		<tr>
			<th scope="row">
				<label for="scholar-profile-is-featured">Featured?</label>
			</th>
			<td>
				<input type="checkbox" name="scholar-profile-info[is_featured]" id="scholar-profile-is-featured" <?php checked( $scholar_profile->get_is_featured() ); ?> /> <label for="scholar-profile-is-featured">This Profile should have the 'Featured' status, so that it appears on the home page carousel.</input>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="scholar-profile-is-advisory">Advisory Board member?</label>
			</th>
			<td>
				<input type="checkbox" name="scholar-profile-info[is_advisory]" id="scholar-profile-is-advisory" <?php checked( $scholar_profile->get_is_advisory() ); ?> /> <label for="scholar-profile-is-advisory">This profile belongs to a membef the Advisory Board, and their information will appear in the Advisory Board Members block.</input>
			</td>
		</tr>
	<?php endif; ?>
</table>

<?php wp_nonce_field( 'scholar-profile-info-' . $scholar_profile_id, 'scholar-profile-info-nonce', false ); ?>
