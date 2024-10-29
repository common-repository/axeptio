<?php

/**
 * Database creation
 */
function axeptio_install()
{
    global $wpdb;

    $table_name = $wpdb->prefix.'axeptio_configuration';
    $table_name_user = $wpdb->prefix.'axeptio_user';
    $table_name_user_configuration = $wpdb->prefix.'axeptio_user_configuration';

    $charset_collate = $wpdb->get_charset_collate();

    $sqls = array();
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $sqls[] = "CREATE TABLE $table_name (
		id_axeptio_configuration int(11) NOT NULL AUTO_INCREMENT,
		type varchar(128) NOT NULL,
		identifier varchar(128) NOT NULL,
		name varchar(128) NOT NULL,
		lang varchar(55)  NOT NULL,
		is_newsletter boolean,
		is_partner boolean,
		PRIMARY KEY  (id_axeptio_configuration)
	) $charset_collate;";


    $sqls[] = "CREATE TABLE $table_name_user (
		id_axeptio_user int(11) NOT NULL AUTO_INCREMENT,
		id_user int(11) NOT NULL,
		token varchar(128) NOT NULL,
		PRIMARY KEY  (id_axeptio_user)
	) $charset_collate;";
    
    $sqls[] = "CREATE TABLE $table_name_user_configuration (
		id_axeptio_user_configuration int(11) NOT NULL AUTO_INCREMENT,
		id_axeptio_user int(11) NOT NULL,
		id_axeptio_configuration int(11) NOT NULL,
		checked boolean,
		PRIMARY KEY (id_axeptio_user_configuration)
	) $charset_collate;";


    foreach ($sqls as $sql) {
        dbDelta($sql);
    }

    add_option('axeptio_db_version', AXEPTIO_VERSION);
}

/**
 * Get general settings
 * @return mixed|void
 */
function axeptio_get_options()
{
    static $options;

    if (!$options) {
        $defaults = require AXEPTIO_PLUGIN_DIR.'config/default-settings.php';
        $options = (array)get_option('axeptio', array());
        $options = array_merge($defaults, $options);
    }

    /**
     * Filters the Axeptio for WordPress settings (general).
     *
     * @param array $options
     */
    return apply_filters('axeptio_settings', $options);
}

/**
 * Generate plugin url
 * @param string $path
 * @return string
 */
function axeptio_plugin_url($path = '')
{
    $url = plugins_url($path, AXEPTIO_PLUGIN);

    if (is_ssl() && 'http:' == substr($url, 0, 5)) {
        $url = 'https:'.substr($url, 5);
    }

    return $url;
}

/**
 * Prepare Axeptio consent
 */
function prepare_consent()
{
    if (is_user_logged_in()) {
        $token = wp_get_session_token();
    }

    wp_enqueue_script(
        'axeptio',
        axeptio_plugin_url('assets/js/script.js'),
        array('jquery'),
        AXEPTIO_VERSION,
        true
    );

    wp_enqueue_script(
        'axeptio-embed',
        'https://js.axept.io/embed.js',
        array(),
        AXEPTIO_VERSION,
        true
    );

    $opts = axeptio_get_options();
    $client_id = $opts['client_id'];

    //check if user is logged and already stored in axeptio users with token
    //create user if not
    $token = bin2hex(random_bytes(20));
    if (is_user_logged_in()) {
        //check if in axeptio users
        $user = AxeptioUser::getByWpId(get_current_user_id());

        if (!$user) {
            global $wpdb;
            $table_name = $wpdb->prefix.'axeptio_user';
            $wpdb->insert(
                $table_name,
                array(
                    'id_user' => get_current_user_id(),
                    'token' => $token,
                )
            );
        } else {
            $token = $user[0]->token;
        }
    }

    $jsDatas = array(
        'client_id' => $client_id,
        'api_url' => AXEPTIO_API_URL,
        'platform_url' => AXEPTIO_PLATFORM_URL,
        'token' => $token,
    );

    wp_localize_script('axeptio', 'php_vars', $jsDatas);

}

/**
 * Activate Shortcode Execution for Contact Form 7
 */
function activeCf7()
{
    //if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
    add_filter('wpcf7_form_elements', 'do_shortcode');
    //}
}

/**Load plugin in WP-Admin
 */
function axeptio_load_plugin()
{
    $admin_tools = new Axeptio_Admin_Tools();
    $admin = new Axeptio_Admin($admin_tools);
    $admin->add_hooks();
}

/**
 * Load scripts
 */
function baw_enqueue_my_script()
{

    global $post;
    if (!$post) {
        return;
    }
    $matches = array();
    $pattern = get_shortcode_regex();
    preg_match_all('/'.$pattern.'/s', $post->post_content, $matches);
    foreach ($matches[2] as $value) {
        if ($value == 'axeptio') {


//            wp_enqueue_script(
//                'axeptio-embed',
//                'https://js.axept.io/embed.js',
//                array(),
//                AXEPTIO_VERSION,
//                true
//            );
//
//            wp_enqueue_script(
//                'axeptio',
//                axeptio_plugin_url('assets/js/script.js'),
//                array('jquery'),
//                AXEPTIO_VERSION,
//                true
//            );
            break;
        }
    }
}


