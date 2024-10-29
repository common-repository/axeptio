<?php
/*
Plugin Name: Axeptio
Plugin URI: https://wordpress.org/plugins/axeptio/
Description: Axeptio consent integration for Wordpress
Author: Weggs
Author URI: http://weggs.fr
Text Domain: axeptio
Domain Path: /languages/
Version: 1.0.3
*/

define('AXEPTIO_VERSION', '1.0.3');
define('AXEPTIO_MINIMUM_WP_VERSION', '4.0');
define('AXEPTIO_PLUGIN', __FILE__);
define('AXEPTIO_PLUGIN_FILE', __FILE__);
define('AXEPTIO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AXEPTIO_PLUGIN_URL', plugins_url('/', __FILE__));
define('AXEPTIO_API_URL', 'https://api.axept.io/v1');
define('AXEPTIO_PLATFORM_URL', 'https://platform.axept.io');
//define( 'AKISMET_DELETE_LIMIT', 100000 );

require_once(AXEPTIO_PLUGIN_DIR.'includes/api/AxeptioAPIClient.php');
require_once(AXEPTIO_PLUGIN_DIR.'includes/functions.php');
require_once(AXEPTIO_PLUGIN_DIR.'includes/admin/class-admin-tools.php');
require_once(AXEPTIO_PLUGIN_DIR.'includes/admin/class-admin.php');
require_once(AXEPTIO_PLUGIN_DIR.'shortcode/shortcode.php');
require_once(AXEPTIO_PLUGIN_DIR.'includes/axeptio/axeptio-configuration.php');
require_once(AXEPTIO_PLUGIN_DIR.'includes/axeptio/axeptio-user.php');

// Initialize admin section of plugin
if (is_admin()) {
    add_action('plugins_loaded', 'axeptio_load_plugin', 8);
}

//prepare Axeptio consents
add_action('init', 'prepare_consent');

//axeptio_shortcode();
add_shortcode('axeptio', 'axeptio_shortcode');

//create db structure
register_activation_hook(__FILE__, 'axeptio_install');

//active contact form 7 shortcode
add_action('init', 'activeCf7');

//add_action('wp_print_scripts', 'baw_enqueue_my_script');
add_action('wp_footer', 'baw_enqueue_my_script');



