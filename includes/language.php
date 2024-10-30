<?php
add_action( 'plugins_loaded', 'c4d_si_load_textdomain' );

function c4d_si_load_textdomain() {
	load_plugin_textdomain( 'c4d-si-domain', false, dirname( plugin_basename( __FILE__ ), 2 ) . '/languages' );
}
