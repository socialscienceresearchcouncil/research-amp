<?php

namespace SSRC\RAMP;

class PressForward {
	public function init() {
		// Bail if PF is not available.
		if ( ! defined( 'PF_SLUG' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 20 );

		add_action( 'wp_insert_post', [ $this, 'save_publication_date' ] );
		add_action( 'transition_pf_post_meta', [ $this, 'save_publication_date_on_transition' ], 10, 2 );

		add_action( 'init', [ $this, 'adjust_taxonomies' ], 30 );

		add_action( 'admin_menu', [ $this, 'grant_nomthis_access' ] );

		add_action( 'add_meta_boxes_nomthis', [ $this, 'metaboxes' ], 100 );

		add_filter(
			'pf_valid_post_taxonomies',
			function() {
				return [ 'ramp_assoc_topic', 'ssrc_focus_tag' ];
			}
		);

		add_action(
			'media_buttons',
			function() {
				remove_action( 'media_buttons', 'nominate_this_media_buttons' );
			},
			0
		);

		add_filter(
			'pre_update_option_pf_last_nominated_feed',
			'__return_empty_string'
		);
	}

	/**
	 * Force PF to look for nomthis requests early in the load process.
	 *
	 * Otherwise WP bounces us due to insufficient caps.
	 */
	public function grant_nomthis_access() {
		start_pf_nom_this();
	}

	public function metaboxes() {
		remove_meta_box( 'pf-nomthis-submit', 'nomthis', 'side' );
		remove_meta_box( 'pf-categorydiv', 'nomthis', 'side' );
		remove_meta_box( 'pf-tagsdiv', 'nomthis', 'side' );

		add_meta_box(
			'disinfo-nomthis-submit',
			__( 'Send to MediaWell', 'ramp' ),
			array( $this, 'submit_meta_box' ),
			'nomthis',
			'side',
			'high'
		);

		$rt_post_type = get_post_type( 'ramp_topic' );
		add_meta_box(
			'disinfo-nomthis-rts',
			__( 'Research Fields', 'ramp' ),
			array( $this, 'rts_meta_box' ),
			'nomthis',
			'side'
		);

		add_meta_box(
			'disinfo-nomthis-focus-tags',
			__( 'Tags', 'ramp' ),
			array( $this, 'focus_tags_meta_box' ),
			'nomthis',
			'side'
		);

		add_meta_box(
			'disinfo-nomthis-date',
			__( 'Publication Date', 'ramp' ),
			array( $this, 'date_meta_box' ),
			'nomthis',
			'side'
		);
	}

	public function adjust_taxonomies() {
		$nomination_post_type = pressforward( 'schema.nominations' )->post_type;
		register_taxonomy_for_object_type( 'ramp_assoc_topic', pressforward( 'schema.nominations' )->post_type );
		unregister_taxonomy_for_object_type( 'post_tag', pressforward( 'schema.nominations' )->post_type );
		unregister_taxonomy_for_object_type( 'category', pressforward( 'schema.nominations' )->post_type );
	}

	public function register_assets() {
		wp_register_script(
			'disinfo-pressforward',
			RAMP_PLUGIN_URL . '/assets/js/pressforward.js',
			array( 'jquery', PF_SLUG . '-twitter-bootstrap' ),
			RAMP_VER,
			true
		);

		wp_localize_script(
			'disinfo-pressforward',
			'RAMPPressForward',
			[
				'eventsIsActive' => defined( 'TRIBE_EVENTS_FILE' ),
				'restBase'       => esc_url_raw( rest_url( 'disinfo/v1' ) ),
				'restNonce'      => wp_create_nonce( 'wp_rest' ),
			]
		);
	}

	public function enqueue_scripts( $hook ) {
		if ( 'pressforward_page_pf-review' === $hook ) {
			wp_enqueue_script( 'disinfo-pressforward' );
		}
	}

	public function add_taxonomies_to_nomthis() {
		if ( ! function_exists( 'post_categories_meta_box' ) ) {
			require_once ABSPATH . '/wp-admin/includes/meta-boxes.php';
		}

		// all taxonomies
		foreach ( get_object_taxonomies( 'nomination' ) as $tax_name ) {
			$taxonomy = get_taxonomy( $tax_name );
			if ( ! $taxonomy->show_ui || false === $taxonomy->meta_box_cb ) {
				continue;
			}

			$label = $taxonomy->labels->name;

			if ( ! is_taxonomy_hierarchical( $tax_name ) ) {
				$tax_meta_box_id = 'tagsdiv-' . $tax_name;
			} else {
				$tax_meta_box_id = $tax_name . 'div';
			}

			add_meta_box( $tax_meta_box_id, $label, $taxonomy->meta_box_cb, 'nomthis', 'side', 'low', array( 'taxonomy' => $tax_name ) );
		}
	}

	/**
	 * Generates markup for the Submit meta box on the Nominate This interface.
	 */
	public function submit_meta_box() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$url              = isset( $_GET['u'] ) ? esc_url( $_GET['u'] ) : '';
		$author_retrieved = pressforward( 'controller.metas' )->get_author_from_url( $url );

		?>

		<p id="publishing-actions">
		<?php
			$publish_type = get_option( PF_SLUG . '_draft_post_status', 'draft' );

			$pf_draft_post_type_value = get_option( PF_SLUG . '_draft_post_type', 'post' );

		if ( 'draft' === $publish_type ) {
			$cap = 'edit_posts';
		} else {
			$cap = 'publish_posts';
		}
			submit_button( __( 'Nominate', 'ramp' ), 'button button-primary', 'draft', false, array( 'id' => 'save' ) );

		if ( current_user_can( 'edit_others_posts' ) ) {
			submit_button( __( 'Send to Draft', 'ramp' ), 'secondary', 'publish', false );
		}
		?>
				<span class="spinner" style="display: none;"></span>
			</p>
			<p>
				<?php
				if ( ! $author_retrieved ) {
					$author_value = '';
				} else {
					$author_value = $author_retrieved;
				}
				?>
			<label for="item_author"><input type="text" id="item_author" name="item_author" value="<?php echo esc_attr( $author_value ); ?>" /><br />&nbsp;<?php echo esc_html( apply_filters( 'pf_author_nominate_this_prompt', __( 'Enter Authors', 'ramp' ) ) ); ?></label>
			</p>
			<?php
			do_action( 'nominate_this_sidebar_head' );
			?>

		<?php
	}

	public function rts_meta_box( $post, $box ) {
		wp_enqueue_style( 'disinfo-research-topics-metabox', RAMP_PLUGIN_URL . '/assets/css/research-topics-metabox.css', [], RAMP_VER );

		$tax_name = 'ramp_assoc_topic';
		$taxonomy = get_taxonomy( $tax_name );

		?>
		<div id="taxonomy-<?php echo esc_attr( $tax_name ); ?>" class="categorydiv">
			<div id="<?php echo esc_attr( $tax_name ); ?>-all" class="tabs-panel">
				<?php
				$name = ( 'category' === $tax_name ) ? 'post_category' : 'tax_input[' . esc_attr( $tax_name ) . ']';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo esc_attr( $tax_name ); ?>checklist" data-wp-lists="list:<?php echo esc_attr( $tax_name ); ?>" class="categorychecklist form-no-clear">
					<?php
					wp_terms_checklist(
						$post->ID,
						array(
							'taxonomy' => $tax_name,
						)
					);
					?>
				</ul>
			</div>
		</div>

		<style type="text/css">
		#site-heading {
			margin-top: 0;
		}

		#site-heading a {
			background-image: url('<?php echo esc_html( get_stylesheet_directory_uri() ); ?>/assets/MediaWell-logo.svg');
			background-repeat: no-repeat;
			display: inline-block;
			height: 50px;
			width: 200px;
		}

		#site-heading #site-title {
			display: none;
		}

		/*
		#site-heading:after {
			content: 'wtf';
			display: block;
		}
		*/
		</style>

		<?php
	}

	public function focus_tags_meta_box( $post ) {
		wp_enqueue_script( 'disinfo-focus-tags', RAMP_PLUGIN_URL . '/assets/js/focus-tags.js', [ 'jquery', 'disinfo-select2' ], RAMP_VER, true );
		wp_enqueue_style( 'disinfo-select2' );
		wp_enqueue_style( 'disinfo-focus-tags-metabox', RAMP_PLUGIN_URL . '/assets/css/focus-tags-metabox.css', [], RAMP_VER );

		$tax_name              = 'ssrc_focus_tag';
		$taxonomy              = get_taxonomy( $tax_name );
		$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
		if ( ! is_string( $terms_to_edit ) ) {
			$terms_to_edit = '';
		}
		$tags = get_terms(
			[
				'taxonomy'   => $tax_name,
				'hide_empty' => false,
			]
		);
		?>

		<div class="tagsdiv" id="<?php echo esc_attr( $tax_name ); ?>">
			<select name="tax_input[ssrc_focus_tag]" multiple id="focus-tags-select">
				<?php foreach ( $tags as $tag ) : ?>
					<option value="<?php echo esc_attr( $tag->term_id ); ?>"><?php echo esc_html( $tag->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Begin typing to select from a list of tags.', 'ramp' ); ?></p>
		</div>

		<?php
	}

	public function date_meta_box( $post ) {
		?>
<input type="date" name="publication-date" id="publication-date" />
<p class="description"><?php esc_html_e( 'Enter the original publication date of the article', 'ramp' ); ?></p>
		<?php
		wp_nonce_field( 'disinfo-publication-date', 'disinfo_publication_date_nonce', false );
	}

	public function save_publication_date( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'nomination' !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['disinfo_publication_date_nonce'] ) ) {
			return;
		}

		check_admin_referer( 'disinfo-publication-date', 'disinfo_publication_date_nonce' );

		if ( empty( $_POST['publication-date'] ) ) {
			return;
		}

		$publication_date = wp_unslash( $_POST['publication-date'] );

		update_post_meta( $post_id, 'publication_date', $publication_date );
	}

	public function save_publication_date_on_transition( $old_post, $new_post ) {
		$publication_date = get_post_meta( $old_post, 'publication_date', true );

		if ( ! $publication_date ) {
			return;
		}

		update_post_meta( $new_post, 'publication_date', $publication_date );
	}
}
