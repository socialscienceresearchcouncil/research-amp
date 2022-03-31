<?php

namespace SSRC\RAMP;

class Schema {
	protected $cpttaxonomies = [];

	protected $sortable_taxonomies = [
		'ramp_assoc_profile',
		'ramp_assoc_topic',
	];

	protected $post_types_for_sortable_taxonomies = [
		'ramp_review',
		'ramp_article',
		'ramp_topic',
		'ramp_profile',
		'ramp_citation',
	];

	public function init() {
		add_action( 'init', [ $this, 'register_post_types' ], 5 );
		add_action( 'init', [ $this, 'register_taxonomies' ], 20 );
		add_action( 'init', [ $this, 'link_cpts_and_taxonomies' ], 30 );
		add_action( 'init', [ $this, 'register_scripts' ] );

		// Sync Citation RTs to associated SPs.
		add_action( 'set_object_terms', [ $this, 'sync_citation_rts_to_sps' ], 10, 4 );

		// Filter post_type_link for LR versions.
		add_filter( 'post_type_link', [ $this, 'filter_review_version_link' ], 10, 2 );

		// Include postmeta fields in Relevanssi.
		add_filter( 'relevanssi_index_custom_fields', [ $this, 'add_fields_to_index' ], 10, 2 );

		// Loosen restrictions on LR Version slug uniqueness.
		add_filter( 'pre_wp_unique_post_slug', [ $this, 'check_review_version_post_slug' ], 10, 6 );

		// Set default sort order for certain taxonomies.
		add_filter( 'get_terms_defaults', [ $this, 'set_get_terms_defaults' ], 10, 2 );
	}

	public function register_scripts() {
		wp_register_style(
			'ramp-directory-filters',
			RAMP_PLUGIN_URL . '/assets/css/directory-filters.css',
			[ 'ramp-select2' ],
			RAMP_VER
		);

		wp_register_script(
			'ramp-directory-filters',
			RAMP_PLUGIN_URL . '/assets/js/directory-filters.js',
			[ 'jquery', 'ramp-select2' ],
			RAMP_VER,
			true
		);

		wp_register_script(
			'ramp-load-more',
			RAMP_PLUGIN_URL . '/assets/js/load-more.js',
			[ 'jquery' ],
			RAMP_VER,
			true
		);

		wp_register_script(
			'ramp-select2',
			RAMP_PLUGIN_URL . '/lib/select2/select2.min.js',
			[ 'jquery' ],
			RAMP_VER,
			true
		);

		wp_register_style(
			'ramp-select2',
			RAMP_PLUGIN_URL . '/lib/select2/select2.min.css',
			[],
			RAMP_VER
		);

		wp_register_script(
			'ramp-sidebar',
			RAMP_PLUGIN_URL . '/assets/js/sidebar.js',
			[],
			RAMP_VER,
			true
		);

		wp_localize_script(
			'ramp-sidebar',
			'RAMPSidebar',
			[
				'buttonTextShowMore' => __( 'Show More', 'ramp' ),
				'buttonTextShowLess' => __( 'Show Less', 'ramp' ),
			]
		);

		wp_register_script(
			'ramp-altmetrics',
			RAMP_PLUGIN_URL . '/assets/js/altmetrics.js',
			[],
			RAMP_VER,
			true
		);
	}

	public function register_post_types() {
		add_post_type_support( 'page', 'excerpt' );

		// Research Review (formerly Lit Review)
		register_post_type(
			'ramp_review',
			[
				'label'             => __( 'Research Reviews', 'ramp' ),
				'labels'            => [
					'name'               => __( 'Research Reviews', 'ramp' ),
					'singular_name'      => __( 'Research Review', 'ramp' ),
					'add_new_item'       => __( 'Add New Research Review', 'ramp' ),
					'edit_item'          => __( 'Edit Research Review', 'ramp' ),
					'new_item'           => __( 'New Research Review', 'ramp' ),
					'view_item'          => __( 'View Research Review', 'ramp' ),
					'view_items'         => __( 'View Research Reviews', 'ramp' ),
					'search_items'       => __( 'Search Research Reviews', 'ramp' ),
					'not_found'          => __( 'No Research Reviews found', 'ramp' ),
					'not_found_in_trash' => __( 'No Research Reviews found in Trash', 'ramp' ),
					'all_items'          => __( 'All Research Reviews', 'ramp' ),
					'name_admin_bar'     => __( 'Research Reviews', 'ramp' ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'research-reviews',
					'with_front' => false,
				],
				'menu_icon'         => 'dashicons-book',
				'rest_base'         => 'reviews',
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
			]
		);

		// Research Review Versions.
		register_post_type(
			'ramp_review_version',
			[
				'label'             => __( 'Research Review Versions', 'ramp' ),
				'labels'            => [
					'name'               => __( 'Research Review Versions', 'ramp' ),
					'singular_name'      => __( 'Research Review Version', 'ramp' ),
					'add_new_item'       => __( 'Add New Research Review Version', 'ramp' ),
					'edit_item'          => __( 'Edit Research Review Version', 'ramp' ),
					'new_item'           => __( 'New Research Review Version', 'ramp' ),
					'view_item'          => __( 'View Research Review Version', 'ramp' ),
					'view_items'         => __( 'View Research Review Version', 'ramp' ),
					'search_items'       => __( 'Search Research Review Versions', 'ramp' ),
					'not_found'          => __( 'No Research Review Versions found', 'ramp' ),
					'not_found_in_trash' => __( 'No Research Review Versions found in Trash', 'ramp' ),
					'all_items'          => __( 'All Research Review Versions', 'ramp' ),
					'name_admin_bar'     => __( 'Research Review Versions', 'ramp' ),
				],
				'public'            => true,
				'show_ui'           => true,
				'rewrite'           => [
					'slug'       => 'research-reviews/%lrslug%/versions',
					'with_front' => false,
				],
				'has_archive'       => false,
				'menu_icon'         => 'dashicons-book',
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
			]
		);

		add_rewrite_rule(
			'^research-reviews/([^/]+)/versions/([^/]+)/?',
			'index.php?post_type=ramp_review_version&lr_slug=$matches[1]&name=$matches[2]',
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
			'ramp_article',
			[
				'label'             => __( 'Articles', 'ramp' ),
				'labels'            => [
					'name'               => __( 'Articles', 'ramp' ),
					'singular_name'      => __( 'Article', 'ramp' ),
					'add_new_item'       => __( 'Add New Article', 'ramp' ),
					'edit_item'          => __( 'Edit Article', 'ramp' ),
					'new_item'           => __( 'New Article', 'ramp' ),
					'view_item'          => __( 'View Article', 'ramp' ),
					'view_items'         => __( 'View Articles', 'ramp' ),
					'search_items'       => __( 'Search Articles', 'ramp' ),
					'not_found'          => __( 'No Articles found', 'ramp' ),
					'not_found_in_trash' => __( 'No Articles found in Trash', 'ramp' ),
					'all_items'          => __( 'All Articles', 'ramp' ),
					'name_admin_bar'     => __( 'Articles', 'ramp' ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'articles',
					'with_front' => false,
				],
				'menu_icon'         => 'dashicons-lightbulb',
				'show_in_rest'      => true,
				'rest_base'         => 'articles',
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'author', 'thumbnail' ],
			]
		);

		// @todo Should be removed.
		add_image_size( 'article-image', 565, 202, true );

		// Research Topics.
		register_post_type(
			'ramp_topic',
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
				'has_archive'       => 'research-topics',
				'rewrite'           => [
					'slug'       => 'research-topics',
					'with_front' => false,
				],
				'menu_icon'         => 'dashicons-format-status',
				'show_in_rest'      => true,
				'rest_base'         => 'research-topics',
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'page-attributes', 'thumbnail' ],
			]
		);

		// @todo Should be removed.
		add_image_size( 'research-topic-image', 564, 180, true );

		// Profiles.
		register_post_type(
			'ramp_profile',
			[
				'label'         => __( 'Profiles', 'ramp' ),
				'labels'        => [
					'name'               => __( 'Profiles', 'ramp' ),
					'singular_name'      => __( 'Profile', 'ramp' ),
					'add_new_item'       => __( 'Add New Profile', 'ramp' ),
					'edit_item'          => __( 'Edit Profile', 'ramp' ),
					'new_item'           => __( 'New Profile', 'ramp' ),
					'view_item'          => __( 'View Profile', 'ramp' ),
					'view_items'         => __( 'View Profiles', 'ramp' ),
					'search_items'       => __( 'Search Profiles', 'ramp' ),
					'not_found'          => __( 'No Profiles found', 'ramp' ),
					'not_found_in_trash' => __( 'No Profiles found in Trash', 'ramp' ),
					'all_items'          => __( 'All Profiles', 'ramp' ),
					'name_admin_bar'     => __( 'Profiles', 'ramp' ),
				],
				'public'        => true,
				'show_ui'       => true,
				'has_archive'   => true,
				'rewrite'       => [
					'slug'       => 'profiles',
					'with_front' => false,
				],
				'menu_icon'     => 'dashicons-welcome-learn-more',
				'show_in_rest'  => true,
				'rest_base'     => 'profiles',
				'template'      => [
					[
						'core/columns',
						[],
						[
							[
								'core/column',
								[ 'width' => '66.66%' ],
								[
									[
										'core/paragraph',
										[
											'content'   => __( 'Profile', 'ramp' ),
											'className' => 'ramp-header-tag',
										],
									],
									[ 'core/post-title' ],
									[
										'core/paragraph',
										[
											'className'   => 'ramp-profile-title-institution',
											'placeholder' => __( 'Enter title and institution', 'ramp' ),
										],
									],
									[
										'core/group',
										[
											'className' => 'ramp-profile-bio',
										],
										[
											[
												'core/paragraph',
												[ 'placeholder' => __( 'Enter profile bio', 'ramp' ) ],
											],
										],
									],
									[
										'ramp/profile-research-topics',
										[
											'style' => [
												'spacing' => [
													'margin' => [
														'bottom' => '48px',
														'top'    => '48px',
													],
												],
											],
										],
									],
								],
							],
							[
								'core/column',
								[ 'width' => '33.33%' ],
								[
									[ 'core/post-featured-image' ],
									[ 'core/separator' ],
									[
										'core/group',
										[
											'lock' => [
												'remove' => false,
												'move'   => false,
											],
										],
										[
											[
												'ramp/profile-vital-link',
												[ 'vitalType' => 'email' ],
											],
											[
												'ramp/profile-vital-link',
												[ 'vitalType' => 'twitter' ],
											],
											[
												'ramp/profile-vital-link',
												[ 'vitalType' => 'orcidId' ],
											],
											[
												'ramp/profile-vital-link',
												[ 'vitalType' => 'website' ],
											],
										],
									],
								],
							],
						],
					],
				],
				'template_lock' => 'all',
				'supports'      => [ 'title', 'thumbnail', 'editor', 'custom-fields' ],
			]
		);

		register_meta(
			'post',
			'ramp_vital_email',
			[
				'object_subtype' => 'ramp_profile',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Profile Email Address', 'ramp' ),
			]
		);

		register_meta(
			'post',
			'ramp_vital_twitter',
			[
				'object_subtype' => 'ramp_profile',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Profile Twitter Handle', 'ramp' ),
			]
		);

		register_meta(
			'post',
			'ramp_vital_orcid',
			[
				'object_subtype' => 'ramp_profile',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Profile ORCID ID', 'ramp' ),
			]
		);

		register_meta(
			'post',
			'ramp_vital_website',
			[
				'object_subtype' => 'ramp_profile',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Profile Website', 'ramp' ),
			]
		);

		// @todo Probably OK to keep but we should use everywhere.
		add_image_size( 'profile-avatar', 300, 300, true );

		// Citation Library.
		register_post_type(
			'ramp_citation',
			[
				'label'        => __( 'Citations', 'ramp' ),
				'labels'       => [
					'name'               => __( 'Citations', 'ramp' ),
					'singular_name'      => __( 'Citation', 'ramp' ),
					'add_new_item'       => __( 'Add New Citation', 'ramp' ),
					'edit_item'          => __( 'Edit Citation', 'ramp' ),
					'new_item'           => __( 'New Citation', 'ramp' ),
					'view_item'          => __( 'View Citation', 'ramp' ),
					'view_items'         => __( 'View Citations', 'ramp' ),
					'search_items'       => __( 'Search Citations', 'ramp' ),
					'not_found'          => __( 'No Citations found', 'ramp' ),
					'not_found_in_trash' => __( 'No Citations found in Trash', 'ramp' ),
					'all_items'          => __( 'All Citations', 'ramp' ),
					'name_admin_bar'     => __( 'Citations', 'ramp' ),
				],
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => [
					'slug'       => 'citations',
					'with_front' => false,
				],
				'show_ui'      => true,
				'menu_icon'    => 'dashicons-portfolio',
				'show_in_rest' => true,
				'rest_base'    => 'citation',
				'supports'     => [ 'title', 'editor' ],
			]
		);

		// Zotero libraries.
		register_post_type(
			'ramp_zotero_library',
			[
				'label'         => __( 'Zotero Libraries', 'ramp' ),
				'labels'        => [
					'name'               => __( 'Zotero Libraries', 'ramp' ),
					'singular_name'      => __( 'Zotero Library', 'ramp' ),
					'add_new_item'       => __( 'Add New Zotero Library', 'ramp' ),
					'edit_item'          => __( 'Edit Zotero Library', 'ramp' ),
					'new_item'           => __( 'New Zotero Library', 'ramp' ),
					'view_item'          => __( 'View Zotero Library', 'ramp' ),
					'view_items'         => __( 'View Zotero Library', 'ramp' ),
					'search_items'       => __( 'Search Zotero Libraries', 'ramp' ),
					'not_found'          => __( 'No Zotero Libraries found', 'ramp' ),
					'not_found_in_trash' => __( 'No Zotero Libraries found in Trash', 'ramp' ),
					'all_items'          => __( 'All Zotero Libraries', 'ramp' ),
					'name_admin_bar'     => __( 'Zotero Libraries', 'ramp' ),
				],
				'public'        => false,
				'has_archive'   => false,
				'show_ui'       => true,
				'menu_icon'     => 'dashicons-book-alt',
				'show_in_rest'  => true,
				'rest_base'     => 'zotero-library',
				'supports'      => [ 'title', 'editor', 'custom-fields' ],
				'template'      => [ [ 'ramp/zotero-library-info-help' ] ],
				'template_lock' => 'all',
			]
		);

		register_meta(
			'post',
			'zotero_group_id',
			[
				'object_subtype' => 'ramp_zotero_library',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Zotero Group ID', 'ramp' ),
			]
		);

		register_meta(
			'post',
			'zotero_group_url',
			[
				'object_subtype' => 'ramp_zotero_library',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Zotero Group URL', 'ramp' ),
			]
		);

		register_meta(
			'post',
			'zotero_api_key',
			[
				'object_subtype' => 'ramp_zotero_library',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Zotero API Key', 'ramp' ),
			]
		);
	}

	public function register_taxonomies() {
		$post_types = [
			'ramp_profile',
			'ramp_citation',
			'ramp_review',
			'ramp_article',
		];

		register_taxonomy(
			'ramp_assoc_topic',
			array_merge( $post_types, [ 'nomination' ] ),
			[
				'label'        => __( 'Research Fields', 'ramp' ),
				'labels'       => [
					'name'          => __( 'Research Fields', 'ramp' ),
					'singular_name' => __( 'Research Field', 'ramp' ),
					'add_new_item'  => __( 'Add New Research Field', 'ramp' ),
					'not_found'     => __( 'No Research Fields found', 'ramp' ),
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
			'ramp_focus_tag',
			array_merge( $post_types, [ 'nomination' ] ),
			[
				'label'        => __( 'Focus Tags', 'ramp' ),
				'labels'       => [
					'name'          => __( 'Focus Tags', 'ramp' ),
					'singular_name' => __( 'Focus Tag', 'ramp' ),
					'add_new_item'  => __( 'Add New Focus Tag', 'ramp' ),
					'not_found'     => __( 'No Focus Tags found', 'ramp' ),
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
			'ramp_assoc_profile',
			array_diff( $post_types, [ 'ramp_profile' ] ),
			[
				'label'        => __( 'Profiles', 'ramp' ),
				'labels'       => [
					'name'          => __( 'Profiles', 'ramp' ),
					'singular_name' => __( 'Profile', 'ramp' ),
					'add_new_item'  => __( 'Add New Profile', 'ramp' ),
					'not_found'     => __( 'No Profiles found', 'ramp' ),
				],
				'hierarchical' => true,
				'public'       => false,
				'rest_base'    => 'associated-profiles',
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
			'ramp_article_type',
			[ 'ramp_article' ],
			[
				'label'        => __( 'Article Type', 'ramp' ),
				'labels'       => [
					'name'          => __( 'Article Type', 'ramp' ),
					'singular_name' => __( 'Article Types', 'ramp' ),
					'add_new_item'  => __( 'Add New Article Type', 'ramp' ),
					'not_found'     => __( 'No Article Types found', 'ramp' ),
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'rest_base'    => 'article-types',
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [
					'slug' => 'item-type',
				],
			]
		);

		register_taxonomy(
			'ramp_item_type',
			[ 'post' ],
			[
				'label'        => __( 'Item Type', 'ramp' ),
				'labels'       => [
					'name'          => __( 'Item Type', 'ramp' ),
					'singular_name' => __( 'Item Types', 'ramp' ),
					'add_new_item'  => __( 'Add New Item Type', 'ramp' ),
					'not_found'     => __( 'No Item Types found', 'ramp' ),
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
			'ramp_profile_tag',
			[ 'ramp_profile' ],
			[
				'label'        => __( 'Profile Tag', 'ramp' ),
				'labels'       => [
					'name'          => __( 'Profile Tag', 'ramp' ),
					'singular_name' => __( 'Profile Tags', 'ramp' ),
					'add_new_item'  => __( 'Add New Profile Tag', 'ramp' ),
					'not_found'     => __( 'No Profile Tags found', 'ramp' ),
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'show_ui'      => true,
				'rewrite'      => [
					'slug' => 'profile-tag',
				],
			]
		);

		register_taxonomy_for_object_type( 'ramp_focus_tag', 'post' );
		register_taxonomy_for_object_type( 'ramp_assoc_topic', 'post' );
		register_taxonomy_for_object_type( 'ramp_assoc_profile', 'post' );

		unregister_taxonomy_for_object_type( 'post_tag', 'post' );

		// Set up taxonomy sync.
		new Libraries\TaxonomyOrder(
			$this->post_types_for_sortable_taxonomies,
			$this->sortable_taxonomies
		);
	}

	public function link_cpts_and_taxonomies() {
		$this->cpttaxonomies['research_topic'] = new CPTTax( 'ramp_topic', 'ramp_assoc_topic' );
		$this->cpttaxonomies['profile']        = new CPTTax( 'ramp_profile', 'ramp_assoc_profile' );
	}

	public function get_cpttax_map( $key ) {
		return $this->cpttaxonomies[ $key ];
	}

	public function filter_review_version_link( $permalink, $post ) {
		$lr_post = get_post( $post->post_parent );

		if ( ! $lr_post || 'ramp_review' !== $lr_post->post_type ) {
			return $permalink;
		}

		return str_replace( '%lrslug%', $lr_post->post_name, $permalink );
	}

	public function sync_citation_rts_to_sps( $object_id, $terms, $tt_ids, $taxonomy ) {
		$citation_post = get_post( $object_id );
		if ( ! $citation_post || 'ramp_citation' !== $citation_post->post_type ) {
			return;
		}

		// Trigger when modifying a citation's RTs or SPs.
		if ( 'ramp_assoc_topic' !== $taxonomy && 'ramp_assoc_profile' !== $taxonomy ) {
			return;
		}

		// Requery for clarity, since $terms could be one of two different things.
		$citation_sps = wp_get_object_terms( $object_id, 'ramp_assoc_profile' );
		$citation_rts = wp_get_object_terms( $object_id, 'ramp_assoc_topic' );

		$sp_map = ramp_app()->get_cpttax_map( 'profile' );
		foreach ( $citation_sps as $citation_sp ) {
			$sp_id = $sp_map->get_post_id_for_term_id( $citation_sp->term_id );

			// Never overwrite - these can be managed manually by SSRC team.
			wp_set_object_terms( $sp_id, wp_list_pluck( $citation_rts, 'term_id' ), 'ramp_assoc_topic', true );
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

			case 'ramp_profile':
				$addl_fields = [
					'email_address',
					'first_name',
					'homepage_url',
					'last_name',
					'orcid_id',
					'title',
				];
				break;

			case 'ramp_citation':
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
		wp_enqueue_style( 'ramp-select2' );
		wp_enqueue_script( 'ramp-sp-meta-box', RAMP_PLUGIN_URL . '/assets/js/sp-meta-box.js', [ 'jquery', 'ramp-select2' ], RAMP_VER, true );

		$sps = get_posts(
			[
				'post_type'      => 'ramp_profile',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => [ 'meta_value' => 'ASC' ],
				'meta_key'       => 'last_name',
			]
		);

		$sp_map = ramp_app()->get_cpttax_map( 'profile' );

		$terms = [];
		foreach ( $sps as $sp_id ) {
			$profile   = Profile::get_instance( $sp_id );
			$term_name = sprintf(
				'%s, %s',
				$profile->get_last_name(),
				$profile->get_first_name()
			);

			$term_id = $sp_map->get_term_id_for_post_id( $sp_id );

			$terms[] = [
				'id'   => $term_id,
				'name' => $term_name,
			];
		}

		$terms_of_post = wp_get_object_terms( $post->ID, 'ramp_assoc_profile', [ 'fields' => 'ids' ] );

		?>
		<label for="sp-selector" class="screen-reader-text"><?php esc_html_e( 'Select Profiles', 'ramp' ); ?></label>
		<select id="sp-selector" name="tax_input[ramp_assoc_profile][]" multiple>
			<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term['id'] ); ?>" <?php selected( in_array( $term['id'], $terms_of_post, true ) ); ?>><?php echo esc_html( $term['name'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function check_review_version_post_slug( $retval, $slug, $post_id, $post_status, $post_type, $post_parent ) {
		if ( 'ramp_review_version' !== $post_type ) {
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

	public function set_get_terms_defaults( $defaults, $taxonomies ) {
		// Err on the side of caution.
		if ( array_diff( $taxonomies, $this->sortable_taxonomies ) ) {
			return $defaults;
		}

		$defaults['orderby'] = 'term_order';

		return $defaults;
	}
}
