<?php
add_action('wp_enqueue_scripts', 'c4d_si_load_scripts_site');
add_action('admin_enqueue_scripts', 'c4d_si_load_scripts_admin');

function c4d_si_load_scripts_site()
{

	wp_enqueue_script(
		'c4d-si-app-js',
		C4DWOOSI_PLUGIN_URI . '/assets/front.js',
		array('jquery'),
		false,
		true
	);

	// Localize the script with new data
	$options = array(
		'clear'    => __('Clear', 'c4d-si-domain'),
		'ajax_url' => admin_url('admin-ajax.php'),
		'app_url'  => C4DWOOSI_APP_URI,

	);
	wp_localize_script('c4d-si-app-js', 'c4dShoppable', $options);
}

function c4d_si_load_scripts_admin($hook)
{
	global $post_type;
	if ('c4d-si-collect' === $post_type) {
		//js
		wp_enqueue_media();

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');

		wp_enqueue_script('c4d-si-admin-js', C4DWOOSI_PLUGIN_URI . '/assets/admin.js');

		// Localize the script with new data
		$options = array(
			'app_url'               => C4DWOOSI_APP_URI,
			'search_products_nonce' => wp_create_nonce('search-products'),
		);

		wp_localize_script('c4d-si-admin-js', 'c4dShoppable', $options);
	}
}
