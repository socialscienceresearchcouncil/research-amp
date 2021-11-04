<?php

namespace SSRC\RAMP;

class Schema {
	protected $cpttaxonomies = [];

	public function init() {
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ], 20 );
		add_action( 'init', [ $this, 'link_cpts_and_taxonomies' ], 30 );
		add_action( 'init', [ $this, 'set_up_post_type_features' ], 40 );
		add_action( 'init', [ $this, 'register_scripts' ] );

		// Sync Citation RTs to associated SPs.
		add_action( 'set_object_terms', [ $this, 'sync_citation_rts_to_sps' ], 10, 4 );

		// Filter post_type_link for LR versions.
		add_filter( 'post_type_link', [ $this, 'filter_lr_version_link' ], 10, 2 );

		// Include postmeta fields in Relevanssi.
		add_filter( 'relevanssi_index_custom_fields', [ $this, 'add_fields_to_index' ], 10, 2 );

		// Loosen restrictions on LR Version slug uniqueness.
		add_filter( 'pre_wp_unique_post_slug', [ $this, 'check_lr_version_post_slug' ], 10, 6 );
	}

	public function register_scripts() {
		// @todo Must be included in plugin.
		wp_register_script( 'disinfo-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js', [], RAMP_VER, true );
		wp_register_style( 'disinfo-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css', [], RAMP_VER );
	}

	public function register_post_types() {
		add_post_type_support( 'page', 'excerpt' );

		// Field Review (formerly Lit Review)
		register_post_type(
			'ssrc_lit_review',
			[
				'label'             => 'Field Reviews',
				'labels'            => [
					'name'               => 'Field Reviews',
					'singular_name'      => 'Field Review',
					'add_new_item'       => 'Add New Field Review',
					'edit_item'          => 'Edit Field Review',
					'new_item'           => 'New Field Review',
					'view_item'          => 'View Field Review',
					'view_items'         => 'View Field Reviews',
					'search_items'       => 'Search Field Reviews',
					'not_found'          => 'No Field Reviews found',
					'not_found_in_trash' => 'No Field Reviews found in Trash',
					'all_items'          => 'All Field Reviews',
					'name_admin_bar'     => 'Field Reviews',
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'literature-reviews',
					'with_front' => false,
				],
				'menu_icon'         => 'dashicons-book',
				'show_in_rest'      => false, // Disables Gutenberg.
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
			]
		);

		// Field Review Versions.
		register_post_type(
			'ssrc_lr_version',
			[
				'label'             => 'Field Review Versions',
				'labels'            => [
					'name'               => 'Field Review Versions',
					'singular_name'      => 'Field Review Version',
					'add_new_item'       => 'Add New Field Review Version',
					'edit_item'          => 'Edit Field Review Version',
					'new_item'           => 'New Field Review Version',
					'view_item'          => 'View Field Review Version',
					'view_items'         => 'View Field Review Version',
					'search_items'       => 'Search Field Review Versions',
					'not_found'          => 'No Field Review Versions found',
					'not_found_in_trash' => 'No Field Review Versions found in Trash',
					'all_items'          => 'All Field Review Versions',
					'name_admin_bar'     => 'Field Review Versions',
				],
				'public'            => true,
				'show_ui'           => true,
				'rewrite'           => [
					'slug'       => 'literature-reviews/%lrslug%/versions',
					'with_front' => false,
				],
				'has_archive'       => false,
				'menu_icon'         => 'dashicons-book',
				'show_in_rest'      => false, // Disables Gutenberg.
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
			]
		);

		add_rewrite_rule(
			'^literature-reviews/([^/]+)/versions/([^/]+)/?',
			'index.php?post_type=ssrc_lr_version&lr_slug=$matches[1]&name=$matches[2]',
			'top'
		);

		add_filter(
			'query_vars',
			function( $vars ) {
				$vars[] = 'lr_slug';
				return $vars;
			}
		);

		// Articles
		register_post_type(
			'ssrc_expref_pt',
			[
				'label'             => 'Articles',
				'labels'            => [
					'name'               => 'Articles',
					'singular_name'      => 'Article',
					'add_new_item'       => 'Add New Article',
					'edit_item'          => 'Edit Article',
					'new_item'           => 'New Article',
					'view_item'          => 'View Article',
					'view_items'         => 'View Articles',
					'search_items'       => 'Search Articles',
					'not_found'          => 'No Articles found',
					'not_found_in_trash' => 'No Articles found in Trash',
					'all_items'          => 'All Articles',
					'name_admin_bar'     => 'Articles',
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'articles',
					'with_front' => false,
				],
				'menu_icon'         => 'dashicons-lightbulb',
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'author', 'thumbnail' ],
			]
		);

		add_image_size( 'expert-reflection-image', 565, 202, true );

		// Research Topics.
		register_post_type(
			'ssrc_restop_pt',
			[
				'label'             => __( 'Research Topics', 'ramp' ),
				'labels'            => [
					'name'               => __( 'Research Topics', 'ramp' ),
					'singular_name'      => __( 'Research Topic', 'ramp' ),
					'add_new_item'       => __( 'Add New Research Topic', 'ramp' ),
					'edit_item'          => __( 'Edit Research Topic', 'ramp' ),
					'new_item'           => __( 'New Research Topic', 'ramp' ),
					'view_item'          => __( 'View Research Topic', 'ramp' ),
					'view_items'         => __( 'View Research Topics', 'ramp' ),
					'search_items'       => __( 'Search Research Topics', 'ramp' ),
					'not_found'          => __( 'No Research Topics found', 'ramp' ),
					'not_found_in_trash' => __( 'No Research Topics found in Trash', 'ramp' ),
					'all_items'          => __( 'All Research Topics', 'ramp' ),
					'name_admin_bar'     => __( 'Research Topics', 'ramp' ),
				],
				'public'            => true,
				'has_archive'       => 'research-fields',
				'rewrite'           => [
					'slug'       => 'research-fields',
					'with_front' => false,
				],
				'menu_icon'         => 'dashicons-format-status',
				'show_in_rest'      => false, // Disables Gutenberg.
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'page-attributes', 'thumbnail' ],
			]
		);

		add_image_size( 'research-topic-image', 564, 180, true );

		// Profiles.
		register_post_type(
			'ssrc_schprof_pt',
			[
				'label'        => 'Profiles',
				'labels'       => [
					'name'               => 'Profiles',
					'singular_name'      => 'Profile',
					'add_new_item'       => 'Add New Profile',
					'edit_item'          => 'Edit Profile',
					'new_item'           => 'New Profile',
					'view_item'          => 'View Profile',
					'view_items'         => 'View Profiles',
					'search_items'       => 'Search Profiles',
					'not_found'          => 'No Profiles found',
					'not_found_in_trash' => 'No Profiles found in Trash',
					'all_items'          => 'All Profiles',
					'name_admin_bar'     => 'Profiles',
				],
				'public'       => true,
				'show_ui'      => true,
				'has_archive'  => true,
				'rewrite'      => [
					'slug'       => 'our-network',
					'with_front' => false,
				],
				'menu_icon'    => 'dashicons-welcome-learn-more',
				'show_in_rest' => true, // Disables Gutenberg.
				'supports'     => [ 'title', 'thumbnail', 'editor' ],
			]
		);

		add_image_size( 'scholar-profile-avatar', 300, 300, true );

		// Citation Library.
		register_post_type(
			'ssrc_citation',
			[
				'label'        => 'Citations',
				'labels'       => [
					'name'               => 'Citations',
					'singular_name'      => 'Citation',
					'add_new_item'       => 'Add New Citation',
					'edit_item'          => 'Edit Citation',
					'new_item'           => 'New Citation',
					'view_item'          => 'View Citation',
					'view_items'         => 'View Citations',
					'search_items'       => 'Search Citations',
					'not_found'          => 'No Citations found',
					'not_found_in_trash' => 'No Citations found in Trash',
					'all_items'          => 'All Citations',
					'name_admin_bar'     => 'Citations',
				],
				'public'       => true,
				'has_archive'  => false,
				'rewrite'      => [
					'slug'       => 'citation',
					'with_front' => false,
				],
				'show_ui'      => true,
				'menu_icon'    => 'dashicons-portfolio',
				'show_in_rest' => true,
				'rest_base'    => 'citation',
				'supports'     => [ 'title', 'editor' ],
			]
		);
	}

	public function register_taxonomies() {
		$post_types = [
			'ssrc_schprof_pt',
			'ssrc_citation',
			'ssrc_lit_review',
			'ssrc_expref_pt',
		];

		register_taxonomy(
			'ssrc_research_topic',
			array_merge( $post_types, [ 'nomination' ] ),
			[
				'label'        => 'Research Fields',
				'labels'       => [
					'name'          => 'Research Fields',
					'singular_name' => 'Research Field',
					'add_new_item'  => 'Add New Research Field',
					'not_found'     => 'No Research Fields found',
				],
				'hierarchical' => true,
				'public'       => true,
				'rewrite'      => [
					'slug' => 'research-topic',
				],
				'show_ui'      => true,
				'show_in_rest' => true,
				'capabilities' => [
					'manage_terms' => 'do_not_allow',
					'edit_terms'   => 'do_not_allow',
					'delete_terms' => 'do_not_allow',
					'assign_terms' => 'read',
				],
			]
		);

		register_taxonomy(
			'ssrc_focus_tag',
			array_merge( $post_types, [ 'nomination' ] ),
			[
				'label'        => 'Focus Tags',
				'labels'       => [
					'name'          => 'Focus Tags',
					'singular_name' => 'Focus Tag',
					'add_new_item'  => 'Add New Focus Tag',
					'not_found'     => 'No Focus Tags found',
				],
				'hierarchical' => true,
				'public'       => true,
				'rewrite'      => [
					'slug' => 'tag',
				],
				'show_in_rest' => true,
				'capabilities' => [
					'manage_terms' => 'edit_others_posts',
					'edit_terms'   => 'edit_others_posts',
					'delete_terms' => 'edit_others_posts',
					'assign_terms' => 'read',
				],
			]
		);

		register_taxonomy(
			'ssrc_scholar_profile',
			array_diff( $post_types, [ 'ssrc_schprof_pt' ] ),
			[
				'label'        => 'Profiles',
				'labels'       => [
					'name'          => 'Profiles',
					'singular_name' => 'Profile',
					'add_new_item'  => 'Add New Profile',
					'not_found'     => 'No Profiles found',
				],
				'hierarchical' => true,
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'capabilities' => [
					'manage_terms' => 'do_not_allow',
					'edit_terms'   => 'do_not_allow',
					'delete_terms' => 'do_not_allow',
					'assign_terms' => 'edit_posts',
				],
				'meta_box_cb'  => [ $this, 'sp_meta_box_cb' ],
			]
		);

		register_taxonomy(
			'ssrc_article_type',
			[ 'ssrc_expref_pt' ],
			[
				'label'        => 'Article Type',
				'labels'       => [
					'name'          => 'Article Type',
					'singular_name' => 'Article Types',
					'add_new_item'  => 'Add New Article Type',
					'not_found'     => 'No Article Types found',
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [
					'slug' => 'item-type',
				],
			]
		);

		register_taxonomy(
			'ssrc_item_type',
			[ 'post' ],
			[
				'label'        => 'Item Type',
				'labels'       => [
					'name'          => 'Item Type',
					'singular_name' => 'Item Types',
					'add_new_item'  => 'Add New Item Type',
					'not_found'     => 'No Item Types found',
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'show_ui'      => true,
				'rewrite'      => [
					'slug' => 'item-type',
				],
			]
		);

		register_taxonomy(
			'ssrc_profile_tag',
			[ 'ssrc_schprof_pt' ],
			[
				'label'        => 'Profile Tag',
				'labels'       => [
					'name'          => 'Profile Tag',
					'singular_name' => 'Profile Tags',
					'add_new_item'  => 'Add New Profile Tag',
					'not_found'     => 'No Profile Tags found',
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'show_ui'      => true,
				'rewrite'      => [
					'slug' => 'profile-tag',
				],
			]
		);

		register_taxonomy_for_object_type( 'ssrc_focus_tag', 'post' );
		register_taxonomy_for_object_type( 'ssrc_research_topic', 'post' );
		register_taxonomy_for_object_type( 'ssrc_scholar_profile', 'post' );

		unregister_taxonomy_for_object_type( 'post_tag', 'post' );
	}

	public function link_cpts_and_taxonomies() {
		$this->cpttaxonomies['research_topic']  = new CPTTax( 'ssrc_restop_pt', 'ssrc_research_topic' );
		$this->cpttaxonomies['scholar_profile'] = new CPTTax( 'ssrc_schprof_pt', 'ssrc_scholar_profile' );

	}

	public function get_cpttax_map( $key ) {
		return $this->cpttaxonomies[ $key ];
	}

	public function set_up_post_type_features() {

		//      $lit_reviews =
	}

	public function filter_lr_version_link( $permalink, $post ) {
		$lr_post = get_post( $post->post_parent );

		if ( ! $lr_post || 'ssrc_lit_review' !== $lr_post->post_type ) {
			return $permalink;
		}

		return str_replace( '%lrslug%', $lr_post->post_name, $permalink );
	}

	public function sync_citation_rts_to_sps( $object_id, $terms, $tt_ids, $taxonomy ) {
		$citation_post = get_post( $object_id );
		if ( ! $citation_post || 'ssrc_citation' !== $citation_post->post_type ) {
			return;
		}

		// Trigger when modifying a citation's RTs or SPs.
		if ( 'ssrc_research_topic' !== $taxonomy && 'ssrc_scholar_profile' !== $taxonomy ) {
			return;
		}

		// Requery for clarity, since $terms could be one of two different things.
		$citation_sps = wp_get_object_terms( $object_id, 'ssrc_scholar_profile' );
		$citation_rts = wp_get_object_terms( $object_id, 'ssrc_research_topic' );

		$sp_map = disinfo_app()->get_cpttax_map( 'scholar_profile' );
		foreach ( $citation_sps as $citation_sp ) {
			$sp_id = $sp_map->get_post_id_for_term_id( $citation_sp->term_id );

			// Never overwrite - these can be managed manually by SSRC team.
			wp_set_object_terms( $sp_id, wp_list_pluck( $citation_rts, 'term_id' ), 'ssrc_research_topic', true );
		}
	}

	public function add_fields_to_index( $fields, $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return;
		}

		$addl_fields = [];

		switch ( $post->post_type ) {
			case 'post':
				$addl_fields = [
					'item_author',
				];
				break;

			case 'ssrc_schprof_pt':
				$addl_fields = [
					'email_address',
					'first_name',
					'homepage_url',
					'last_name',
					'orcid_id',
					'title',
				];
				break;

			case 'ssrc_citation':
				$addl_fields = [
					'zotero_author',
				];
				break;
		}

		if ( ! $fields ) {
			$fields = [];
		}

		return array_merge( $fields, $addl_fields );
	}

	public function sp_meta_box_cb( $post ) {
		wp_enqueue_style( 'disinfo-select2' );
		wp_enqueue_script( 'disinfo-sp-meta-box', RAMP_PLUGIN_URL . '/assets/js/sp-meta-box.js', [ 'jquery', 'disinfo-select2' ], RAMP_VER, true );

		$sps = get_posts(
			[
				'post_type'      => 'ssrc_schprof_pt',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => [ 'meta_value' => 'ASC' ],
				'meta_key'       => 'last_name',
			]
		);

		$sp_map = disinfo_app()->get_cpttax_map( 'scholar_profile' );

		$terms = [];
		foreach ( $sps as $sp_id ) {
			$scholar_profile = ScholarProfile::get_instance( $sp_id );
			$term_name       = sprintf(
				'%s, %s',
				$scholar_profile->get_last_name(),
				$scholar_profile->get_first_name()
			);

			$term_id = $sp_map->get_term_id_for_post_id( $sp_id );

			$terms[] = [
				'id'   => $term_id,
				'name' => $term_name,
			];
		}

		$terms_of_post = wp_get_object_terms( $post->ID, 'ssrc_scholar_profile', [ 'fields' => 'ids' ] );

		?>
		<label for="sp-selector" class="screen-reader-text">Select Profiles</label>
		<select id="sp-selector" name="tax_input[ssrc_scholar_profile][]" multiple>
			<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term['id'] ); ?>" <?php selected( in_array( $term['id'], $terms_of_post, true ) ); ?>><?php echo esc_html( $term['name'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function check_lr_version_post_slug( $retval, $slug, $post_id, $post_status, $post_type, $post_parent ) {
		if ( 'ssrc_lr_version' !== $post_type ) {
			return $retval;
		}

		$lr_version = new LitReviews\Version( $post_id );
		$parent     = $lr_version->get_parent();

		$siblings = LitReviews\Version::get( $parent->ID );
		$conflict = false;
		foreach ( $siblings as $sibling ) {
			if ( $sibling->ID === $post_id ) {
				continue;
			}

			if ( $sibling->post_name === $slug ) {
				// Let this fall through. WP will append a suffix.
				$conflict = true;
			}
		}

		if ( ! $conflict ) {
			return $slug;
		} else {
			return $retval;
		}
	}
}
