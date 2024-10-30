<?php
/*
Plugin Name: C4D Shoppable Images
Plugin URI: http://coffee4dev.com/woocommerce-shoppable-images/
Description: Increases traffic through your website by creating shoppable images: images containing different points which link to various products.
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-shoppable
Domain Path: /languages
Version: 2.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! in_array(
	'woocommerce/woocommerce.php',
	get_option( 'active_plugins' )
) ) {
	return;
}

require_once __DIR__ . '/includes/defined.php';
require_once __DIR__ . '/includes/load-script.php';
require_once __DIR__ . '/includes/language.php';
require_once __DIR__ . '/includes/post-type.php';
require_once __DIR__ . '/includes/product-search.php';
require_once __DIR__ . '/includes/shortcode.php';
