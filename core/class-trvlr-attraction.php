<?php

/**
 * The file that defines the core business logic and CPT for Attractions
 *
 * @package    Trvlr
 * @subpackage Trvlr/core
 */

class Trvlr_Attraction {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}

	/**
	 * Register the 'trvlr_attraction' custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => _x( 'Attractions', 'Post Type General Name', 'trvlr' ),
			'singular_name'         => _x( 'Attraction', 'Post Type Singular Name', 'trvlr' ),
			'menu_name'             => __( 'Attractions', 'trvlr' ),
			'name_admin_bar'        => __( 'Attraction', 'trvlr' ),
			'archives'              => __( 'Attraction Archives', 'trvlr' ),
			'attributes'            => __( 'Attraction Attributes', 'trvlr' ),
			'parent_item_colon'     => __( 'Parent Attraction:', 'trvlr' ),
			'all_items'             => __( 'All Attractions', 'trvlr' ),
			'add_new_item'          => __( 'Add New Attraction', 'trvlr' ),
			'add_new'               => __( 'Add New', 'trvlr' ),
			'new_item'              => __( 'New Attraction', 'trvlr' ),
			'edit_item'             => __( 'Edit Attraction', 'trvlr' ),
			'update_item'           => __( 'Update Attraction', 'trvlr' ),
			'view_item'             => __( 'View Attraction', 'trvlr' ),
			'view_items'            => __( 'View Attractions', 'trvlr' ),
			'search_items'          => __( 'Search Attraction', 'trvlr' ),
			'not_found'             => __( 'Not found', 'trvlr' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'trvlr' ),
			'featured_image'        => __( 'Featured Image', 'trvlr' ),
			'set_featured_image'    => __( 'Set featured image', 'trvlr' ),
			'remove_featured_image' => __( 'Remove featured image', 'trvlr' ),
			'use_featured_image'    => __( 'Use as featured image', 'trvlr' ),
			'insert_into_item'      => __( 'Insert into attraction', 'trvlr' ),
			'uploaded_to_this_item' => __( 'Uploaded to this attraction', 'trvlr' ),
			'items_list'            => __( 'Attractions list', 'trvlr' ),
			'items_list_navigation' => __( 'Attractions list navigation', 'trvlr' ),
			'filter_items_list'     => __( 'Filter attractions list', 'trvlr' ),
		);
		$args = array(
			'label'                 => __( 'Attraction', 'trvlr' ),
			'description'           => __( 'Tours and Experiences from TRVLR AI System', 'trvlr' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
			'taxonomies'            => array( 'trvlr_attraction_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-location',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);
		register_post_type( 'trvlr_attraction', $args );

	}

	/**
	 * Register the 'trvlr_attraction_tag' taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Attraction Tags', 'Taxonomy General Name', 'trvlr' ),
			'singular_name'              => _x( 'Attraction Tag', 'Taxonomy Singular Name', 'trvlr' ),
			'menu_name'                  => __( 'Attraction Tags', 'trvlr' ),
			'all_items'                  => __( 'All Tags', 'trvlr' ),
			'parent_item'                => __( 'Parent Tag', 'trvlr' ),
			'parent_item_colon'          => __( 'Parent Tag:', 'trvlr' ),
			'new_item_name'              => __( 'New Tag Name', 'trvlr' ),
			'add_new_item'               => __( 'Add New Tag', 'trvlr' ),
			'edit_item'                  => __( 'Edit Tag', 'trvlr' ),
			'update_item'                => __( 'Update Tag', 'trvlr' ),
			'view_item'                  => __( 'View Tag', 'trvlr' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'trvlr' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'trvlr' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trvlr' ),
			'popular_items'              => __( 'Popular Tags', 'trvlr' ),
			'search_items'               => __( 'Search Tags', 'trvlr' ),
			'not_found'                  => __( 'Not Found', 'trvlr' ),
			'no_terms'                   => __( 'No tags', 'trvlr' ),
			'items_list'                 => __( 'Tags list', 'trvlr' ),
			'items_list_navigation'      => __( 'Tags list navigation', 'trvlr' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'show_in_rest'               => true,
		);
		register_taxonomy( 'trvlr_attraction_tag', array( 'trvlr_attraction' ), $args );

	}

}

