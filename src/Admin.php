<?php

namespace SSRC\Disinfo;

use SSRC\Disinfo\LitReviews\Version;
use \WP_User;

class Admin {
	protected $pressforward;

	public function __construct( PressForward $pressforward ) {
		$this->pressforward = $pressforward;
	}

	public function init() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( 'admin_init', [ $this, 'catch_lr_version_delete' ] );
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
					'MediaWell Stats',
					[ $this, 'dashboard_widget_cb' ]
				);
			},
			5
		);

		add_action( 'save_post', [ $this, 'scholar_profile_info_save_cb' ] );
		add_action( 'save_post', [ $this, 'scholar_profile_claim_email_save_cb' ] );
		add_action( 'save_post', [ $this, 'versions_save_cb' ] );
		add_action( 'save_post', [ $this, 'version_name_save_cb' ] );
		add_action( 'save_post', [ $this, 'news_item_author_save_cb' ] );
		add_action( 'save_post', [ $this, 'formatted_citation_save_cb' ] );
		add_action( 'save_post', [ $this, 'doi_save_cb' ] );
		add_action( 'save_post', [ $this, 'publication_date_save_cb' ] );

		add_filter( 'manage_edit-ssrc_schprof_pt_columns', [ $this, 'add_schprof_featured_column' ] );
		add_action( 'manage_ssrc_schprof_pt_posts_custom_column', [ $this, 'schprof_featured_column_content' ], 10, 2 );
		add_filter( 'views_edit-ssrc_schprof_pt', [ $this, 'add_schprof_featured_view' ] );
		add_filter( 'pre_get_posts', [ $this, 'schprof_featured_query' ] );

		$this->pressforward->init();
	}

	public function add_meta_boxes() {
		add_meta_box(
			'scholar-profile-info',
			'Profile Info',
			[ $this, 'scholar_profile_info_cb' ],
			'ssrc_schprof_pt',
			'normal'
		);

		add_meta_box(
			'scholar-profile-claim-email',
			'Profile Claim Email',
			[ $this, 'scholar_profile_claim_email_cb' ],
			'ssrc_schprof_pt',
			'side'
		);

		add_meta_box(
			'zotero-id',
			'Zotero',
			[ $this, 'zotero_cb' ],
			'ssrc_citation',
			'normal'
		);

		add_meta_box(
			'zotero-collection-id',
			'Zotero',
			[ $this, 'zotero_collection_cb' ],
			'ssrc_restop_pt',
			'normal'
		);

		// Lit Reviews Version system.
		add_meta_box(
			'literature-review-versions',
			'Versions',
			[ $this, 'versions_cb' ],
			'ssrc_lit_review',
			'advanced'
		);

		add_meta_box(
			'literature-review-version-name',
			'Version Name',
			[ $this, 'version_name_cb' ],
			'ssrc_lr_version',
			'side'
		);

		// Author attribution for news items.
		add_meta_box(
			'news-item-author',
			'Public-Facing Author Attribution',
			[ $this, 'news_item_author_cb' ],
			'post',
			'normal'
		);

		// Formatted citation.
		add_meta_box(
			'formatted-citation',
			'Formatted Citation',
			[ $this, 'formatted_citation_cb' ],
			[ 'ssrc_lit_review', 'ssrc_expref_pt', 'ssrc_lr_version' ],
			'normal'
		);

		// DOI
		add_meta_box(
			'doi',
			'DOI',
			[ $this, 'doi_cb' ],
			[ 'ssrc_lit_review', 'ssrc_expref_pt' ],
			'normal'
		);

		// Publication date
		add_meta_box(
			'publication_date',
			'Publication Date',
			[ $this, 'publication_date_cb' ],
			[ 'post' ],
			'normal'
		);

		// Featured status
		add_meta_box(
			'featured_status',
			'Featured',
			[ $this, 'featured_status_date_cb' ],
			[ 'post', 'ssrc_expref_pt' ],
			'side'
		);
	}

	public function scholar_profile_info_cb( $post ) {
		include DISINFO_PLUGIN_DIR . '/templates/scholar-profile-form.php';
	}

	public function scholar_profile_claim_email_cb( $post ) {
		$claim_email = get_post_meta( $post->ID, 'claim_email', true );

		?>

		<label>
			<input type="text" name="claim-email" value="<?php echo esc_attr( $claim_email ); ?>" />
		</label>

		<p class="description">Enter the scholar's institutional email here. If a user claims this Profile using this email address, the scholar's MediaWell account will be created automatically.</p>

		<?php
		wp_nonce_field( 'claim-email', 'claim-email-nonce', false );
	}

	public function zotero_cb( $post ) {
		$zotero_url = '';
		$zotero_id  = get_post_meta( $post->ID, 'zotero_id', true );
		if ( $zotero_id ) {
			$zotero_url = 'https://www.zotero.org/groups/' . DISINFO_ZOTERO_GROUP_ID . '/items/itemKey/' . $zotero_id;
		}

		if ( $zotero_id ) {
			?>
<strong>Zotero ID:</strong> <?php echo esc_html( $zotero_id ); ?><br />
<strong>Zotero URL:</strong> <a href="<?php echo esc_attr( $zotero_url ); ?>"><?php echo esc_html( $zotero_url ); ?></a>
			<?php
		} else {
			if ( 'publish' === $post->post_status ) {
				echo 'No Zotero entry for this citation. Save this post to create the Zotero entry.';
			} else {
				echo 'A Zotero entry will be created when you publish this citation.';
			}
		}
	}

	public function zotero_collection_cb( $post ) {
		$zotero_url = '';
		$zotero_id  = get_post_meta( $post->ID, 'zotero_collection_id', true );
		if ( $zotero_id ) {
			$zotero_url = 'https://www.zotero.org/groups/' . DISINFO_ZOTERO_GROUP_ID . '/collections/' . $zotero_id;
		}

		if ( $zotero_id ) {
			?>
<strong>Zotero Collection ID:</strong> <?php echo esc_html( $zotero_id ); ?><br />
<strong>Zotero Collection URL:</strong> <a href="<?php echo esc_attr( $zotero_url ); ?>"><?php echo esc_html( $zotero_url ); ?></a>
			<?php
		} else {
			if ( 'publish' === $post->post_status ) {
				echo 'There is no Zotero collection corresponding to this Research Field. Save this post and a Zotero collection should be created automatically.';
			} else {
				echo 'A Zotero collection will be created when you publish this Research Field.';
			}
		}
	}

	public function scholar_profile_info_save_cb( $post_id ) {
		if ( ! isset( $_POST['scholar-profile-info'] ) ) {
			return;
		}

		check_admin_referer( 'scholar-profile-info-' . $post_id, 'scholar-profile-info-nonce' );

		$scholar_profile = ScholarProfile::get_instance( $post_id );
		if ( ! $scholar_profile->exists() ) {
			return;
		}

		foreach ( $scholar_profile->get_meta_keys() as $meta ) {
			if ( isset( $_POST['scholar-profile-info'][ $meta ] ) ) {
				$scholar_profile->set( $meta, $_POST['scholar-profile-info'][ $meta ] );
			}
		}

		$is_featured = ! empty( $_POST['scholar-profile-info']['is_featured'] );
		$scholar_profile->set( 'is_featured', $is_featured );

		$is_advisory = ! empty( $_POST['scholar-profile-info']['is_advisory'] );
		$scholar_profile->set( 'is_advisory', $is_advisory );

		// Prevent recursion.
		remove_action( 'save_post', [ $this, 'scholar_profile_info_save_cb' ] );
		$scholar_profile->save();
		add_action( 'save_post', [ $this, 'scholar_profile_info_save_cb' ] );
	}

	public function scholar_profile_claim_email_save_cb( $post_id ) {
		if ( ! isset( $_POST['claim-email-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'claim-email', 'claim-email-nonce' );

		$scholar_profile = ScholarProfile::get_instance( $post_id );
		if ( ! $scholar_profile->exists() ) {
			return;
		}

		$claim_email = $_POST['claim-email'] ?: '';

		update_post_meta( $post_id, 'claim_email', $claim_email );
	}

	public function versions_cb( $post ) {
		wp_enqueue_style( 'disinfo-versions-admin', DISINFO_PLUGIN_URL . '/assets/css/versions-admin.css' );

		$versions = Version::get( $post->ID );

		$delete_base = admin_url( 'post.php?action=edit&post=' . $post->ID );

		?>

		<h3>Existing versions</h3>

		<?php if ( $versions ) : ?>
			<table class="lr-versions-admin">
				<thead>
					<th scope="col">Name</th>
					<th scope="col">Date</th>
					<th scope="col">Actions</th>
				</thead>

				<tbody>
					<?php foreach ( $versions as $version ) : ?>
						<?php
						$delete_link = add_query_arg( 'delete-version', $version->ID );
						$delete_link = wp_nonce_url( $delete_link, 'delete_lr_version_' . $version->ID );
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
			<p>You have not yet created any Versions.</p>
		<?php endif; ?>

		<h3>Create new version</h3>

		<p>To create a new version, enter a name and then click 'Update' in the 'Publish' box above.</p>

		<label for="version-name">Name</label>
		<input id="version-name" name="version-name" type="text" value="" />

		<button type="button" onclick="document.getElementById('version-name').value = '<?php echo date( 'Ymd' ); ?>'">Use current date</button>

		<?php wp_nonce_field( 'version-name', 'version-name-nonce', false ); ?>

		<?php
	}

	public function catch_feature_request() {
		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( empty( $_GET['disinfo-feature'] ) ) {
			return;
		}

		$post_id = intval( $_GET['disinfo-feature'] );

		check_admin_referer( 'disinfo-feature-' . $post_id );

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

		if ( empty( $_GET['disinfo-unfeature'] ) ) {
			return;
		}

		$post_id = intval( $_GET['disinfo-unfeature'] );

		check_admin_referer( 'disinfo-unfeature-' . $post_id );

		$featured_post = Featured\FeaturedItem::get_instance( $post_id );
		$featured_post->mark_unfeatured();

		if ( ! empty( $_GET['redirect_to'] ) ) {
			$redirect_to = wp_unslash( $_GET['redirect_to'] );
			$redirect_to = add_query_arg( 'unfeature-success', 1, $redirect_to );
			wp_safe_redirect( $redirect_to );
			die;
		}
	}

	public function catch_lr_version_delete() {
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

		check_admin_referer( 'delete_lr_version_' . $version_id );

		$version = get_post( $version_id );
		$parent  = $version->post_parent;

		wp_delete_post( $version_id );

		$redirect = admin_url( 'post.php?action=edit&post_type=ssrc_lit_review&post=' . $parent );
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

		$version_id = wp_insert_post( [
			'post_type'    => 'ssrc_lr_version',
			'post_title'   => sprintf( '%s - Version %s', $source_post->post_title, $version_name ),
			'post_name'    => sanitize_title( $version_name ),
			'post_content' => $source_post->post_content,
			'post_status'  => 'publish',
			'post_parent'  => $source_post->ID,
			'post_author'  => get_current_user_id(),
		] );

		update_post_meta( $version_id, 'version_name', $version_name );

		// Copy citation from LR.
		$citation = get_post_meta( $source_post->ID, 'formatted_citation', true );
		update_post_meta( $version_id, 'formatted_citation', $citation );
	}

	public function news_item_author_cb( $post ) {
		$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $post->ID, 'item_author' );

		?>
		<label>
			Source author <input type="text" name="item-author" value="<?php echo esc_attr( $custom_author ); ?>" />
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


			<label class="screen-reader-text" for="formatted-citation-textarea">Formatted citation</label>
			<textarea name="formatted-citation" id="formatted-citation-textarea"><?php echo esc_textarea( $citation ); ?></textarea>
			<p>For use in Cite This.</p>

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

		$label = $current_post->post_type === 'ssrc_expref_pt' ? 'Article' : 'post';

		$featured_date = $featured_post->get_featured_date();
		if ( $featured_date ) {
			printf(
				'<p>This %s was initially featured on %s at %s.</p>',
				esc_html( $label ),
				$featured_date->format( 'Y-m-d' ),
				$featured_date->setTimeZone( new \DateTimeZone( 'America/New_York' ) )->format( 'h:ia' )
			);

			if ( $featured_post->is_currently_featured( $current_post->post_type ) ) {
				printf(
					'<p><strong>This is the currently featured %s.</strong></p>',
					esc_html( $label )
				);
			} else {
				$currently_featured_post = get_post( Featured\FeaturedItem::get_currently_featured_id( $current_post->post_type ) );
				printf(
					'<p>The currently featured %s is <a href="%s">%s</a>.</p>',
					esc_html( $label ),
					esc_attr( get_edit_post_link( $currently_featured_post->ID ) ),
					esc_html( $currently_featured_post->post_title )
				);
			}

			printf(
				'<p><a class="button" href="%s">Unfeature this %s</a></p>',
				$featured_post->get_unfeature_link( $_SERVER['REQUEST_URI'] ),
				esc_html( $label )
			);
		} else {
			printf(
				'<p><a class="button" href="%s">Feature this %s</a></p>',
				$featured_post->get_feature_link( $_SERVER['REQUEST_URI'] ),
				esc_html( $label )
			);

			printf(
				'<p class="description">Clicking this button will cause the current %s to be placed in the featured column on the home page.',
				esc_html( $label )
			);
		}

		/*
		$featured_posts_url = '';
		printf(
			'<p><a href="%s">%s</a></p>',
			$featured_posts_url,
			'Managed featured posts'
		);
		*/
	}

	public function dashboard_widget_cb() {
		$users_lc      = wp_cache_get_last_changed( 'users' );
		$eal_cache_key = 'subscriber_count' . $users_lc;
		$eal_count     = wp_cache_get( $eal_cache_key, 'users' );
		if ( false === $eal_count ) {
			$subscribers = get_users( [
				'fields' => 'ids',
				'role'   => 'subscriber',
			] );
			$eal_count = count( $subscribers );
			wp_cache_set( $eal_cache_key, $eal_count, 'users' );
		} else {
			$eal_count = (int) $eal_count;
		}

		$claimed_cache_key = 'claimed_count' . $users_lc;
		$claimed_count     = wp_cache_get( $claimed_cache_key, 'posts' );
		if ( false === $claimed_count ) {
			$claimed = get_posts( [
				'fields'     => 'ids',
				'post_type'  => 'ssrc_schprof_pt',
				'meta_query' => [
					[
						'key'     => 'associated_user',
						'compare' => 'EXISTS',
					]
				],
				'posts_per_page' => -1,
			] );
			$claimed_count = count( $claimed );
			wp_cache_set( $claimed_cache_key, $claimed_count, 'posts' );
		} else {
			$claimed_count = (int) $claimed_count;
		}

		$scholar_cache_key = 'scholar_count' . $users_lc;
		$scholar_count     = wp_cache_get( $scholar_cache_key, 'posts' );
		if ( false === $scholar_count ) {
			$scholars = get_posts( [
				'fields'         => 'ids',
				'post_type'      => 'ssrc_schprof_pt',
				'posts_per_page' => -1,
			] );
			$scholar_count = count( $scholars );
			wp_cache_set( $scholar_cache_key, $scholar_count, 'posts' );
		} else {
			$scholar_count = (int) $scholar_count;
		}

		$nom_cache_key = 'nom_count' . $users_lc;
		$nom_count     = wp_cache_get( $nom_cache_key, 'posts' );
		if ( false === $nom_count ) {
			$nominations = get_posts( [
				'fields'         => 'ids',
				'post_type'      => 'nomination',
				'post_status'    => 'any',
				'posts_per_page' => -1,
			] );
			$nom_count = count( $nominations );
			wp_cache_set( $nom_cache_key, $nom_count, 'posts' );
		} else {
			$nom_count = (int) $nom_count;
		}

		$enom_cache_key = 'enom_count' . $users_lc;
		$enom_count     = wp_cache_get( $enom_cache_key, 'posts' );
		if ( false === $enom_count ) {
			$authors = get_users( [
				'fields'       => 'ids',
				'role__not_in' => [ 'subscriber' ],
			] );

			$enominations = get_posts( [
				'fields'         => 'ids',
				'post_type'      => 'nomination',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'author__not_in' => array_map( 'intval', $authors ),
			] );
			$enom_count = count( $enominations );
			wp_cache_set( $enom_cache_key, $enom_count, 'posts' );
		} else {
			$enom_count = (int) $enom_count;
		}
		?>

		<table class="widefat">
			<tr>
				<th scope="row">Editor-at-Large count</th>
				<td><?php echo esc_html( $eal_count ); ?></td>
			</tr>

			<tr>
				<th scope="row">Claimed Profiles</th>
				<td><?php echo esc_html( $claimed_count ); ?> / <?php echo esc_html( $scholar_count ); ?> (<?php echo esc_html( floor( ( $claimed_count / $scholar_count ) * 100 ) ); ?>%)</td>
			</tr>

			<tr>
				<th scope="row">Nomination count</th>
				<td><?php echo esc_html( $nom_count ); ?> (<?php echo esc_html( $enom_count ); ?> external)</td>
			</tr>
		</table>
		<?php

		$users_by_category_cache_key = 'users_by_category' . $users_lc;
		$counts = wp_cache_get( $users_by_category_cache_key, 'users' );
		if ( false === $counts ) {
			global $wpdb;

			$raw = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'user_category'" );
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
		return $columns;
	}

	public function schprof_featured_column_content( $column, $post_id ) {
		if ( 'featured' !== $column ) {
			return;
		}

		$scholar_profile = ScholarProfile::get_instance( $post_id );

		if ( $scholar_profile->get_is_featured() ) {
			echo 'Yes';
		}
	}

	public function add_schprof_featured_view( $views ) {
		$views['featured'] = sprintf(
			'<a href="%s">Featured <span class="count">(%s)</span></a>',
			esc_attr( admin_url( 'edit.php?post_type=ssrc_schprof_pt&is_featured=1' ) ),
			count( ScholarProfile::get_featured_ids() )
		);
		return $views;
	}

	public function schprof_featured_query( $query ) {
		global $pagenow;

		if ( ! is_admin() ) {
			return;
		}

		if ( 'edit.php' !== $pagenow || empty( $_GET['post_type'] ) || 'ssrc_schprof_pt' !== $_GET['post_type'] ) {
			return;
		}

		if ( empty( $_GET['is_featured'] ) || '1' !== $_GET['is_featured'] ) {
			return;
		}

		$query->set( 'meta_key', 'is_featured' );
		$query->set( 'meta_value', '1' );
	}
}
