<?php

class Axeptio_Admin
{

    protected $plugin_file;

    protected $tools;

    public function __construct(Axeptio_Admin_Tools $tools)
    {
        $this->plugin_file = plugin_basename(AXEPTIO_PLUGIN_FILE);
        $this->tools = $tools;
        $this->load_translations();
    }

    /**
     * Initialize admin
     */
    public function initialize()
    {
        // Register settings
        register_setting(
            'axeptio_settings',
            'axeptio',
            array(
                $this,
                'save_general_settings',
            )
        );
    }

    /**
     * Validates the General settings
     * @param array $settings
     * @return array
     */
    public function save_general_settings(array $settings)
    {

        $current = axeptio_get_options();
        // merge with current settings to allow passing partial arrays to this method
        $settings = array_merge($current, $settings);

        $settings['client_id'] = sanitize_text_field($settings['client_id']);

        $opts = axeptio_get_options();
        $client_id = $opts['client_id'];

        //if we change client id, re init consents
        if ($settings['client_id'] != $client_id) {
            AxeptioConfiguration::reset();
        }

        return $settings;
    }

    /**
     * Load assets
     */
    public function enqueue_assets()
    {
        wp_enqueue_style(
            'axeptio-admin',
            axeptio_plugin_url('assets/css/admin.css'),
            array(),
            AXEPTIO_VERSION,
            'all'
        );

        wp_enqueue_script(
            'axeptio-admin',
            axeptio_plugin_url('assets/js/script-admin.js'),
            array('jquery', 'jquery-ui-tabs'),
            AXEPTIO_VERSION,
            true
        );

        //ajax admin
        $jsDatas = array(
            'ajax_url' => 'admin-ajax.php',
        );

        wp_localize_script('axeptio-admin', 'admin_php_vars', $jsDatas);


        wp_enqueue_style(
            'jquery-ui',
            axeptio_plugin_url('assets/js/jquery-ui/themes/smoothness/jquery-ui.css'),
            array(),
            AXEPTIO_VERSION,
            'all'
        );
    }

    /**
     * Registers all hooks
     */
    public function add_hooks()
    {


        // Actions used globally throughout WP Admin
        add_action(
            'admin_menu',
            array(
                $this,
                'build_menu',
            )
        );
        add_action(
            'admin_init',
            array(
                $this,
                'initialize',
            )
        );

        add_action(
            'admin_enqueue_scripts',
            array($this, 'enqueue_assets')
        );

        //check if on admin plugin page
        if (!$this->tools->on_plugin_page()) {
            return false;
        }
    }

    /**
     * Register the setting pages and their menu items
     */
    public function build_menu()
    {
        $required_cap = $this->tools->get_required_capability();

        $menu_items = array(
            'general' => array(
                'title' => __('Axeptio API Settings', 'axeptio'),
                'text' => __('Axeptio', 'axeptio'),
                'slug' => '',
                'callback' => array(
                    $this,
                    'show_generals_setting_page',
                ),
                'position' => 0,
            ),
        );

        /**
         * Filters the menu items to appear under the main menu item.
         *
         * To add your own item, add an associative array in the following format.
         *
         * $menu_items[] = array(
         *     'title' => 'Page title',
         *     'text'  => 'Menu text',
         *     'slug' => 'Page slug',
         *     'callback' => 'my_page_function',
         *     'position' => 50
         * );
         *
         * @param array $menu_items
         * @since 3.0
         */
        $menu_items = (array)apply_filters('axeptio_admin_menu_items', $menu_items);

        // add top menu item
        add_menu_page(
            'Axeptio',
            'Axeptio',
            $required_cap,
            'axeptio',
            array(
                $this,
                'show_generals_setting_page',
            ),
            AXEPTIO_PLUGIN_URL.'assets/img/icon.png',
            '99'
        );
    }

    /**
     * Add menu entry to admin
     * @param array $item
     */
    public function add_menu_item(array $item)
    {
        // Generate menu slug
        $slug = 'axeptio';
        if (!empty($item['slug'])) {
            $slug .= '-'.$item['slug'];
        }

        // provide some defaults
        $parent_slug = !empty($item['parent_slug']) ? $item['parent_slug'] : 'axeptio';
        $capability = !empty($item['capability']) ? $item['capability'] : $this->tools->get_required_capability();

        // register page
        $hook = add_submenu_page(
            $parent_slug,
            $item['title'].' - Axeptio for WordPress',
            $item['text'],
            $capability,
            $slug,
            $item['callback']
        );

        // register callback for loading this page, if given
        if (array_key_exists('load_callback', $item)) {
            add_action('load-'.$hook, $item['load_callback']);
        }
    }

    /**
     * Get all axeptio settings for admin
     */
    public function show_generals_setting_page()
    {
        $opts = axeptio_get_options();
        $client_id = $opts['client_id'];

        $configurationsBase = AxeptioAPIClient::getAllCollections($client_id, true);
        //$configurations = AxeptioAPIClient::getSortCollectionByLang($configurationsBase);

        //update or add configuration if needed
        foreach ($configurationsBase->documents as $configuration) {
            $axeptioConfiguration = AxeptioConfiguration::get($configuration->identifier);
            if (!$axeptioConfiguration) {

                $datas = array(
                    'name' => ($configuration->name) ? $configuration->name : $configuration->identifier,
                    'type' => 'doc',
                    'identifier' => $configuration->identifier,
                    'is_newsletter' => 0,
                    'is_partner' => 0,
                    'lang' => $configuration->language,
                );
                AxeptioConfiguration::createFromApi($datas);

            } else {
                AxeptioConfiguration::updateFields(
                    array('name' => $configuration->name),
                    array('name' => $axeptioConfiguration[0]->name)
                );
            }
        }

        foreach ($configurationsBase->personalDataUsages as $configuration) {
            $axeptioConfiguration = AxeptioConfiguration::get($configuration->identifier);
            if (!$axeptioConfiguration) {

                $datas = array(
                    'name' => ($configuration->name) ? $configuration->name : $configuration->identifier,
                    'type' => 'pdu',
                    'identifier' => $configuration->identifier,
                    'is_newsletter' => 0,
                    'is_partner' => 0,
                    'lang' => $configuration->language,
                );
                AxeptioConfiguration::createFromApi($datas);

            } else {
                AxeptioConfiguration::updateFields(
                    array('name' => $configuration->name),
                    array('name' => $axeptioConfiguration[0]->name)
                );
            }
        }

        $configurations = AxeptioConfiguration::getSortByLang();


        require AXEPTIO_PLUGIN_DIR.'includes/views/general-settings.php';
    }


    //    /**
    //     * Check if on the selected page
    //     *
    //     * @param string $page
    //     * @return bool
    //     */
    //    public function on_plugin_page($page = null)
    //    {
    //        // any settings page
    //        if (is_null($page)) {
    //            return isset($_GET['page']) && strpos($_GET['page'], 'axeptio') === 0;
    //        }
    //
    //        // specific page
    //        return $this->get_plugin_page() === $page;
    //    }

    /**
     * Load plugin translations
     */
    private function load_translations()
    {
        load_plugin_textdomain('axeptio', false, dirname($this->plugin_file).'/languages');
    }

}