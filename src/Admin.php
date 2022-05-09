<?php

namespace SSRC\RAMP;

use \WP_User;

class Admin {
	protected $pressforward;

	public function __construct( PressForward $pressforward ) {
		$this->pressforward = $pressforward;
	}

	public function init() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( 'admin_init', [ $this, 'catch_feature_request' ] );
		add_action( 'admin_init', [ $this, 'catch_unfeature_request' ] );

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
			[ 'ramp_review', 'ramp_article' ],
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
