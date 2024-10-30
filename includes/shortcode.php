<?php

add_shortcode( 'c4d-si-collection', 'c4d_si_shortcode_collections' );

function c4d_si_shortcode_collections( $atts ) {
	$atts = shortcode_atts(
		array(
			'ids'        => '',
			'categories' => '',
			'layout'     => 'grid', // grid, slider, mansory,
			'column'     => 4,
			'gap'        => 10,
			'load_more'  => 3,
		),
		$atts
	);

	$html = '<div class="c4d-si-wrap"
				data-settings="' . htmlspecialchars( json_encode( $atts ) ) . '">';

	// get by post id
	$ids = explode( ',', $atts['ids'] );

	if ( $atts['ids'] && count( $ids ) ) {
		foreach ( $ids as $id ) {
			if ( $id ) {
				$html .= c4d_si_get_collection_html( $id );
			}
		}
	}

	// get by categories
	$categories = explode( ',', $atts['categories'] );
	if ( $atts['categories'] && count( $categories ) ) {
		$post_ids = c4d_si_get_collection_by_categories( $categories );
		foreach ( $post_ids as $id ) {
			$html .= c4d_si_get_collection_html( $id );
		}
	}

	$html .= '</div>';

	return $html;
}

function c4d_si_get_collection_by_categories( $categories = array() ) {
	$args  = array(
		'post_type' => 'c4d-si-collect',

		'tax_query' => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $categories,
			),
		),
	);
	$query = new WP_Query( $args );

	$ids = array();

	foreach ( $query->posts as $post ) {
		$ids[] = $post->ID;
	}

	wp_reset_query();

	return $ids;
}

function c4d_si_get_collection_html( $id ) {
	$id         = trim( $id );
	$collection = get_post_meta(
		$id,
		'_c4d_si_collection',
		true
	);
	$html       = '<div id = "c4d-si-collection-' . $id . '" class="c4d-si-collection" style="display: none;">' . htmlspecialchars( $collection ) . ' </div>';
	return $html;
}

// shortcode guide
add_action( 'add_meta_boxes', 'c4d_woo_si_add_meta_boxes', 30 );

function c4d_woo_si_add_meta_boxes( $post_type ) {
	add_meta_box( 'meta-box-shortcode', __( 'Shortcode', 'c4d-si-domain' ), 'c4d_woo_si_show_shortcode_callback', 'c4d-si-collect', 'side', 'high' );
}

function c4d_woo_si_show_shortcode_callback( $post ) {
	echo ' <p><strong> ' . __( 'Your shortcode of this collection', 'c4d-si-domain' ) . ': </strong></p>';
	echo ' <p><pre>[c4d-si-collections ids="' . $post->ID . '"] </pre></p>';
	echo ' <p>[c4d-si-collections categories="category-name-1,category-name-2, category-name-3"]</p>';
	include_once 'guide.php';
}

// end shortcode guide

// add shortcode column in list page
add_filter( 'manage_c4d-si-collect_posts_columns', 'c4d_woo_si_columns_head' );
add_action( 'manage_c4d-si-collect_posts_custom_column', 'c4d_woo_si_columns_content', 10, 2 );

function c4d_woo_si_columns_head( $defaults ) {
	$defaults['shortcode'] = __( 'Shortcode', 'c4d - si - domain' );
	return $defaults;
}

function c4d_woo_si_columns_content( $column_name, $post_id ) {
	if ( 'shortcode' === $column_name ) {
		echo '[c4d-si-collection id="' . $post_id. '"]';
	}
}
	// add shortcode column in list page
