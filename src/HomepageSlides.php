<?php

/**
 * Homepage slides functionality.
 */

namespace SSRC\RAMP;

class HomepageSlides {
	protected $post_type = 'ramp_homepage_slide';

	public function __construct() {}

	public static function get_instance() {
		static $instance;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	public function init() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'register_assets' ] );

		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );

		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'slide_info_save_cb' ] );
	}

	public function register_post_type() {
		register_post_type(
			$this->post_type,
			[
				'labels'              => [
					'name'          => 'Homepage Slides',
					'singular_name' => 'Homepage Slide',
					'menu_name'     => 'Homepage Slides',
				],
				'public'              => true,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
				'exclude_from_search' => true,
			]
		);
	}

	public function register_assets() {
		// RAMP-specific themes are loaded in the compiled frontend block styles.
		wp_register_style( 'ramp-glide', RAMP_PLUGIN_URL . '/node_modules/@glidejs/glide/dist/css/glide.core.css', [], RAMP_VER );
		wp_register_style( 'ramp-glide-theme', RAMP_PLUGIN_URL . '/node_modules/@glidejs/glide/dist/css/glide.theme.css', [ 'ramp-glide' ], RAMP_VER );

		wp_register_script( 'ramp-glide', RAMP_PLUGIN_URL . '/node_modules/@glidejs/glide/dist/glide.js', [], RAMP_VER, true );
		wp_register_script( 'ramp-homepage-slides', RAMP_PLUGIN_URL . '/assets/js/homepage-slides.js', [ 'ramp-glide' ], RAMP_VER, true );
	}

	public function enqueue_block_assets() {
		wp_enqueue_style( 'ramp-glide-theme' );
	}

	public function register_meta_boxes( $post ) {
		add_meta_box(
			'ramp_slide_info',
			__( 'Slide Info', 'ramp' ),
			[ $this, 'slide_info_cb' ],
			$this->post_type,
			'normal',
			'default'
		);

	}

	public function slide_info_cb( $post ) {
		$meta_text   = get_post_meta( $post->ID, 'ramp_slide_meta_text', true );
		$button_text = get_post_meta( $post->ID, 'ramp_slide_button_text', true );
		$button_url  = get_post_meta( $post->ID, 'ramp_slide_button_url', true );
		?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="col">
					<label for="slide-url">Meta Text</label>
				</th>

				<td>
					<input type="text" name="slide-meta-text" id="slide-meta-text" value="<?php echo esc_attr( $meta_text ); ?>" />
					<p class="description">Text that appears above the title.</p>
				</td>
			</tr>

			<tr>
				<th scope="col">
					<label for="slide-url">Button Text</label>
				</th>

				<td>
					<input type="text" name="slide-button-text" id="slide-button-text" value="<?php echo esc_attr( $button_text ); ?>" />
				</td>
			</tr>

			<tr>
				<th scope="col">
					<label for="slide-url">Button URL</label>
				</th>

				<td>
					<input type="url" name="slide-button-url" id="slide-button-url" value="<?php echo esc_attr( $button_url ); ?>" />
				</td>
			</tr>
		</table>
		<?php

		wp_nonce_field( 'ramp_slide_info', 'ramp-slide-info-nonce', false );
	}

	public function slide_info_save_cb( $post_id ) {
		if ( ! isset( $_POST['ramp-slide-info-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'ramp_slide_info', 'ramp-slide-info-nonce' );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$meta_text   = wp_unslash( $_POST['slide-meta-text'] );
		$button_text = wp_unslash( $_POST['slide-button-text'] );
		$button_url  = wp_unslash( $_POST['slide-button-url'] );

		update_post_meta( $post_id, 'ramp_slide_meta_text', $meta_text );
		update_post_meta( $post_id, 'ramp_slide_button_text', $button_text );
		update_post_meta( $post_id, 'ramp_slide_button_url', $button_url );
	}
}
