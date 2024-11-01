<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://test.com
 * @since      1.0.0
 *
 * @package    Sites_Generator
 * @subpackage Sites_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sites_Generator
 * @subpackage Sites_Generator/admin
 * @author     Author <author@site.com>
 */
class Sites_Generator_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sites_Generator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sites_Generator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name . '_styles_css', plugin_dir_url( __FILE__ ) . 'dist/main.min.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sites_Generator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sites_Generator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-tabs' );

        wp_enqueue_script( $this->plugin_name . '_knockoutjs', plugin_dir_url( __FILE__ ) . 'js/knockout/knockout.min.js', array(), '3.5.1', true );

        $all_templates                  = get_page_templates();
        $request_url                    = admin_url( 'admin-ajax.php' );
        $default_tpl_name               = __( 'Default', SITES_GENERATOR_MENU_SLUG );
        $general_site_url_with_protocol = site_url( get_main_site_id() );
        $general_site_url               = trim( parse_url( $general_site_url_with_protocol )['host'], '\\' );
        wp_enqueue_script( $this->plugin_name . '_main_js', plugin_dir_url( __FILE__ ) . 'dist/main.min.js', array(), '1.0.0', true );
        wp_localize_script(
            $this->plugin_name . '_main_js',
            'Storage',
            array(
                'allSitePagesTemplates' => $all_templates,
                'requestUrl'            => $request_url,
                'defaultTemplateName'   => $default_tpl_name,
                'generalSiteUrl'      => $general_site_url,
            )
        );

    }
}
