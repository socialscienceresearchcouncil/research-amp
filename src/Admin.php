<?php

namespace SSRC\RAMP;

use WP_User;
use PAnD;

use SSRC\RAMP\Citation;
use SSRC\RAMP\Zotero\Library as ZoteroLibrary;

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class Admin {
	/**
	 * PressForward instance.
	 *
	 * @since 1.0.0
	 *
	 * @var PressForward
	 */
	protected $pressforward;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param PressForward $pressforward PressForward instance.
	 *
	 * @return void
	 */
	public function __construct( PressForward $pressforward ) {
		$this->pressforward = $pressforward;
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( 'save_post', [ $this, 'news_item_author_save_cb' ] );
		add_action( 'save_post', [ $this, 'formatted_citation_save_cb' ] );
		add_action( 'save_post', [ $this, 'doi_save_cb' ] );
		add_action( 'save_post', [ $this, 'publication_date_save_cb' ] );

		add_filter( 'manage_edit-ramp_profile_columns', [ $this, 'add_schprof_featured_column' ] );
		add_action( 'manage_ramp_profile_posts_custom_column', [ $this, 'schprof_featured_column_content' ], 10, 2 );

		add_action( 'admin_notices', [ $this, 'add_companion_plugin_admin_notice' ] );

		// Necessary to load scripts for persistent dismissible admin notices.
		add_action( 'admin_init', array( 'PAnD', 'init' ) );

		$this->pressforward->init();

		Zotero\Admin::init();
	}

	/**
	 * Add meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'zotero-id',
			__( 'Zotero', 'research-amp' ),
			[ $this, 'zotero_cb' ],
			'ramp_citation',
			'normal'
		);

		add_meta_box(
			'zotero-collection-id',
			__( 'Zotero', 'research-amp' ),
			[ $this, 'zotero_collection_cb' ],
			'ramp_topic',
			'normal'
		);

		// Author attribution for news items.
		if ( function_exists( 'pressforward' ) ) {
			add_meta_box(
				'news-item-author',
				__( 'Public-Facing Author Attribution', 'research-amp' ),
				[ $this, 'news_item_author_cb' ],
				'ramp_news_item',
				'normal'
			);
		}

		// Formatted citation.
		add_meta_box(
			'formatted-citation',
			__( 'Formatted Citation', 'research-amp' ),
			[ $this, 'formatted_citation_cb' ],
			[ 'ramp_review', 'ramp_article' ],
			'normal'
		);

		// DOI
		add_meta_box(
			'doi',
			__( 'DOI', 'research-amp' ),
			[ $this, 'doi_cb' ],
			[ 'ramp_review', 'ramp_article' ],
			'normal'
		);

		// Publication date
		add_meta_box(
			'publication_date',
			__( 'Publication Date', 'research-amp' ),
			[ $this, 'publication_date_cb' ],
			[ 'ramp_news_item' ],
			'normal'
		);
	}

	/**
	 * Zotero meta box callback.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function zotero_cb( $post ) {
		$citation = Citation::get_from_post_id( $post->ID );

		$zotero_url = $citation->get_zotero_url();
		$zotero_id  = $citation->get_zotero_id();

		if ( $zotero_id ) {
			?>
<strong><?php esc_html_e( 'Zotero ID:', 'research-amp' ); ?></strong> <?php echo esc_html( $zotero_id ); ?><br />
<strong><?php esc_html_e( 'Zotero URL:', 'research-amp' ); ?></strong> <a href="<?php echo esc_attr( $zotero_url ); ?>"><?php echo esc_html( $zotero_url ); ?></a>
			<?php
		} elseif ( 'publish' === $post->post_status ) {
			esc_html_e( 'No Zotero entry for this citation. Save this post to create the Zotero entry.', 'research-amp' );
		} else {
			esc_html_e( 'A Zotero entry will be created when you publish this citation.', 'research-amp' );
		}
	}

	/**
	 * Zotero collection meta box callback.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function zotero_collection_cb( $post ) {
		$zotero_id  = '';
		$zotero_url = '';

		// There's got to be a better way to do this.
		$collection_id = get_post_meta( $post->ID, 'zotero_collection_id', true );
		if ( $collection_id ) {
			$libraries = ZoteroLibrary::get_libraries();
			foreach ( $libraries as $library ) {
				$lib_collections = $library->get_collection_list();
				if ( isset( $lib_collections[ $collection_id ] ) ) {
					$zotero_url = $lib_collections[ $collection_id ]['url'];
					break;
				}
			}

			if ( $zotero_url ) {
				$zotero_id = $collection_id;
			}
		}

		if ( $zotero_id ) {
			?>
<strong><?php esc_html_e( 'Zotero Collection ID:', 'research-amp' ); ?></strong> <?php echo esc_html( $zotero_id ); ?><br />
<strong><?php esc_html_e( 'Zotero Collection URL:', 'research-amp' ); ?></strong> <a href="<?php echo esc_attr( $zotero_url ); ?>"><?php echo esc_html( $zotero_url ); ?></a>
			<?php
		} elseif ( 'publish' === $post->post_status ) {
			esc_html_e( 'There is no Zotero collection corresponding to this Research Topic. Save this post and a Zotero collection should be created automatically.', 'research-amp' );
		} else {
			esc_html_e( 'A Zotero collection will be created when you publish this Research Topic.', 'research-amp' );
		}
	}

	/**
	 * News item author meta box callback.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function news_item_author_cb( $post ) {
		$custom_author = pressforward( 'controller.metas' )->retrieve_meta( $post->ID, 'item_author' );

		?>
		<label>
			<?php esc_html_e( 'Source author', 'research-amp' ); ?> <input type="text" name="item-author" value="<?php echo esc_attr( $custom_author ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'news-item-author', 'news-item-author-nonce', false );
	}

	/**
	 * Save news item author.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function news_item_author_save_cb( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'ramp_news_item' !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['news-item-author-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'news-item-author', 'news-item-author-nonce' );

		$item_author = wp_unslash( $_POST['item-author'] );

		update_post_meta( $post_id, 'item_author', $item_author );
	}

	/**
	 * Formatted citation meta box callback.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
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


			<label class="screen-reader-text" for="formatted-citation-textarea"><?php esc_html_e( 'Formatted citation', 'research-amp' ); ?></label>
			<textarea name="formatted-citation" id="formatted-citation-textarea"><?php echo esc_textarea( $citation ); ?></textarea>
			<p><?php esc_html_e( 'For use in Cite This.', 'research-amp' ); ?></p>

		<?php
		wp_nonce_field( 'formatted-citation', 'formatted-citation-nonce', false );
	}

	/**
	 * Save formatted citation.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function formatted_citation_save_cb( $post_id ) {
		if ( ! isset( $_POST['formatted-citation-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'formatted-citation', 'formatted-citation-nonce' );

		$citation = wp_unslash( $_POST['formatted-citation'] );

		update_post_meta( $post_id, 'formatted_citation', $citation );
	}

	/**
	 * DOI meta box callback.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function doi_cb( $post ) {
		$doi = get_post_meta( $post->ID, 'doi', true );

		?>

		<label>
			<input type="text" name="doi" value="<?php echo esc_attr( $doi ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'doi', 'doi-nonce', false );
	}

	/**
	 * Save DOI.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function doi_save_cb( $post_id ) {
		if ( ! isset( $_POST['doi-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'doi', 'doi-nonce' );

		$doi = wp_unslash( $_POST['doi'] );

		update_post_meta( $post_id, 'doi', $doi );
	}

	/**
	 * Publication date meta box callback.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function publication_date_cb( $post ) {
		$publication_date = get_post_meta( $post->ID, 'publication_date', true );

		?>

		<label>
			<input type="date" name="publication-date" value="<?php echo esc_attr( $publication_date ); ?>" />
		</label>

		<?php
		wp_nonce_field( 'publication-date', 'publication-date-nonce', false );
	}

	/**
	 * Save publication date.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function publication_date_save_cb( $post_id ) {
		if ( ! isset( $_POST['publication-date-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'publication-date', 'publication-date-nonce' );

		$publication_date = wp_unslash( $_POST['publication-date'] );

		update_post_meta( $post_id, 'publication_date', $publication_date );
	}

	/**
	 * Add featured status meta box.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
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
				esc_html__( 'This %1$s was initially featured on %2$s at %4$s.', 'research-amp' ),
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
					esc_html__( 'This is the currently featured %s.', 'research-amp' ),
					esc_html( $label )
				);
				echo '</strong></p>';
			} else {
				$currently_featured_post = get_post( Featured\FeaturedItem::get_currently_featured_id( $current_post->post_type ) );
				echo '<p>';
				printf(
					/* translators: 1. Post type name; 2. Link to currently featured post */
					esc_html__( 'The currently featured %1$s is %2$s.', 'research-amp' ),
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
					esc_html__( 'Unfeature this %s', 'research-amp' ),
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
					esc_html__( 'Feature this %s', 'research-amp' ),
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

	/**
	 * Add the 'Featured' column to the Profiles list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function add_schprof_featured_column( $columns ) {
		$last = array_pop( $columns );
		return array_merge( $columns, [ 'featured' => 'Featured?' ], [ $last ] );
	}

	/**
	 * Add content to the 'Featured' column in the Profiles list table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function schprof_featured_column_content( $column, $post_id ) {
		if ( 'featured' !== $column ) {
			return;
		}

		$profile_profile = Profile::get_instance( $post_id );

		if ( $profile_profile->get_is_featured() ) {
			echo 'Yes';
		}
	}

	/**
	 * Add admin notice about companion plugins.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_companion_plugin_admin_notice() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// Only display on the main Dashboard page or on the Plugins page.
		if ( ! ( is_admin() && ( 'plugins.php' === $GLOBALS['pagenow'] || 'index.php' === $GLOBALS['pagenow'] ) ) ) {
			return;
		}

		if ( ! PAnD::is_admin_notice_active( 'ramp-plugin-notice' ) ) {
			return;
		}

		$plugins = [
			[
				'name'        => 'DK PDF',
				'plugin_dir'  => 'dk-pdf',
				'plugin_file' => 'dk-pdf/dk-pdf.php',
				'url'         => 'https://wordpress.org/plugins/dk-pdf/',
			],
			[
				'name'        => 'Easy Table of Contents',
				'plugin_dir'  => 'easy-table-of-contents',
				'plugin_file' => 'easy-table-of-contents/easy-table-of-contents.php',
				'url'         => 'https://wordpress.org/plugins/easy-table-of-contents/',
			],
			[
				'name'        => 'PressForward',
				'plugin_dir'  => 'pressforward',
				'plugin_file' => 'pressforward/pressforward.php',
				'url'         => 'https://wordpress.org/plugins/pressforward/',
			],
			[
				'name'        => 'The Events Calendar',
				'plugin_dir'  => 'the-events-calendar',
				'plugin_file' => 'the-events-calendar/the-events-calendar.php',
				'url'         => 'https://wordpress.org/plugins/the-events-calendar/',
			],
		];

		// Plugins that are not installed.
		$missing_plugins = array_filter(
			$plugins,
			function ( $plugin ) {
				return is_wp_error( validate_plugin( $plugin['plugin_file'] ) );
			}
		);

		// Plugins that are installed but not active.
		$inactive_plugins = array_filter(
			$plugins,
			function ( $plugin ) {
				return 0 === validate_plugin( $plugin['plugin_file'] ) && ! is_plugin_active( $plugin['plugin_file'] );
			}
		);

		if ( ! $missing_plugins && ! $inactive_plugins ) {
			return;
		}

		$missing_plugins_lis = array_map(
			function ( $plugin ) {
				return sprintf(
					'<li><a href="%s">%s</a></li>',
					esc_attr( $plugin['url'] ),
					esc_html( $plugin['name'] )
				);
			},
			$missing_plugins
		);

		$inactive_plugins_lis = array_map(
			function ( $plugin ) {
				return sprintf(
					'<li><a href="%s">%s</a></li>',
					esc_attr( $plugin['url'] ),
					esc_html( $plugin['name'] )
				);
			},
			$inactive_plugins
		);

		$notice_text  = '<div class="ramp-plugin-notice__header">';
		$notice_text .= '<img class="ramp-plugin-notice__header-logo" src="' . esc_url( RAMP_PLUGIN_URL . 'assets/img/research-amp-logo.png' ) . '" alt="' . esc_attr__( 'Research AMP logo', 'research-amp' ) . '" />';
		$notice_text .= '</div>';

		$notice_text .= '<p><strong>' . esc_html__( 'Research AMP works best with a number of companion plugins.', 'research-amp' ) . '</strong></p>';

		if ( $missing_plugins_lis ) {
			$notice_text .= '<div class="ramp-plugin-notice__list">';
			$notice_text .= '<p>' . esc_html__( 'The following plugins are not installed:', 'research-amp' ) . '</p>';
			$notice_text .= '<ul>' . implode( '', $missing_plugins_lis ) . '</ul>';
			$notice_text .= '</div>';
		}

		if ( $inactive_plugins_lis ) {
			$notice_text .= '<div class="ramp-plugin-notice__list">';
			$notice_text .= '<p>' . esc_html__( 'The following plugins are installed but not active:', 'research-amp' ) . '</p>';
			$notice_text .= '<ul>' . implode( '', $inactive_plugins_lis ) . '</ul>';
			$notice_text .= '</div>';
		}

		// translators: Link to Plugins panel.
		$notice_text .= '<p><strong>' . sprintf( esc_html__( 'Visit %s to install and activate these plugins.', 'research-amp' ), '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . esc_html__( 'Plugins', 'research-amp' ) . '</a>' ) . '</strong></p>';

		wp_enqueue_style( 'research-amp-admin', RAMP_PLUGIN_URL . 'assets/css/admin-notices.css', [], RAMP_VER );

		printf(
			'<div data-dismissible="ramp-plugin-notice" class="notice is-dismissible ramp-plugin-notice"><p>%s</p></div>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$notice_text
		);
	}
}
