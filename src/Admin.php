<?php

namespace SSRC\RAMP;

use SSRC\RAMP\LitReviews\Version;
use \WP_User;

class Admin {
	protected $pressforward;

	public function __construct( PressForward $pressforward ) {
		$this->pressforward = $pressforward;
	}

	public function init() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( 'admin_init', [ $this, 'catch_review_version_delete' ] );
		add_action( 'admin_init', [ $this, 'catch_feature_request' ] );
		add_action( 'admin_init', [ $this, 'catch_unfeature_request' ] );

		add_action(
			'wp_dashboard_setup',
			function() {
				if ( ! current_user_can( 'edit_others_posts' ) ) {
					return;
				}

				wp_add_dashboard_widget(
					'mediawell-stats',
					__( 'RAMP Stats', 'ramp' ),
					[ $this, 'dashboard_widget_cb' ]
				);
			},
			5
		);

		add_action( 'save_post', [ $this, 'profile_info_save_cb' ] );
		add_action( 'save_post', [ $this, 'profile_claim_email_save_cb' ] );
		add_action( 'save_post', [ $this, 'versions_save_cb' ] );
		add_action( 'save_post', [ $this, 'version_name_save_cb' ] );
		add_action( 'save_post', [ $this, 'news_item_author_save_cb' ] );
		add_action( 'save_post', [ $this, 'formatted_citation_save_cb' ] );
		add_action( 'save_post', [ $this, 'doi_save_cb' ] );
		add_action( 'save_post', [ $this, 'publication_date_save_cb' ] );

		add_filter( 'manage_edit-ramp_profile_columns', [ $this, 'add_schprof_featured_column' ] );
		add_action( 'manage_ramp_profile_posts_custom_column', [ $this, 'schprof_featured_column_content' ], 10, 2 );
		add_filter( 'views_edit-ramp_profile', [ $this, 'add_schprof_featured_view' ] );
		add_filter( 'pre_get_posts', [ $this, 'schprof_featured_query' ] );

		$this->pressforward->init();

		Zotero\Admin::init();
	}

	public function add_meta_boxes() {
		add_meta_box(
			'profile-info',
			__( 'Profile Info', 'ramp' ),
			[ $this, 'profile_info_cb' ],
			'ramp_profile',
			'normal'
		);

		add_meta_box(
			'profile-claim-email',
			__( 'Profile Claim Email', 'ramp' ),
			[ $this, 'profile_claim_email_cb' ],
			'ramp_profile',
			'side'
		);

		add_meta_box(
			'zotero-id',
			__( 'Zotero', 'ramp' ),
			[ $this, 'zotero_cb' ],
			'ramp_citation',
			'normal'
		);

		add_meta_box(
			'zotero-collection-id',
			__( 'Zotero', 'ramp' ),
			[ $this, 'zotero_collection_cb' ],
			'ramp_topic',
			'normal'
		);

		// Lit Reviews Version system.
		add_meta_box(
			'research-review-versions',
			__( 'Versions', 'ramp' ),
			[ $this, 'versions_cb' ],
			'ramp_review',
			'advanced'
		);

		add_meta_box(
			'research-review-version-name',
			__( 'Version Name', 'ramp' ),
			[ $this, 'version_name_cb' ],
			'ramp_review_version',
			'side'
		);

		// Author attribution for news items.
		add_meta_box(
			'news-item-author',
			__( 'Public-Facing Author Attribution', 'ramp' ),
			[ $this, 'news_item_author_cb' ],
			'post',
			'normal'
		);

		// Formatted citation.
		add_meta_box(
			'formatted-citation',
			__( 'Formatted Citation', 'ramp' ),
			[ $this, 'formatted_citation_cb' ],
			[ 'ramp_review', 'ramp_article', 'ramp_review_version' ],
			'normal'
		);

		// DOI
		add_meta_box(
			'doi',
			__( 'DOI', 'ramp' ),
			[ $this, 'doi_cb' ],
			[ 'ramp_review', 'ramp_article' ],
			'normal'
		);

		// Publication date
		add_meta_box(
			'publication_date',
			__( 'Publication Date', 'ramp' ),
			[ $this, 'publication_date_cb' ],
			[ 'post' ],
			'normal'
		);

		// Featured status
		add_meta_box(
			'featured_status',
			__( 'Featured', 'ramp' ),
			[ $this, 'featured_status_date_cb' ],
			[ 'post', 'ramp_article' ],
			'side'
		);
	}

	public function profile_info_cb( $post ) {
		include RAMP_PLUGIN_DIR . '/templates/profile-form.php';
	}

	public function profile_claim_email_cb( $post ) {
		$claim_email = get_post_meta( $post->ID, 'claim_email', true );

		?>

		<label>
			<input type="text" name="claim-email" value="<?php echo esc_attr( $claim_email ); ?>" />
		</label>

		<p class="description"><?php esc_html_e( "Enter the user's institutional email here. If a user claims this Profile using this email address, the user's local account will be created automatically.", 'ramp' ); ?></p>

		<?php
		wp_nonce_field( 'claim-email', 'claim-email-nonce', false );
	}

	public function zotero_cb( $post ) {
		$zotero_url = '';
		$zotero_id  = get_post_meta( $post->ID, 'zotero_id', true );
		if ( $zotero_id ) {
			$zotero_url = 'https://www.zotero.org/groups/' . RAMP_ZOTERO_GROUP_ID . '/items/itemKey/' . $zotero_id;
		}

		if ( $zotero_id ) {
			?>
<strong><?php esc_html_e( 'Zotero ID:', 'ramp' ); ?></strong> <?php echo esc_html( $zotero_id ); ?><br />
<strong><?php esc_html_e( 'Zotero URL:', 'ramp' ); ?></strong> <a href="<?php echo esc_attr( $zotero_url ); ?>"><?php echo esc_html( $zotero_url ); ?></a>
			<?php
		} else {
			if ( 'publish' === $post->post_status ) {
				esc_html_e( 'No Zotero entry for this citation. Save this post to create the Zotero entry.', 'ramp' );
			} else {
				esc_html_e( 'A Zotero entry will be created when you publish this citation.', 'ramp' );
			}
		}
	}

	public function zotero_collection_cb( $post ) {
		$zotero_url = '';
		$zotero_id  = get_post_meta( $post->ID, 'zotero_collection_id', true );
		if ( $zotero_id ) {
			$zotero_url = 'https://www.zotero.org/groups/' . RAMP_ZOTERO_GROUP_ID . '/collections/' . $zotero_id;
		}

		if ( $zotero_id ) {
			?>
<strong><?php esc_html_e( 'Zotero Collection ID:', 'ramp' ); ?></strong> <?php echo esc_html( $zotero_id ); ?><br />
<strong><?php esc_html_e( 'Zotero Collection URL:', 'ramp' ); ?></strong> <a href="<?php echo esc_attr( $zotero_url ); ?>"><?php echo esc_html( $zotero_url ); ?></a>
			<?php
		} else {
			if ( 'publish' === $post->post_status ) {
				esc_html_e( 'There is no Zotero collection corresponding to this Research Topic. Save this post and a Zotero collection should be created automatically.', 'ramp' );
			} else {
				esc_html_e( 'A Zotero collection will be created when you publish this Research Topic.', 'ramp' );
			}
		}
	}

	public function profile_info_save_cb( $post_id ) {
		if ( ! isset( $_POST['profile-info'] ) ) {
			return;
		}

		check_admin_referer( 'profile-info-' . $post_id, 'profile-info-nonce' );

		$profile = Profile::get_instance( $post_id );
		if ( ! $profile->exists() ) {
			return;
		}

		foreach ( $profile->get_meta_keys() as $meta ) {
			if ( isset( $_POST['profile-info'][ $meta ] ) ) {
				$profile->set( $meta, $_POST['profile-info'][ $meta ] );
			}
		}

		$is_featured = ! empty( $_POST['profile-info']['is_featured'] );
		$profile->set( 'is_featured', $is_featured );

		$is_advisory = ! empty( $_POST['profile-info']['is_advisory'] );
		$profile->set( 'is_advisory', $is_advisory );

		// Prevent recursion.
		remove_action( 'save_post', [ $this, 'profile_info_save_cb' ] );
		$profile->save();
		add_action( 'save_post', [ $this, 'profile_info_save_cb' ] );
	}

	public function profile_claim_email_save_cb( $post_id ) {
		if ( ! isset( $_POST['claim-email-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'claim-email', 'claim-email-nonce' );

		$profile = Profile::get_instance( $post_id );
		if ( ! $profile->exists() ) {
			return;
		}

		$claim_email = $_POST['claim-email'] ? wp_unslash( $_POST['claim-email'] ) : '';

		update_post_meta( $post_id, 'claim_email', $claim_email );
	}

	public function versions_cb( $post ) {
		wp_enqueue_style(
			'ramp-versions-admin',
			RAMP_PLUGIN_URL . '/assets/css/versions-admin.css',
			[],
			RAMP_VER
		);

		$versions = Version::get( $post->ID );

		$delete_base = admin_url( 'post.php?action=edit&post=' . $post->ID );

		?>

		<h3><?php esc_html_e( 'Existing versions', 'ramp' ); ?></h3>

		<?php if ( $versions ) : ?>
			<table class="lr-versions-admin">
				<thead>
					<th scope="col"><?php esc_html_e( 'Name', 'ramp' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Date', 'ramp' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Actions', 'ramp' ); ?></th>
				</thead>

				<tbody>
					<?php foreach ( $versions as $version ) : ?>
						<?php
						$delete_link = add_query_arg( 'delete-version', $version->ID );
						$delete_link = wp_nonce_url( $delete_link, 'delete_review_version_' . $version->ID );
						?>
						<tr>
							<td><?php printf( '<a href="%s">%s</a>', esc_attr( get_permalink( $version ) ), esc_html( get_post_meta( $version->ID, 'version_name', true ) ) ); ?></td>
							<td><?php echo esc_html( $version->post_date ); ?></td>
							<td><?php printf( '<a href="%s">Delete</a>', esc_attr( $delete_link ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		<?php else : ?>
			<p><?php esc_html_e( 'You have not yet created any Versions.', 'ramp' ); ?>', 'ramp' ); ?></p>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Create new version', 'ramp' ); ?></h3>

		<p><?php esc_html_e( "To create a new version, enter a name and then click 'Update' in the 'Publish' box above.", 'ramp' ); ?></p>

		<label for="version-name"><?php esc_html_e( 'Name', 'ramp' ); ?></label>
		<input id="version-name" name="version-name" type="text" value="" />

		<button type="button" onclick="document.getElementById('version-name').value = '<?php echo esc_js( gmdate( 'Ymd' ) ); ?>'"><?php esc_html_e( 'Use current date', 'ramp' ); ?></button>

		<?php wp_nonce_field( 'version-name', 'version-name-nonce', false ); ?>

		<?php
	}

	public function catch_feature_request() {
		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( empty( $_GET['ramp-feature'] ) ) {
			return;
		}

		$post_id = intval( $_GET['ramp-feature'] );

		check_admin_referer( 'ramp-feature-' . $post_id );

		$featured_post = Featured\FeaturedItem::get_instance( $post_id );
		$featured_post->mark_featured();

		if ( ! empty( $_GET['redirect_to'] ) ) {
			$redirect_to = wp_unslash( $_GET['redirect_to'] );
			$redirect_to = add_query_arg( 'feature-success', 1, $redirect_to );
			wp_safe_redirect( $redirect_to );
			die;
		}
	}

	public function catch_unfeature_request() {
		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( empty( $_GET['ramp-unfeature'] ) ) {
			return;
		}

		$post_id = intval( $_GET['ramp-unfeature'] );

		check_admin_referer( 'ramp-unfeature-' . $post_id );

		$featured_post = Featured\FeaturedItem::get_instance( $post_id );
		$featured_post->mark_unfeatured();

		if ( ! empty( $_GET['redirect_to'] ) ) {
			$redirect_to = wp_unslash( $_GET['redirect_to'] );
			$redirect_to = add_query_arg( 'unfeature-success', 1, $redirect_to );
			wp_safe_redirect( $redirect_to );
			die;
		}
	}

	public function catch_review_version_delete() {
		global $pagenow;

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( 'post.php' !== $pagenow ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) || 'edit' !== $_GET['action'] ) {
			return;
		}

		if ( empty( $_GET['delete-version'] ) ) {
			return;
		}

		$version_id = intval( $_GET['delete-version'] );

		check_admin_referer( 'delete_review_version_' . $version_id );

		$version = get_post( $version_id );
		$parent  = $version->post_parent;

		wp_delete_post( $version_id );

		$redirect = admin_url( 'post.php?action=edit&post_type=ramp_review&post=' . $parent );
		wp_safe_redirect( $redirect );
		die;
	}

	public function versions_save_cb( $post_id ) {
		if ( empty( $_POST['version-name'] ) ) {
			return;
		}

		check_admin_referer( 'version-name', 'version-name-nonce' );

		$version_name = wp_unslash( $_POST['version-name'] );

		// Prevent recursion.
		unset( $_POST['version-name'] );

		$source_post = get_post( $post_id );

		$version_id = wp_insert_post(
			[
				'post_type'    => 'ramp_review_version',
				/* translators: 1. Research review version title; 2. Version name */
				'post_title'   => sprintf( __( '%1$s - Version %2$s', 'ramp' ), $source_post->post_title, $version_name ),
				'post_name'    => sanitize_title( $version_name ),
				'post_content' => $source_post->post_content,
				'post_status'  => 'publish',
				'post_parent'  => $source_post->ID,
				'post_author'  => get_current_user_id(),
			]
		);

		update_post_meta( $version_id, 'version_name', $version_name );

		// Copy citation from LR.
		$citation = get_post_meta( $source_post->ID, 'formatted_citation', true );
		update_post_meta( $version_id, 'formatted_citation', $citation );
	}

	public function news_item_author_cb( $post ) {
		$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $post->ID, 'item_author' );

		?>
		<label>
			<?php esc_html_e( 'Source author', 'ramp' ); ?> <input type="text" name="item-author" value="<?php echo esc_attr( $custom_author ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'news-item-author', 'news-item-author-nonce', false );
	}

	public function news_item_author_save_cb( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'post' !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['news-item-author-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'news-item-author', 'news-item-author-nonce' );

		$item_author = wp_unslash( $_POST['item-author'] );

		update_post_meta( $post_id, 'item_author', $item_author );
	}

	public function formatted_citation_cb( $post ) {
		$citation = get_post_meta( $post->ID, 'formatted_citation', true );

		?>
		<style>
		#formatted-citation-textarea {
			display: block;
			height: 4em;
			width: 100%;
		}
		</style>


			<label class="screen-reader-text" for="formatted-citation-textarea"><?php esc_html_e( 'Formatted citation', 'ramp' ); ?></label>
			<textarea name="formatted-citation" id="formatted-citation-textarea"><?php echo esc_textarea( $citation ); ?></textarea>
			<p><?php esc_html_e( 'For use in Cite This.', 'ramp' ); ?></p>

		<?php
		wp_nonce_field( 'formatted-citation', 'formatted-citation-nonce', false );
	}

	public function formatted_citation_save_cb( $post_id ) {
		if ( ! isset( $_POST['formatted-citation-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'formatted-citation', 'formatted-citation-nonce' );

		$citation = wp_unslash( $_POST['formatted-citation'] );

		update_post_meta( $post_id, 'formatted_citation', $citation );
	}

	public function doi_cb( $post ) {
		$doi = get_post_meta( $post->ID, 'doi', true );

		?>

		<label>
			<input type="text" name="doi" value="<?php echo esc_attr( $doi ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'doi', 'doi-nonce', false );
	}

	public function doi_save_cb( $post_id ) {
		if ( ! isset( $_POST['doi-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'doi', 'doi-nonce' );

		$doi = wp_unslash( $_POST['doi'] );

		update_post_meta( $post_id, 'doi', $doi );
	}

	public function publication_date_cb( $post ) {
		$publication_date = get_post_meta( $post->ID, 'publication_date', true );

		?>

		<label>
			<input type="date" name="publication-date" value="<?php echo esc_attr( $publication_date ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'publication-date', 'publication-date-nonce', false );
	}

	public function publication_date_save_cb( $post_id ) {
		if ( ! isset( $_POST['publication-date-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'publication-date', 'publication-date-nonce' );

		$publication_date = wp_unslash( $_POST['publication-date'] );

		update_post_meta( $post_id, 'publication_date', $publication_date );
	}

	public function version_name_cb( $post ) {
		$version_name = get_post_meta( $post->ID, 'version_name', true );

		?>

		<label>
			<input name="version-name-edit" value="<?php echo esc_attr( $version_name ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'version-name-edit', 'version-name-edit-nonce', false );
	}

	public function version_name_save_cb( $post_id ) {
		if ( ! isset( $_POST['version-name-edit-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'version-name-edit', 'version-name-edit-nonce' );

		$version_name = wp_unslash( $_POST['version-name-edit'] );

		update_post_meta( $post_id, 'version_name', $version_name );
	}

	public function featured_status_date_cb( $current_post ) {
		// Don't show on non-published posts, which can't be featured.
		if ( 'publish' !== $current_post->post_status ) {
			return;
		}

		$featured_post = Featured\FeaturedItem::get_instance( $current_post->ID );

		// @todo This does not translate
		$label = 'ramp_article' === $current_post->post_type ? 'Article' : 'post';

		$featured_date = $featured_post->get_featured_date();
		if ( $featured_date ) {
			echo '<p>';
			printf(
				/* translators: 1. Post type name; 2. Date on which item was featured; 3. time at which item was featured */
				esc_html__( 'This %1$s was initially featured on %2$s at %4$s.', 'ramp' ),
				esc_html( $label ),
				esc_html( $featured_date->format( 'Y-m-d' ) ),
				// @todo This should not be hardcoded to NYC.
				esc_html( $featured_date->setTimeZone( new \DateTimeZone( 'America/New_York' ) )->format( 'h:ia' ) )
			);
			echo '</p>';

			if ( $featured_post->is_currently_featured( $current_post->post_type ) ) {
				echo '<p><strong>';
				printf(
					/* translators: Post type name */
					esc_html__( 'This is the currently featured %s.', 'ramp' ),
					esc_html( $label )
				);
				echo '</strong></p>';
			} else {
				$currently_featured_post = get_post( Featured\FeaturedItem::get_currently_featured_id( $current_post->post_type ) );
				echo '<p>';
				printf(
					/* translators: 1. Post type name; 2. Link to currently featured post */
					esc_html__( 'The currently featured %1$s is %2$s.', 'ramp' ),
					esc_html( $label ),
					sprintf(
						'<a href="%s">%s</a>',
						esc_attr( get_edit_post_link( $currently_featured_post->ID ) ),
						esc_html( $currently_featured_post->post_title )
					)
				);
				echo '</p>';
			}

			echo '<p>';
			printf(
				'<a class="button" href="%s">%s</a>',
				esc_attr( $featured_post->get_unfeature_link( $_SERVER['REQUEST_URI'] ) ),
				sprintf(
					/* translators: Unfeatured item type */
					esc_html__( 'Unfeature this %s', 'ramp' ),
					esc_html( $label )
				)
			);
			echo '</p>';
		} else {
			echo '<p>';
			printf(
				'<a class="button" href="%s">%s</a>',
				esc_attr( $featured_post->get_feature_link( $_SERVER['REQUEST_URI'] ) ),
				sprintf(
					/* translators: Featured item type */
					esc_html__( 'Feature this %s', 'ramp' ),
					esc_html( $label )
				)
			);
			echo '</p>';

			echo '<p class="description">';
			printf(
				'Clicking this button will cause the current %s to be placed in the featured column on the home page.',
				esc_html( $label )
			);
			echo '</p>';
		}
	}

	public function dashboard_widget_cb() {
		$users_lc      = wp_cache_get_last_changed( 'users' );
		$eal_cache_key = 'subscriber_count' . $users_lc;
		$eal_count     = wp_cache_get( $eal_cache_key, 'users' );
		if ( false === $eal_count ) {
			$subscribers = get_users(
				[
					'fields' => 'ids',
					'role'   => 'subscriber',
				]
			);
			$eal_count   = count( $subscribers );
			wp_cache_set( $eal_cache_key, $eal_count, 'users' );
		} else {
			$eal_count = (int) $eal_count;
		}

		$claimed_cache_key = 'claimed_count' . $users_lc;
		$claimed_count     = wp_cache_get( $claimed_cache_key, 'posts' );
		if ( false === $claimed_count ) {
			$claimed       = get_posts(
				[
					'fields'         => 'ids',
					'post_type'      => 'ramp_profile',
					'meta_query'     => [
						[
							'key'     => 'associated_user',
							'compare' => 'EXISTS',
						],
					],
					'posts_per_page' => -1,
				]
			);
			$claimed_count = count( $claimed );
			wp_cache_set( $claimed_cache_key, $claimed_count, 'posts' );
		} else {
			$claimed_count = (int) $claimed_count;
		}

		$profile_cache_key = 'profile_count' . $users_lc;
		$profile_count     = wp_cache_get( $profile_cache_key, 'posts' );
		if ( false === $profile_count ) {
			$profiles      = get_posts(
				[
					'fields'         => 'ids',
					'post_type'      => 'ramp_profile',
					'posts_per_page' => -1,
				]
			);
			$profile_count = count( $profiles );
			wp_cache_set( $profile_cache_key, $profile_count, 'posts' );
		} else {
			$profile_count = (int) $profile_count;
		}

		$nom_cache_key = 'nom_count' . $users_lc;
		$nom_count     = wp_cache_get( $nom_cache_key, 'posts' );
		if ( false === $nom_count ) {
			$nominations = get_posts(
				[
					'fields'         => 'ids',
					'post_type'      => 'nomination',
					'post_status'    => 'any',
					'posts_per_page' => -1,
				]
			);
			$nom_count   = count( $nominations );
			wp_cache_set( $nom_cache_key, $nom_count, 'posts' );
		} else {
			$nom_count = (int) $nom_count;
		}

		$enom_cache_key = 'enom_count' . $users_lc;
		$enom_count     = wp_cache_get( $enom_cache_key, 'posts' );
		if ( false === $enom_count ) {
			$authors = get_users(
				[
					'fields'       => 'ids',
					'role__not_in' => [ 'subscriber' ],
				]
			);

			$enominations = get_posts(
				[
					'fields'         => 'ids',
					'post_type'      => 'nomination',
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'author__not_in' => array_map( 'intval', $authors ),
				]
			);
			$enom_count   = count( $enominations );
			wp_cache_set( $enom_cache_key, $enom_count, 'posts' );
		} else {
			$enom_count = (int) $enom_count;
		}
		?>

		<table class="widefat">
			<tr>
				<th scope="row"><?php esc_html_e( 'Editor-at-Large count', 'ramp' ); ?></th>
				<td><?php echo esc_html( $eal_count ); ?></td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Claimed Profiles', 'ramp' ); ?></th>
				<td><?php echo esc_html( $claimed_count ); ?> / <?php echo esc_html( $profile_count ); ?> (<?php echo esc_html( floor( ( $claimed_count / $profile_count ) * 100 ) ); ?>%)</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Nomination count', 'ramp' ); ?></th>
				<?php /* translators: Count for external nominations */ ?>
				<td><?php echo esc_html( $nom_count ); ?> (<?php sprintf( esc_html__( '%s external', 'ramp' ), esc_html( $enom_count ) ); ?></td>
			</tr>
		</table>
		<?php

		$users_by_category_cache_key = 'users_by_category' . $users_lc;
		$counts                      = wp_cache_get( $users_by_category_cache_key, 'users' );
		if ( false === $counts ) {
			global $wpdb;

			$raw    = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'user_category'" );
			$counts = [];
			foreach ( $raw as $type ) {
				if ( ! isset( $counts[ $type ] ) ) {
					$counts[ $type ] = 0;
				}
				$counts[ $type ]++;

			}

			wp_cache_set( $users_by_category_cache_key, $counts, 'users' );
		}

		?>

		<br />
		<h3><strong>Audience types</strong></h3>
		<table class="widefat">
			<?php foreach ( UserManagement::get_categories() as $value => $label ) : ?>
				<tr>
					<th span="col"><?php echo esc_html( $label ); ?></th>
					<th span="col"><?php echo ( isset( $counts[ $value ] ) ? esc_html( number_format_i18n( $counts[ $value ] ) ) : 0 ); ?></th>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
	}

	public function add_schprof_featured_column( $columns ) {
		$last = array_pop( $columns );
		return array_merge( $columns, [ 'featured' => 'Featured?' ], [ $last ] );
	}

	public function schprof_featured_column_content( $column, $post_id ) {
		if ( 'featured' !== $column ) {
			return;
		}

		$profile_profile = Profile::get_instance( $post_id );

		if ( $profile_profile->get_is_featured() ) {
			echo 'Yes';
		}
	}

	public function add_schprof_featured_view( $views ) {
		$views['featured'] = sprintf(
			'<a href="%s">%s <span class="count">(%s)</span></a>',
			esc_html__( 'Featured', 'ramp' ),
			esc_attr( admin_url( 'edit.php?post_type=ramp_profile&is_featured=1' ) ),
			count( Profile::get_featured_ids() )
		);
		return $views;
	}

	public function schprof_featured_query( $query ) {
		global $pagenow;

		if ( ! is_admin() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'edit.php' !== $pagenow || empty( $_GET['post_type'] ) || 'ramp_profile' !== $_GET['post_type'] ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['is_featured'] ) || '1' !== $_GET['is_featured'] ) {
			return;
		}

		$query->set( 'meta_key', 'is_featured' );
		$query->set( 'meta_value', '1' );
	}
}
