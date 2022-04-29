<?php

namespace SSRC\RAMP;

class UserManagement {
	// @todo Does this remain?
	protected static $categories;

	public function init() {
		// Admin
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'meta_box_save_cb' ] );
	}

	public static function get_categories() {
		return self::$categories;
	}

	public function enqueue_assets() {
		wp_enqueue_style(
			'ramp-login',
			RAMP_PLUGIN_URL . '/assets/css/login.css',
			[],
			RAMP_VER
		);

		wp_enqueue_script(
			'ramp-login',
			RAMP_PLUGIN_URL . '/assets/js/login.js',
			[ 'jquery' ],
			RAMP_VER,
			true
		);
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
		wp_enqueue_style( 'ramp-select2' );
		wp_enqueue_script(
			'ramp-profile-admin',
			RAMP_PLUGIN_URL . 'assets/js/profile-admin.js',
			[ 'ramp-select2', 'jquery' ],
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

		wp_localize_script( 'ramp-profile-admin', 'RAMPProfileUsers', $data );

		?>
		<label for="associated-user" class="screen-reader-text"><?php esc_html_e( 'Associated user', 'ramp' ); ?></label>
		<select id="associated-user" name="associated-user"></select>
		<p class="description"><?php esc_html_e( 'Select the WordPress user account associated with this Profile.', 'ramp' ); ?></p>
		<?php wp_nonce_field( 'ramp-associated-user', 'ramp-associated-user-nonce' ); ?>
		<?php
	}

	public function meta_box_save_cb( $post_id ) {
		if ( empty( $_POST['ramp-associated-user-nonce'] ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( ! $post || 'ramp_profile' !== $post->post_type ) {
			return;
		}

		check_admin_referer( 'ramp-associated-user', 'ramp-associated-user-nonce' );

		$associated_user = (int) wp_unslash( $_POST['associated-user'] );
		if ( ! $associated_user ) {
			delete_post_meta( $post_id, 'associated_user', $associated_user );
		} else {
			update_post_meta( $post_id, 'associated_user', $associated_user );
		}
	}
}
