<?php
/**
 * Schema.
 *
 * @package SSRC\RAMP
 */

namespace SSRC\RAMP;

/**
 * Schema definition.
 *
 * @since 1.0.0
 */
class Schema {
	/**
	 * CPT-taxonomy mappings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $cpttaxonomies = [];

	/**
	 * Sortable taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $sortable_taxonomies = [
		'ramp_assoc_profile',
		'ramp_assoc_topic',
	];

	/**
	 * Post types for sortable taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $post_types_for_sortable_taxonomies = [
		'ramp_review',
		'ramp_article',
		'ramp_topic',
		'ramp_profile',
		'ramp_citation',
	];

	/**
	 * Initializes the schema setup.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_post_types' ], 5 );
		add_action( 'init', [ $this, 'register_taxonomies' ], 20 );
		add_action( 'init', [ $this, 'link_cpts_and_taxonomies' ], 30 );
		add_action( 'init', [ $this, 'register_scripts' ] );

		// Sync Citation RTs to associated SPs.
		add_action( 'set_object_terms', [ $this, 'sync_citation_rts_to_sps' ], 10, 4 );

		// Sync Research Topics to nav menu.
		add_action( 'save_post', [ $this, 'sync_rts_to_nav_menu' ], 10, 2 );

		// Include postmeta fields in Relevanssi.
		add_filter( 'relevanssi_index_custom_fields', [ $this, 'add_fields_to_index' ], 10, 2 );

		// Set default sort order for certain taxonomies.
		add_filter( 'get_terms_defaults', [ $this, 'set_get_terms_defaults' ], 10, 2 );
	}

	/**
	 * Registers scripts used by the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_scripts() {
		wp_register_style(
			'ramp-directory-filters',
			RAMP_PLUGIN_URL . '/assets/css/directory-filters.css',
			[ 'ramp-select2', 'wp-block-button' ],
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
				'buttonTextShowMore' => __( 'Show More', 'research-amp' ),
				'buttonTextShowLess' => __( 'Show Less', 'research-amp' ),
			]
		);

		wp_register_script(
			'ramp-altmetrics',
			RAMP_PLUGIN_URL . '/assets/js/altmetrics.js',
			[],
			RAMP_VER,
			true
		);

		// Always enqueue navigation scripts.
		wp_enqueue_script(
			'ramp-navigation',
			RAMP_PLUGIN_URL . '/assets/js/navigation.js',
			[],
			RAMP_VER,
			true
		);
	}

	/**
	 * Registers post types.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_post_types() {
		add_post_type_support( 'page', 'excerpt' );

		// Research Review.
		register_post_type(
			'ramp_review',
			[
				'label'             => __( 'Research Reviews', 'research-amp' ),
				'labels'            => [
					'name'               => __( 'Research Reviews', 'research-amp' ),
					'singular_name'      => __( 'Research Review', 'research-amp' ),
					'add_new_item'       => __( 'Add New Research Review', 'research-amp' ),
					'edit_item'          => __( 'Edit Research Review', 'research-amp' ),
					'new_item'           => __( 'New Research Review', 'research-amp' ),
					'view_item'          => __( 'View Research Review', 'research-amp' ),
					'view_items'         => __( 'View Research Reviews', 'research-amp' ),
					'search_items'       => __( 'Search Research Reviews', 'research-amp' ),
					'not_found'          => __( 'No Research Reviews found', 'research-amp' ),
					'not_found_in_trash' => __( 'No Research Reviews found in Trash', 'research-amp' ),
					'all_items'          => __( 'All Research Reviews', 'research-amp' ),
					'name_admin_bar'     => __( 'Research Reviews', 'research-amp' ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'research-reviews',
					'with_front' => false,
				],
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'         => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path fill="black" d="M2 9V7H7V9ZM2 14V12H7V14ZM20.6 19 16.75 15.15Q16.15 15.575 15.438 15.787Q14.725 16 14 16Q11.925 16 10.463 14.537Q9 13.075 9 11Q9 8.925 10.463 7.462Q11.925 6 14 6Q16.075 6 17.538 7.462Q19 8.925 19 11Q19 11.725 18.788 12.438Q18.575 13.15 18.15 13.75L22 17.6ZM14 14Q15.25 14 16.125 13.125Q17 12.25 17 11Q17 9.75 16.125 8.875Q15.25 8 14 8Q12.75 8 11.875 8.875Q11 9.75 11 11Q11 12.25 11.875 13.125Q12.75 14 14 14ZM2 19V17H12V19Z"/></svg>' ),
				'rest_base'         => 'reviews',
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
			]
		);

		register_meta(
			'ramp_review',
			'ramp_changelog',
			[
				'object_subtype' => 'ramp_review',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Changelog', 'research-amp' ),
			]
		);

		// Articles
		register_post_type(
			'ramp_article',
			[
				'label'             => __( 'Articles', 'research-amp' ),
				'labels'            => [
					'name'               => __( 'Articles', 'research-amp' ),
					'singular_name'      => __( 'Article', 'research-amp' ),
					'add_new_item'       => __( 'Add New Article', 'research-amp' ),
					'edit_item'          => __( 'Edit Article', 'research-amp' ),
					'new_item'           => __( 'New Article', 'research-amp' ),
					'view_item'          => __( 'View Article', 'research-amp' ),
					'view_items'         => __( 'View Articles', 'research-amp' ),
					'search_items'       => __( 'Search Articles', 'research-amp' ),
					'not_found'          => __( 'No Articles found', 'research-amp' ),
					'not_found_in_trash' => __( 'No Articles found in Trash', 'research-amp' ),
					'all_items'          => __( 'All Articles', 'research-amp' ),
					'name_admin_bar'     => __( 'Articles', 'research-amp' ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'articles',
					'with_front' => false,
				],
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'         => 'data:image/svg+xml;base64,' . base64_encode( '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve"><path fill="black" d="M5,29.2c-0.8,0-1.5-0.3-2.1-0.9S2,27,2,26.2v-16c0-0.8,0.3-1.5,0.9-2.1S4.2,7.2,5,7.2h30c0.8,0,1.5,0.3,2.1,0.9 S38,9.4,38,10.2v16c0,0.8-0.3,1.5-0.9,2.1c-0.6,0.6-1.3,0.9-2.1,0.9H5z M5,26.2h30l0,0l0,0v-16l0,0l0,0H5l0,0l0,0V26.2L5,26.2 L5,26.2z M2,36.1h27v3H2V36.1z M5,10.2L5,10.2L5,10.2v16l0,0l0,0l0,0l0,0V10.2L5,10.2L5,10.2z"/></svg>' ),
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
				'label'             => __( 'Research Topics', 'research-amp' ),
				'labels'            => [
					'name'               => __( 'Research Topics', 'research-amp' ),
					'singular_name'      => __( 'Research Topic', 'research-amp' ),
					'add_new_item'       => __( 'Add New Research Topic', 'research-amp' ),
					'edit_item'          => __( 'Edit Research Topic', 'research-amp' ),
					'new_item'           => __( 'New Research Topic', 'research-amp' ),
					'view_item'          => __( 'View Research Topic', 'research-amp' ),
					'view_items'         => __( 'View Research Topics', 'research-amp' ),
					'search_items'       => __( 'Search Research Topics', 'research-amp' ),
					'not_found'          => __( 'No Research Topics found', 'research-amp' ),
					'not_found_in_trash' => __( 'No Research Topics found in Trash', 'research-amp' ),
					'all_items'          => __( 'All Research Topics', 'research-amp' ),
					'name_admin_bar'     => __( 'Research Topics', 'research-amp' ),
				],
				'public'            => true,
				'has_archive'       => 'research-topics',
				'rewrite'           => [
					'slug'       => 'research-topics',
					'with_front' => false,
				],
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'         => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path fill="black" d="M2 9V7H7V9ZM2 14V12H7V14ZM20.6 19 16.75 15.15Q16.15 15.575 15.438 15.787Q14.725 16 14 16Q11.925 16 10.463 14.537Q9 13.075 9 11Q9 8.925 10.463 7.462Q11.925 6 14 6Q16.075 6 17.538 7.462Q19 8.925 19 11Q19 11.725 18.788 12.438Q18.575 13.15 18.15 13.75L22 17.6ZM14 14Q15.25 14 16.125 13.125Q17 12.25 17 11Q17 9.75 16.125 8.875Q15.25 8 14 8Q12.75 8 11.875 8.875Q11 9.75 11 11Q11 12.25 11.875 13.125Q12.75 14 14 14ZM2 19V17H12V19Z"/></svg>' ),
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
				'label'        => __( 'Profiles', 'research-amp' ),
				'labels'       => [
					'name'               => __( 'Profiles', 'research-amp' ),
					'singular_name'      => __( 'Profile', 'research-amp' ),
					'add_new_item'       => __( 'Add New Profile', 'research-amp' ),
					'edit_item'          => __( 'Edit Profile', 'research-amp' ),
					'new_item'           => __( 'New Profile', 'research-amp' ),
					'view_item'          => __( 'View Profile', 'research-amp' ),
					'view_items'         => __( 'View Profiles', 'research-amp' ),
					'search_items'       => __( 'Search Profiles', 'research-amp' ),
					'not_found'          => __( 'No Profiles found', 'research-amp' ),
					'not_found_in_trash' => __( 'No Profiles found in Trash', 'research-amp' ),
					'all_items'          => __( 'All Profiles', 'research-amp' ),
					'name_admin_bar'     => __( 'Profiles', 'research-amp' ),
				],
				'public'       => true,
				'show_ui'      => true,
				'has_archive'  => true,
				'rewrite'      => [
					'slug'       => 'profiles',
					'with_front' => false,
				],
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'    => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path fill="black" d="M12 12Q10.35 12 9.175 10.825Q8 9.65 8 8Q8 6.35 9.175 5.175Q10.35 4 12 4Q13.65 4 14.825 5.175Q16 6.35 16 8Q16 9.65 14.825 10.825Q13.65 12 12 12ZM4 20V17.2Q4 16.35 4.438 15.637Q4.875 14.925 5.6 14.55Q7.15 13.775 8.75 13.387Q10.35 13 12 13Q13.65 13 15.25 13.387Q16.85 13.775 18.4 14.55Q19.125 14.925 19.562 15.637Q20 16.35 20 17.2V20Z"/></svg>' ),
				'show_in_rest' => true,
				'rest_base'    => 'profiles',
				'template'     => [
					// Top section.
					[
						'core/group',
						[],
						[
							// "Profile" item type label.
							[ 'research-amp/item-type-label' ],

							// Profile display name.
							[ 'core/post-title', [ 'level' => 1 ] ],

							// Title/institution field.
							[ 'research-amp/profile-title-institution' ],

							// Profile types.
							[ 'research-amp/profile-types' ],
						],
					],

					// Main body section.
					[
						'core/columns',
						[],
						[
							// Column with profile photo and social links
							[
								'core/column',
								[ 'width' => '33.33%' ],
								[
									// Profile photo.
									[ 'research-amp/profile-photo' ],

									// Social links.
									[
										'core/group',
										[],
										[
											[
												'research-amp/profile-vital-link',
												[ 'vitalType' => 'email' ],
											],
											[
												'research-amp/profile-vital-link',
												[ 'vitalType' => 'twitter' ],
											],
											[
												'research-amp/profile-vital-link',
												[ 'vitalType' => 'orcidId' ],
											],
											[
												'research-amp/profile-vital-link',
												[ 'vitalType' => 'website' ],
											],
										],
									],
								],
							],

							// Column with bio and research topics.
							[
								'core/column',
								[ 'width' => '66.66%' ],
								[
									[ 'research-amp/profile-bio' ],
									[ 'research-amp/item-research-topics' ],
									[
										'core/post-terms',
										[
											'term'   => 'ramp_focus_tag',
											'prefix' => __( 'Tags: ', 'research-amp' ),
										],
									],
								],
							],
						],
					],
				],
				'supports'     => [ 'title', 'thumbnail', 'editor', 'custom-fields' ],
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
				'description'    => __( 'Profile Email Address', 'research-amp' ),
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
				'description'    => __( 'Profile Twitter Handle', 'research-amp' ),
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
				'description'    => __( 'Profile ORCID ID', 'research-amp' ),
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
				'description'    => __( 'Profile Website', 'research-amp' ),
			]
		);

		register_meta(
			'post',
			'alphabetical_name',
			[
				'object_subtype' => 'ramp_profile',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Name for Alphabetical Sort', 'research-amp' ),
			]
		);

		// @todo Probably OK to keep but we should use everywhere.
		add_image_size( 'profile-avatar', 300, 300, true );

		// Citation Library.
		register_post_type(
			'ramp_citation',
			[
				'label'        => __( 'Citations', 'research-amp' ),
				'labels'       => [
					'name'               => __( 'Citations', 'research-amp' ),
					'singular_name'      => __( 'Citation', 'research-amp' ),
					'add_new_item'       => __( 'Add New Citation', 'research-amp' ),
					'edit_item'          => __( 'Edit Citation', 'research-amp' ),
					'new_item'           => __( 'New Citation', 'research-amp' ),
					'view_item'          => __( 'View Citation', 'research-amp' ),
					'view_items'         => __( 'View Citations', 'research-amp' ),
					'search_items'       => __( 'Search Citations', 'research-amp' ),
					'not_found'          => __( 'No Citations found', 'research-amp' ),
					'not_found_in_trash' => __( 'No Citations found in Trash', 'research-amp' ),
					'all_items'          => __( 'All Citations', 'research-amp' ),
					'name_admin_bar'     => __( 'Citations', 'research-amp' ),
				],
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => [
					'slug'       => 'citations',
					'with_front' => false,
				],
				'show_ui'      => true,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'    => 'data:image/svg+xml;base64,' . base64_encode( '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve"><g><path fill="black" d="M12,34.6c-2.5,0-4.4-0.9-5.8-2.6C4.7,30.3,4,28,4,25.1c0-4.2,1.2-8,3.5-11.4c2.3-3.4,5.9-6.7,10.9-9.9l2.5,2.9 c-3.3,2.7-5.9,5.2-7.7,7.6c-1.8,2.4-2.9,4.5-3,6.4l0.5,0.3c0.4-0.7,1.3-1,2.9-1c1.7,0,3.1,0.7,4.3,2c1.2,1.3,1.7,3.1,1.7,5.1 c0,2.2-0.7,4-2.1,5.4C16,34,14.2,34.6,12,34.6z M35.1,34.6c-2.5,0-4.4-0.9-5.8-2.6c-1.4-1.7-2.1-4.1-2.1-7c0-4.2,1.2-8,3.5-11.4 c2.3-3.4,5.9-6.7,10.9-9.9L44,6.7c-3.3,2.7-5.9,5.2-7.7,7.6c-1.8,2.4-2.9,4.5-3,6.4l0.5,0.3c0.4-0.7,1.3-1,2.9-1 c1.7,0,3.1,0.7,4.3,2c1.2,1.3,1.7,3.1,1.7,5.1c0,2.2-0.7,4-2.1,5.4C39.1,34,37.3,34.6,35.1,34.6z"/></g></svg>' ),
				'show_in_rest' => true,
				'rest_base'    => 'citation',
				'supports'     => [ 'title', 'editor' ],
			]
		);

		// News Item.
		register_post_type(
			'ramp_news_item',
			[
				'label'             => __( 'News Items', 'research-amp' ),
				'labels'            => [
					'name'               => __( 'News Items', 'research-amp' ),
					'singular_name'      => __( 'News Item', 'research-amp' ),
					'add_new_item'       => __( 'Add New News Item', 'research-amp' ),
					'edit_item'          => __( 'Edit News Item', 'research-amp' ),
					'new_item'           => __( 'New News Item', 'research-amp' ),
					'view_item'          => __( 'View News Item', 'research-amp' ),
					'view_items'         => __( 'View News Items', 'research-amp' ),
					'search_items'       => __( 'Search News Items', 'research-amp' ),
					'not_found'          => __( 'No News Items found', 'research-amp' ),
					'not_found_in_trash' => __( 'No News Items found in Trash', 'research-amp' ),
					'all_items'          => __( 'All News Items', 'research-amp' ),
					'name_admin_bar'     => __( 'News Items', 'research-amp' ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [
					'slug'       => 'news-items',
					'with_front' => false,
				],
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'         => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path fill="black" d="M5 21Q4.175 21 3.587 20.413Q3 19.825 3 19V5Q3 4.175 3.587 3.587Q4.175 3 5 3H16L21 8V19Q21 19.825 20.413 20.413Q19.825 21 19 21ZM15 9H19L15 5ZM7 9H12V7H7ZM7 13H17V11H7ZM7 17H17V15H7Z"/></svg>' ),
				'rest_base'         => 'news-items',
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
			]
		);

		// Zotero libraries.
		register_post_type(
			'ramp_zotero_library',
			[
				'label'         => __( 'Zotero Libraries', 'research-amp' ),
				'labels'        => [
					'name'               => __( 'Zotero Libraries', 'research-amp' ),
					'singular_name'      => __( 'Zotero Library', 'research-amp' ),
					'add_new_item'       => __( 'Add New Zotero Library', 'research-amp' ),
					'edit_item'          => __( 'Edit Zotero Library', 'research-amp' ),
					'new_item'           => __( 'New Zotero Library', 'research-amp' ),
					'view_item'          => __( 'View Zotero Library', 'research-amp' ),
					'view_items'         => __( 'View Zotero Library', 'research-amp' ),
					'search_items'       => __( 'Search Zotero Libraries', 'research-amp' ),
					'not_found'          => __( 'No Zotero Libraries found', 'research-amp' ),
					'not_found_in_trash' => __( 'No Zotero Libraries found in Trash', 'research-amp' ),
					'all_items'          => __( 'All Zotero Libraries', 'research-amp' ),
					'name_admin_bar'     => __( 'Zotero Libraries', 'research-amp' ),
				],
				'public'        => false,
				'has_archive'   => false,
				'show_ui'       => true,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'     => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path fill="black" d="M8 18Q7.175 18 6.588 17.413Q6 16.825 6 16V4Q6 3.175 6.588 2.587Q7.175 2 8 2H20Q20.825 2 21.413 2.587Q22 3.175 22 4V16Q22 16.825 21.413 17.413Q20.825 18 20 18ZM13 4V11L15.5 9.5L18 11V4ZM4 22Q3.175 22 2.588 21.413Q2 20.825 2 20V6H4V20Q4 20 4 20Q4 20 4 20H18V22Z"/></svg>' ),
				'show_in_rest'  => true,
				'rest_base'     => 'zotero-library',
				'supports'      => [ 'title', 'editor', 'custom-fields' ],
				'template'      => [ [ 'research-amp/zotero-library-info' ] ],
				'template_lock' => 'all',
			]
		);

		register_meta(
			'post',
			'zotero_library_id',
			[
				'object_subtype' => 'ramp_zotero_library',
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
				'description'    => __( 'Zotero Library ID', 'research-amp' ),
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
				'description'    => __( 'Zotero Group URL', 'research-amp' ),
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
				'description'    => __( 'Zotero API Key', 'research-amp' ),
			]
		);
	}

	/**
	 * Register taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		$post_types = [
			'ramp_profile',
			'ramp_citation',
			'ramp_review',
			'ramp_article',
			'ramp_news_item',
		];

		register_taxonomy(
			'ramp_assoc_topic',
			array_merge( $post_types, [ 'nomination' ] ),
			[
				'label'        => __( 'Research Topics', 'research-amp' ),
				'labels'       => [
					'name'          => __( 'Research Topics', 'research-amp' ),
					'singular_name' => __( 'Research Topic', 'research-amp' ),
					'add_new_item'  => __( 'Add New Research Topic', 'research-amp' ),
					'not_found'     => __( 'No Research Topics found', 'research-amp' ),
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
				'label'        => __( 'Focus Tags', 'research-amp' ),
				'labels'       => [
					'name'          => __( 'Focus Tags', 'research-amp' ),
					'singular_name' => __( 'Focus Tag', 'research-amp' ),
					'add_new_item'  => __( 'Add New Focus Tag', 'research-amp' ),
					'not_found'     => __( 'No Focus Tags found', 'research-amp' ),
				],
				'hierarchical' => false,
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
				'label'        => __( 'Profiles', 'research-amp' ),
				'labels'       => [
					'name'          => __( 'Profiles', 'research-amp' ),
					'singular_name' => __( 'Profile', 'research-amp' ),
					'add_new_item'  => __( 'Add New Profile', 'research-amp' ),
					'not_found'     => __( 'No Profiles found', 'research-amp' ),
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
			]
		);

		register_taxonomy(
			'ramp_article_type',
			[ 'ramp_article' ],
			[
				'label'        => __( 'Article Type', 'research-amp' ),
				'labels'       => [
					'name'          => __( 'Article Type', 'research-amp' ),
					'singular_name' => __( 'Article Types', 'research-amp' ),
					'add_new_item'  => __( 'Add New Article Type', 'research-amp' ),
					'not_found'     => __( 'No Article Types found', 'research-amp' ),
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'rest_base'    => 'article-types',
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [
					'slug' => 'article-type',
				],
			]
		);

		register_taxonomy(
			'ramp_profile_type',
			[ 'ramp_profile' ],
			[
				'label'        => __( 'Profile Type', 'research-amp' ),
				'labels'       => [
					'name'          => __( 'Profile Type', 'research-amp' ),
					'singular_name' => __( 'Profile Types', 'research-amp' ),
					'add_new_item'  => __( 'Add New Profile Type', 'research-amp' ),
					'not_found'     => __( 'No Profile Types found', 'research-amp' ),
				],
				'hierarchical' => true, // Just to get the checkboxes.
				'public'       => true,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [
					'slug' => 'profile-type',
				],
			]
		);

		// Set up taxonomy sync.
		new Libraries\TaxonomyOrder(
			$this->post_types_for_sortable_taxonomies,
			$this->sortable_taxonomies
		);
	}

	/**
	 * Sets up mappings between CPTs and taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function link_cpts_and_taxonomies() {
		$this->cpttaxonomies['research_topic'] = new CPTTax( 'ramp_topic', 'ramp_assoc_topic' );
		$this->cpttaxonomies['profile']        = new CPTTax( 'ramp_profile', 'ramp_assoc_profile' );
	}

	/**
	 * Utility method for fetching a CPT-taxonomy mapping.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The key for the mapping to fetch.
	 * @return CPTTax
	 */
	public function get_cpttax_map( $key ) {
		return $this->cpttaxonomies[ $key ];
	}

	/**
	 * Syncs research topics from citations to corresponding profiles.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $object_id ID of the object being updated.
	 * @param array  $terms     An array of term IDs to update the object with.
	 * @param array  $tt_ids    An array of term taxonomy IDs.
	 * @param string $taxonomy  Taxonomy slug.
	 * @return void
	 */
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

	/**
	 * Syncs newly created or edited research topics to the corresponding nav menu.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id ID of the post being saved.
	 * @param WP_Post $post    The post object.
	 * @return void
	 */
	public function sync_rts_to_nav_menu( $post_id, $post ) {
		if ( 'ramp_topic' !== $post->post_type ) {
			return;
		}

		Util\Navigation::replace_research_topics_subnav();
	}

	/**
	 * Adds custom fields to Relevannsi index.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields  Array of fields to index.
	 * @param int   $post_id ID of the post being indexed.
	 * @return array
	 */
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
		<label for="sp-selector" class="screen-reader-text"><?php esc_html_e( 'Select Profiles', 'research-amp' ); ?></label>
		<select id="sp-selector" name="tax_input[ramp_assoc_profile][]" multiple>
			<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term['id'] ); ?>" <?php selected( in_array( $term['id'], $terms_of_post, true ) ); ?>><?php echo esc_html( $term['name'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Sets the default sort order for our sortable taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults   Array of default query vars.
	 * @param array $taxonomies Array of taxonomies.
	 * @return array
	 */
	public function set_get_terms_defaults( $defaults, $taxonomies ) {
		// Err on the side of caution.
		if ( is_array( $taxonomies ) && array_diff( $taxonomies, $this->sortable_taxonomies ) ) {
			return $defaults;
		}

		$defaults['orderby'] = 'term_order';

		return $defaults;
	}

	/**
	 * Gets a list of taxonomy terms associated with a post type.
	 *
	 * @param string $taxonomy  Taxonomy name.
	 * @param string $post_type Post type name.
	 * @return array
	 */
	public static function get_terms_belonging_to_post_type( $taxonomy, $post_type ) {
		$last_changed = wp_cache_get_last_changed( 'posts' );
		$cache_key    = "$taxonomy-ids-$last_changed";
		$cache_group  = $post_type . '-terms';

		$tax_term_ids = wp_cache_get( $cache_key, $cache_group );
		if ( false === $tax_term_ids ) {
			$post_ids = get_posts(
				[
					'post_type'      => $post_type,
					'fields'         => 'ids',
					'posts_per_page' => -1,
				]
			);

			$tax_term_ids = wp_get_object_terms(
				$post_ids,
				$taxonomy,
				[
					'fields'  => 'ids',
					'orderby' => 'name',
				]
			);

			wp_cache_set( $cache_key, $tax_term_ids, $cache_group );
		} else {
			$tax_term_ids = array_map( 'intval', $tax_term_ids );
		}

		$terms = array_map( 'get_term', $tax_term_ids );

		return $terms;
	}
}
