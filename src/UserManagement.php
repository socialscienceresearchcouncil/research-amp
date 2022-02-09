<?php

namespace SSRC\RAMP;

class UserManagement {
	// @todo Does this remain?
	protected static $categories;

	public function init() {
		add_action( 'register_form', [ $this, 'register_form' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_filter( 'registration_errors', [ $this, 'registration_errors' ], 10, 3 );
		add_action( 'register_new_user', [ $this, 'register_new_user' ] );

		add_action( 'edit_user_profile', [ $this, 'user_category_display' ] );

		add_filter( 'wp_new_user_notification_email', [ $this, 'registration_email' ], 10, 2 );

		// Admin
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'meta_box_save_cb' ] );

		// Front-end save
		add_action( 'template_redirect', [ $this, 'maybe_save' ] );

		self::$categories = [
			'scholar'      => __( 'Scholar/Researcher', 'ramp' ),
			'journalist'   => __( 'Journalist', 'ramp' ),
			'practitioner' => __( 'Policy or Tech Practitioner', 'ramp' ),
			'funder'       => __( 'Funder', 'ramp' ),
			'citizen'      => __( 'Interested Citizen', 'ramp' ),
		];
	}

	public static function get_categories() {
		return self::$categories;
	}

	public function enqueue_assets() {
		wp_enqueue_style(
			'disinfo-login',
			RAMP_PLUGIN_URL . '/assets/css/login.css',
			[],
			RAMP_VER
		);

		wp_enqueue_script(
			'disinfo-login',
			RAMP_PLUGIN_URL . '/assets/js/login.js',
			[ 'jquery' ],
			RAMP_VER,
			true
		);
	}

	public function register_form() {
		// @todo nonce verification
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$name = '';
		if ( isset( $_POST['name'] ) && is_string( $_POST['name'] ) ) {
			$name = wp_unslash( $_POST['name'] );
		}

		$category = '';
		if ( isset( $_POST['user_category'] ) ) {
			$category = wp_unslash( $_POST['user_category'] );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		?>

		<p>
			<label for="name"><?php esc_html_e( 'Name', 'ramp' ); ?><br />
			<input type="text" name="name" id="name" class="input" required value="<?php echo esc_attr( $name ); ?>" size="25" /></label>
		</p>

		<p>
			<label for="user_category"><?php esc_html_e( 'Which category best describes you?', 'ramp' ); ?><br />
			<select name="user_category" id="user_category" class="input user-category" required>
				<option value="" disabled <?php selected( ! $category ); ?>><?php esc_html_e( 'Please select', 'ramp' ); ?></option>

				<?php foreach ( self::get_categories() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function registration_errors( $errors, $user_login, $user_email ) {
		// @todo nonce verification
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! empty( $GLOBALS['mediawell_is_provisioned_user'] ) ) {
			return $errors;
		}

		if ( empty( $_POST['name'] ) ) {
			$errors->add( 'empty_name', __( '<strong>ERROR</strong>: You must provide a name.', 'ramp' ) );
		}

		if ( empty( $_POST['user_category'] ) ) {
			$errors->add( 'empty_category', __( '<strong>ERROR</strong>: You must specify a category.', 'ramp' ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return $errors;
	}

	public function register_new_user( $user_id ) {
		// This is handled separately for automatically-provisioned users.
		// @todo nonce verification
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! empty( $GLOBALS['mediawell_is_provisioned_user'] ) ) {
			return;
		}

		$name     = wp_unslash( $_POST['name'] );
		$category = wp_unslash( $_POST['user_category'] );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$last_space = strrpos( $name, ' ' );
		$first_name = substr( $name, 0, $last_space );
		$last_name  = substr( $name, $last_space + 1 );

		$userdata = array(
			'ID'           => $user_id,
			'display_name' => $name,
			'nickname'     => $name,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
		);
		wp_update_user( $userdata );

		update_user_meta( $user_id, 'user_category', $category );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'associated-account',
			__( 'Associated Account', 'ramp' ),
			[ $this, 'meta_box_cb' ],
			'ramp_profile',
			'side'
		);
	}

	public function meta_box_cb( $post ) {
		wp_enqueue_style( 'disinfo-select2' );
		wp_enqueue_script(
			'disinfo-scholar-profile-admin',
			RAMP_PLUGIN_URL . 'assets/js/scholar-profile-admin.js',
			[ 'disinfo-select2', 'jquery' ],
			RAMP_VER,
			true
		);

		$all_users = [
			[
				'id'   => 0,
				'text' => '',
			],
		];
		foreach ( get_users() as $user ) {
			$all_users[] = [
				'id'   => $user->ID,
				'text' => "$user->user_login ($user->display_name)",
			];
		}

		$id = isset( $post->ID ) ? $post->ID : 0;

		$selected_user_id = get_post_meta( $id, 'associated_user', true );

		$data = [
			'users'          => $all_users,
			'selectedUserId' => $selected_user_id,
		];

		wp_localize_script( 'disinfo-scholar-profile-admin', 'RAMPScholarProfileUsers', $data );

		?>
		<label for="associated-user" class="screen-reader-text"><?php esc_html_e( 'Associated user', 'ramp' ); ?></label>
		<select id="associated-user" name="associated-user"></select>
		<p class="description"><?php esc_html_e( 'Select the WordPress user account associated with this Profile.', 'ramp' ); ?></p>
		<?php wp_nonce_field( 'disinfo-associated-user', 'disinfo-associated-user-nonce' ); ?>
		<?php
	}

	public function meta_box_save_cb( $post_id ) {
		if ( empty( $_POST['disinfo-associated-user-nonce'] ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( ! $post || 'ramp_profile' !== $post->post_type ) {
			return;
		}

		check_admin_referer( 'disinfo-associated-user', 'disinfo-associated-user-nonce' );

		$associated_user = (int) wp_unslash( $_POST['associated-user'] );
		if ( ! $associated_user ) {
			delete_post_meta( $post_id, 'associated_user', $associated_user );
		} else {
			update_post_meta( $post_id, 'associated_user', $associated_user );
		}
	}

	public function maybe_save() {
		// Query checks.
		if ( ! is_main_query() ) {
			return;
		}

		if ( ! is_singular( 'ramp_profile' ) ) {
			return;
		}

		if ( empty( $_GET['edit'] ) || '1' !== $_GET['edit'] ) {
			return;
		}

		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		$sp_id  = get_queried_object_id();
		$sp_obj = ScholarProfile::get_instance( $sp_id );

		// Permission checks.
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! current_user_can( 'edit_users' ) && get_current_user_id() !== $sp_obj->get_associated_user_id() ) {
			return;
		}

		// Nonce check.
		check_admin_referer( 'edit-profile-' . $sp_id );

		$data_keys = [ 'first_name', 'last_name', 'title', 'biography', 'email_address', 'orcid_id', 'twitter_id' ];
		foreach ( $data_keys as $key ) {
			$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			$sp_obj->set( $key, $value );
		}

		$sp_obj->save();

		// Focus Tags
		if ( isset( $_POST['focus_tags'] ) ) {
			$focus_tag_ids = array_map( 'intval', $_POST['focus_tags'] );
		} else {
			$focus_tag_ids = [];
		}

		$set = wp_set_object_terms( $sp_id, $focus_tag_ids, 'ramp_focus_tag' );

		// Avatar
		if ( ! empty( $_POST['avatar_changed'] ) ) {
			$avatar_file_name = isset( $_POST['avatar_file_name'] ) ? wp_unslash( $_POST['avatar_file_name'] ) : '';

			if ( $avatar_file_name ) {
				$upload_dir = wp_upload_dir();
				$avatar_tmp = $upload_dir['basedir'] . '/ninja-forms/tmp/' . $avatar_file_name;
				$extension  = pathinfo( $avatar_file_name, PATHINFO_EXTENSION );

				$size  = getimagesize( $avatar_tmp );
				$max_w = null;
				$max_h = null;
				if ( $size[0] > 1000 ) {
					$max_w = 1000;
				} elseif ( $size[1] > 1000 ) {
					$max_h = 1000;
				}

				if ( $max_w || $max_h ) {
					$editor = wp_get_image_editor( $avatar_tmp );
					$editor->resize( $max_w, $max_h, $crop );
				}

				$avatar_dir = $upload_dir['basedir'] . '/scholar-avatars/';
				if ( ! file_exists( $avatar_dir ) ) {
					wp_mkdir_p( $avatar_dir );
				}

				$post      = get_post( $sp_id );
				$file_name = sanitize_title( $post->post_title ) . '-' . time();

				$avatar_fn   = $file_name . '.' . $extension;
				$avatar_path = $avatar_dir . $avatar_fn;
				rename( $avatar_tmp, $avatar_path );
			}

			update_post_meta( $sp_id, 'avatar_filename', $avatar_fn );
		}

		wp_safe_redirect( add_query_arg( 'updated', '1', get_permalink( get_queried_object() ) ) );
	}

	public function user_category_display( $user ) {
		$category = get_user_meta( $user->ID, 'user_category', true );

		$category_label = '-';
		if ( $category ) {
			$categories = self::get_categories();
			if ( isset( $categories[ $category ] ) ) {
				$category_label = $categories[ $category ];
			}
		}

		?>
		<table class="form-table">
			<th scope="row"><?php esc_html_e( 'Audience type', 'ramp' ); ?></th>
			<td><?php echo esc_html( $category_label ); ?></td>
		</table>


		<?php
	}

	public function registration_email( $message, $user ) {
		preg_match( '/(<http[^>]+action=rp[^>]+>)/', $message['message'], $matches );

		$rp_url = '';
		if ( $matches ) {
			$rp_url = $matches[0];
		}

		if ( ! empty( $GLOBALS['mediawell_is_provisioned_user'] ) ) {
			$new_message = sprintf(
				'Thank you for claiming your MediaWell Profile!

To complete your registration, visit the following URL, where you will be asked to set your password:

%s

After logging in for the first time, visit %s to learn more about submitting items to MediaWell.

- The team at MediaWell',
				$rp_url,
				home_url( 'get-involved' )
			);
		} else {
			$new_message = sprintf(
				'Thank you for registering to be a MediaWell Editor-at-Large!

To complete your registration, visit the following URL, where you will be asked to set your password:

%s

After logging in for the first time, visit %s to learn more about submitting items to MediaWell.

- The team at MediaWell',
				$rp_url,
				home_url( 'get-involved' )
			);
		}

		$message['message'] = $new_message;

		return $message;
	}
}
