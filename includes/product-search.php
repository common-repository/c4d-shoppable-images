<?php
add_action( 'wp_ajax_c4d_si_json_search_products', 'c4d_si_json_search_products' );

function c4d_si_json_search_products() {
	$include_variations = true;
	check_ajax_referer( 'search-products', 'security' );

	if ( empty( $term ) && isset( $_GET['term'] ) ) {
		$term = (string) wc_clean( wp_unslash( $_GET['term'] ) );
	}

	if ( empty( $term ) ) {
		wp_die();
	}

	if ( ! empty( $_GET['limit'] ) ) {
		$limit = absint( $_GET['limit'] );
	} else {
		$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
	}

	$include_ids = ! empty( $_GET['include'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['include'] ) ) : array();
	$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : array();

	$data_store = WC_Data_Store::load( 'product' );
	$ids        = $data_store->search_products( $term, '', (bool) $include_variations, false, $limit, $include_ids, $exclude_ids );

	$product_objects = array_filter( array_map( 'wc_get_product', $ids ), 'wc_products_array_filter_readable' );
	$products        = array();

	foreach ( $product_objects as $product_object ) {
		$formatted_name = $product_object->get_formatted_name();
		$id             = $product_object->get_id();
		$image          = wp_get_attachment_image_src( $product_object->get_image_id(), 'full', false );
		$image          = is_array( $image ) ? $image[0] : '';
		$products[]     = array(
			'id'    => $id,
			'name'  => rawurldecode( $formatted_name ),
			'image' => $image,
			'link'  => get_permalink( $id ),
		);
	}

	wp_send_json( apply_filters( 'woocommerce_json_search_found_products', $products ) );
}

add_action( 'wp_ajax_c4d_si_json_search_product_by_ids', 'c4d_si_json_search_product_by_ids' );
add_action( 'wp_ajax_nopriv_c4d_si_json_search_product_by_ids', 'c4d_si_json_search_product_by_ids' );

function c4d_si_json_search_product_by_ids() {
	$include_variations = true;

	if ( empty( $pids ) && isset( $_GET['pids'] ) ) {
		$pids = wc_clean( wp_unslash( $_GET['pids'] ) );
	}

	if ( empty( $pids ) ) {
		wp_die();
	}

	if ( ! empty( $_GET['limit'] ) ) {
		$limit = absint( $_GET['limit'] );
	} else {
		$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
	}

	$product_objects = array_map( 'wc_get_product', $pids );
	$products        = array();

	foreach ( $product_objects as $product ) {
		$formatted_name = $product->get_formatted_name();
		$id             = $product->get_id();
		$image          = wp_get_attachment_image_src( $product->get_image_id(), 'full', false );
		$image          = is_array( $image ) ? $image[0] : '';
		$args           = c4d_si_create_add_to_cart_args( $product );
		$products[]     = array(
			'id'               => $id,
			'name'             => rawurldecode( $formatted_name ),
			'short_desc'       => $product->get_short_description(),
			'desc'             => $product->get_description(),
			'image'            => $image,
			'link'             => get_permalink( $id ),
			'regular_price'    => $product->get_regular_price(),
			'sale_price'       => $product->get_sale_price(),
			'price'            => $product->get_price(),
			'price_html'       => $product->get_price_html(),
			'add_to_cart_url'  => esc_url( $product->add_to_cart_url() ),
			'add_to_cart_html' => sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() )
			),
			'rating_html'      => wc_get_rating_html( $product->get_average_rating() ),
		);
	}

	wp_send_json( apply_filters( 'woocommerce_json_search_found_products', $products ) );
}

function c4d_si_create_add_to_cart_args( $product ) {
	if ( $product ) {
		$defaults = array(
			'quantity'   => 1,
			'class'      => implode(
				' ',
				array_filter(
					array(
						'button',
						'product_type_' . $product->get_type(),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
					)
				)
			),
			'attributes' => array(
				'data-product_id'  => $product->get_id(),
				'data-product_sku' => $product->get_sku(),
				'aria-label'       => $product->add_to_cart_description(),
				'rel'              => 'nofollow',
			),
		);

		$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

		if ( isset( $args['attributes']['aria-label'] ) ) {
			$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
		}
		return $args;
	}
	return array();
}
