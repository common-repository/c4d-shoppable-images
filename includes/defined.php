<?php
define( 'C4DWOOSI_PLUGIN_URI', plugins_url( '', __DIR__ ) );
if (defined('C4D_TEST_LOCAL')) {
    define( 'C4DWOOSI_APP_URI', 'http://localhost:5173/src/' );
} else {
    define( 'C4DWOOSI_APP_URI' , plugin_dir_url(dirname(__FILE__)). 'app/dist');
}

