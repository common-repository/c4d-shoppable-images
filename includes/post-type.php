<?php
/**
 * Register a custom post type called "book".
 *
 * @see get_post_type_labels() for label keys.
 */
function c4d_si_post_type_init() {
	$labels = array(
		'name'                  => _x( 'Collections', 'Post type general name', 'c4d-si-domain' ),
		'singular_name'         => _x( 'Collection', 'Post type singular name', 'c4d-si-domain' ),
		'menu_name'             => _x( 'Shoppable Images', 'Admin Menu text', 'c4d-si-domain' ),
		'name_admin_bar'        => _x( 'Collection', 'Add New on Toolbar', 'c4d-si-domain' ),
		'add_new'               => __( 'Add New', 'c4d-si-domain' ),
		'add_new_item'          => __( 'Add New Collection', 'c4d-si-domain' ),
		'new_item'              => __( 'New Collection', 'c4d-si-domain' ),
		'edit_item'             => __( 'Edit Collection', 'c4d-si-domain' ),
		'view_item'             => __( 'View Collection', 'c4d-si-domain' ),
		'all_items'             => __( 'All Collections', 'c4d-si-domain' ),
		'search_items'          => __( 'Search Collections', 'c4d-si-domain' ),
		'parent_item_colon'     => __( 'Parent Collections:', 'c4d-si-domain' ),
		'not_found'             => __( 'No Collections found.', 'c4d-si-domain' ),
		'not_found_in_trash'    => __( 'No Collections found in Trash.', 'c4d-si-domain' ),
		'featured_image'        => _x( 'Collection Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'c4d-si-domain' ),
		'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'c4d-si-domain' ),
		'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'c4d-si-domain' ),
		'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'c4d-si-domain' ),
		'archives'              => _x( 'Collection archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'c4d-si-domain' ),
		'insert_into_item'      => _x( 'Insert into Collection', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'c4d-si-domain' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Collection', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'c4d-si-domain' ),
		'filter_items_list'     => _x( 'Filter Collections list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'c4d-si-domain' ),
		'items_list_navigation' => _x( 'Collections list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'c4d-si-domain' ),
		'items_list'            => _x( 'Collections list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'c4d-si-domain' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'c4d-si-collect' ),
		'capability_type'    => 'product',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => null,
		'supports'           => array( 'title' ),
		'taxonomies'         => array( 'category' ),
	);

	register_post_type( 'c4d-si-collect', $args );
}

add_action( 'init', 'c4d_si_post_type_init' );

/**
 * Register meta box(es).
 */
function c4d_si_register_meta_boxes() {
	add_meta_box( 'meta-box-collection', __( 'Collection', 'c4d-si-domain' ), 'c4d_si_my_display_callback', 'c4d-si-collect', 'advanced', 'high' );
}
add_action( 'add_meta_boxes', 'c4d_si_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function c4d_si_my_display_callback( $post ) {
	$collection = get_post_meta( $post->ID, '_c4d_si_collection', true );
	wp_nonce_field( 'c4d_si_collection_add_nonce', 'c4d_si_collection_add_nonce' );
	echo '<div id="c4d-si-admin-app"></div>';
	echo '<textarea id="c4d-si-collection-value-field" name="collection" style="display: none">' . $collection . '</textarea>';
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function c4d_si_save_meta_box( $post_id ) {
	// Check if our nonce is set.
	if ( ! isset( $_POST['c4d_si_collection_add_nonce'] ) ) {
		return $post_id;
	}

	$nonce = wp_kses_post( $_POST['c4d_si_collection_add_nonce'] );

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'c4d_si_collection_add_nonce' ) ) {
		return $post_id;
	}

	/*
	* If this is an autosave, our form has not been submitted,
	* so we don't want to do anything.
	*/
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// Save logic goes here. Don't forget to include nonce checks!
	$collection = wp_kses_post( $_POST['collection'] );
	update_post_meta( $post_id, '_c4d_si_collection', $collection );
}

add_action( 'save_post', 'c4d_si_save_meta_box' );
