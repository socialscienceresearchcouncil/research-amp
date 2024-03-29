<?php

namespace SSRC\RAMP;

class Profile {
	protected $data = [
		'post_id'       => null,
		'display_name'  => '',
		'biography'     => '',
		'first_name'    => '',
		'last_name'     => '',
		'email_address' => '',
		'orcid_id'      => '',
		'twitter_id'    => '',
		'title'         => '',
		'homepage_url'  => '',
		'is_featured'   => false,
	];

	protected $meta_keys = [
		'email_address',
		'first_name',
		'homepage_url',
		'last_name',
		'orcid_id',
		'twitter_id',
		'title',
	];

	public static function get_instance( $profile_id ) {
		$instance = new self();
		$instance->fill( $profile_id );
		return $instance;
	}

	public function exists() {
		return ! is_null( $this->data['post_id'] );
	}

	protected function fill( $profile_id ) {
		$post = get_post( $profile_id );

		if ( ! $post || 'ramp_profile' !== $post->post_type ) {
			return;
		}

		$this->data['post_id']      = $post->ID;
		$this->data['display_name'] = $post->post_title;
		$this->data['biography']    = $post->post_content;

		foreach ( $this->meta_keys as $meta ) {
			$this->data[ $meta ] = get_post_meta( $post->ID, $meta, true );
		}

		$is_featured               = get_post_meta( $post->ID, 'is_featured', true );
		$this->data['is_featured'] = '1' === $is_featured;
	}

	public function save() {
		foreach ( $this->meta_keys as $meta ) {
			update_post_meta( $this->data['post_id'], $meta, $this->data[ $meta ] );
		}

		update_post_meta( $this->data['post_id'], 'is_featured', (int) $this->get_is_featured() );

		$post_vars = [
			'ID'           => $this->get_post_id(),
			'post_content' => $this->get_biography(),
		];

		$fn = $this->get_first_name();
		$ln = $this->get_last_name();
		if ( $fn && $ln ) {
			$post_vars['post_title'] = sprintf( '%s %s', $fn, $ln );
		}

		wp_update_post( $post_vars );
	}

	public function set( $key, $value ) {
		if ( array_key_exists( $key, $this->data ) ) {
			$this->data[ $key ] = $value;
		}
	}

	public function get_post_id() {
		return (int) $this->data['post_id'];
	}

	public function get_meta_keys() {
		return $this->meta_keys;
	}

	public function get_first_name() {
		return trim( $this->data['first_name'] );
	}

	public function get_last_name() {
		return trim( $this->data['last_name'] );
	}

	public function get_display_name() {
		return $this->data['display_name'];
	}

	public function get_title() {
		return wp_strip_all_tags( get_post_meta( $this->get_post_id(), 'ramp_profile_title_institution', true ) );
	}

	public function get_biography() {
		return $this->data['biography'];
	}

	public function get_homepage_url() {
		return $this->data['homepage_url'];
	}

	public function get_orcid_id() {
		return $this->data['orcid_id'];
	}

	public function get_orcid_link() {
		return 'https://orcid.org/' . $this->data['orcid_id'];
	}

	public function get_twitter_id() {
		return $this->data['twitter_id'];
	}

	public function get_email_address() {
		return $this->data['email_address'];
	}

	public function get_is_featured() {
		return (bool) $this->data['is_featured'];
	}

	public function get_focus_tags() {
		return wp_get_object_terms( $this->get_post_id(), 'ramp_focus_tag' );
	}

	public function get_focus_tag_links() {
		$base = get_post_type_archive_link( 'ramp_profile' );

		$links = array_map(
			function ( $tag ) use ( $base ) {
				return sprintf(
					'<a href="%s">%s</a>',
					esc_attr( add_query_arg( 'research-interest', $tag->slug, $base ) ),
					esc_html( $tag->name )
				);
			},
			$this->get_focus_tags()
		);

		return implode( ', ', $links );
	}

	/**
	 * Gets a list of profile types associated with this profile.
	 *
	 * @return array
	 */
	public function get_profile_types() {
		$profile_types = wp_get_object_terms( $this->get_post_id(), 'ramp_profile_type' );
		return wp_list_pluck( $profile_types, 'name' );
	}

	public function get_sp_term_id() {
		$sp_map = ramp_app()->get_cpttax_map( 'profile' );
		return $sp_map->get_term_id_for_post_id( $this->get_post_id() );
	}

	public function get_articles( $args = [] ) {
		$query_args = array_merge(
			[
				'post_type'      => 'ramp_article',
				'posts_per_page' => -1,
				'tax_query'      => [
					[
						'taxonomy' => 'ramp_assoc_profile',
						'terms'    => $this->get_sp_term_id(),
						'field'    => 'term_id',
					],
				],
			],
			$args
		);

		return get_posts( $query_args );
	}

	public function get_citations() {
		$citation_library = new CitationLibrary();

		return $citation_library->get_recently_added_items_local(
			[
				'tax_query'      => [
					[
						'taxonomy' => 'ramp_assoc_profile',
						'terms'    => $this->get_sp_term_id(),
						'field'    => 'term_id',
					],
				],
				'posts_per_page' => -1,
			]
		);
	}

	public function get_avatar_path() {
		$avatar_path = '';
		return $avatar_path;
	}

	public function get_avatar_url() {
		$img_src      = '';
		$thumbnail_id = get_post_thumbnail_id( $this->get_post_id() );
		if ( $thumbnail_id ) {
			$img_details = wp_get_attachment_image_src( $thumbnail_id, 'profile-image' );
			$img_src     = $img_details[0];
		}

		return $img_src;
	}

	public function get_associated_user_id() {
		$id = get_post_meta( $this->get_post_id(), 'associated_user', true );
		return (int) $id;
	}

	/**
	 * Fetches an array of the IDs of featured profiles.
	 *
	 * @return array
	 */
	public static function get_featured_ids() {
		$posts = get_posts(
			[
				'post_type'      => 'ramp_profile',
				'post_status'    => 'publish',
				'meta_key'       => 'is_featured',
				'meta_value'     => '1',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			]
		);

		return array_map( 'intval', $posts );
	}

	/**
	 * Gets the profiles corresponding to a post.
	 *
	 * @param int $post_id
	 * @return array
	 */
	public static function get_profiles_for_post( $post_id ) {
		$profiles     = [];
		$author_terms = wp_get_object_terms( $post_id, 'ramp_assoc_profile', [ 'orderby' => 'term_order' ] );
		if ( $author_terms ) {
			foreach ( $author_terms as $author_term ) {
				$profile_map = ramp_app()->get_cpttax_map( 'profile' );
				$profile_id  = $profile_map->get_post_id_for_term_id( $author_term->term_id );
				if ( $profile_id ) {
					$profiles[] = self::get_instance( $profile_id );
				}
			}
		}

		return $profiles;
	}

	/**
	 * Gets a list of links to profiles associated with a post.
	 *
	 * @param int $post_id
	 * @return array
	 */
	public static function get_profile_links_for_post( $post_id ) {
		return array_map(
			function ( $profile ) {
				return sprintf(
					'<a href="%s">%s</a>',
					esc_url( get_permalink( $profile->get_post_id() ) ),
					esc_html( $profile->get_display_name() )
				);
			},
			self::get_profiles_for_post( $post_id )
		);
	}

	/**
	 * Gets a list of taxonomy terms associated with profiles.
	 */
	public static function get_terms_belonging_to_profiles() {
		return Schema::get_terms_belonging_to_post_type( 'ramp_assoc_topic', 'ramp_profile' );
	}
}
