<?php
/*
Plugin Name: 20i Connect
Plugin URI: https://redboxweb.co.uk
Description: Connect site to 20i hosting
Version: 0.0.1
Author: George Birchall
Text Domain: twentyi
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('TwentyI')) :

    class TwentyI {
        /**
         * Holds the values to be used in the fields callbacks
         */
        protected $options;
        function __construct() {
        }


        /**
         * Start up
         */
        public function initialize() {
            //don't do anything if not dladmin/rbadmin
            if (!get_current_user_id() === 1) {
                return;
            }
            // Set class property
            $this->options = get_option('twentyi_options');
            if ($this->options['account_id']) {
                add_action('admin_bar_menu', [$this, 'register_bar_menu'], 100);
            }
            add_action('admin_menu', array($this, 'add_plugin_page'));
            add_action('admin_init', array($this, 'page_init'));
        }


        function register_bar_menu($admin_bar) {
            //register the top bar menu
            $admin_bar->add_menu([
                'id'    => 'twentyi',
                'title' => 'Go to 20i',
                'href'  => 'https://my.20i.com/services/' . $this->options['account_id'] . '/service-overview',
                'meta'  => [
                    'title' => __('Go to 20i'),
                    'target' => __('_blank')
                ],
            ]);
        }
        /**
         * Add options page
         */
        public function add_plugin_page() {
            // This page will be under "Settings"
            add_options_page(
                '20i Settings',
                '20i Settings',
                'manage_options',
                'twentyi-settings',
                array($this, 'create_admin_page')
            );
        }

        /**
         * Options page callback
         */
        public function create_admin_page() {
            // // Set class property
            // $this->options = get_option('twentyi_options');
            // wp_die(var_dump($this->options));
?>
            <div class="wrap">
                <h1>My Settings</h1>
                <form method="post" action="options.php">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields('twentyi_settings_group');
                    do_settings_sections('twentyi-settings');
                    submit_button();
                    ?>
                </form>
            </div>
<?php
        }

        /**
         * Register and add settings
         */
        public function page_init() {
            register_setting(
                'twentyi_settings_group', // Option group
                'twentyi_options', // Option name
                array($this, 'sanitize') // Sanitize
            );

            add_settings_section(
                'setting_section_id', // ID
                'My Custom Settings', // Title
                array($this, 'print_section_info'), // Callback
                'twentyi-settings' // Page
            );


            add_settings_field(
                'account_id',
                'Account ID',
                array($this, 'title_callback'),
                'twentyi-settings',
                'setting_section_id'
            );
        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize($input) {
            $new_input = array();
            if (isset($input['account_id']))
                $new_input['account_id'] = sanitize_text_field($input['account_id']);

            return $new_input;
        }

        /** 
         * Print the Section text
         */
        public function print_section_info() {
            print 'Enter your settings below:';
        }

        /** 
         * Get the settings option array and print one of its values
         */
        public function title_callback() {
            printf(
                '<input type="text" id="account_id" name="twentyi_options[account_id]" value="%s" />',
                isset($this->options['account_id']) ? esc_attr($this->options['account_id']) : ''
            );
            echo '<p>Example: https://my.20i.com/services/[THIS-SECTION-HERE]/service-overview</p>';
        }
    }
    $twentyi = new TwentyI;
    $twentyi->initialize();

endif; // class_exists check
