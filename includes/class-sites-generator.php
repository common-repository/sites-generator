<?php
//use AddSitesFormDataValidation;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://test.com
 * @since      1.0.0
 *
 * @package    Sites_Generator
 * @subpackage Sites_Generator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sites_Generator
 * @subpackage Sites_Generator/includes
 * @author     Author <author@site.com>
 */
class Sites_Generator {


    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Sites_Generator_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'SITES_GENERATOR_VERSION' ) ) {
            $this->version = SITES_GENERATOR_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = SITES_GENERATOR_MENU_SLUG;

        $this->load_dependencies();

        $this->loader->add_action( 'admin_menu', $this, 'create_settings_page_admin' );

        /**
         * Add a site using a counter
         */
        $this->loader->add_action( 'confirm', $this, 'custom_action' );
        //      $this->loader->add_action( 'wp_ajax_nopriv_custom_action', $this, 'custom_action' );

        // Add the site with a various page count
        $this->loader->add_action( 'wp_ajax_add_sites', $this, 'wp_ajax_add_sites_function' );
        $this->loader->add_action( 'wp_ajax_nopriv_add_sites', $this, 'wp_ajax_add_sites_function' );

        // Site quick edit
        $this->loader->add_action( 'wp_ajax_quickedit_site', $this, 'wp_ajax_quickedit_site_function' );
        $this->loader->add_action( 'wp_ajax_nopriv_quickedit_site', $this, 'wp_ajax_quickedit_site_function' );

        // Change delete site action
        $this->loader->add_action( 'wp_delete_site', $this, 'wp_site_deletion_action' );

        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Sites_Generator_Loader. Orchestrates the hooks of the plugin.
     * - Sites_Generator_i18n. Defines internationalization functionality.
     * - Sites_Generator_Admin. Defines all hooks for the admin area.
     * - Sites_Generator_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sites-generator-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sites-generator-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sites-generator-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sites-generator-public.php';

        $this->loader = new Sites_Generator_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Sites_Generator_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Sites_Generator_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Sites_Generator_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Sites_Generator_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Sites_Generator_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Adds data from form to database.
     *
     * @param int $id
     */
    public function add_data_to_db( $id ) {
        global $wpdb;
        $site = get_site( $id );

        $wpdb->insert(
            SITES_GENERATOR_DB_NAME,
            array(
                'blog_id' => $id,
                'domain'  => $site->domain,
            )
        );
    }

    /**
     * Looks for a subdomain in the plugin table.
     *
     * @param string $subdomain
     * @return bool
     */
    public function subdomain_exists( $subdomain ) {
        $sites = get_sites();
        foreach ( $sites as $site ) {
            if ( $site->domain === $subdomain ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Looks for a subfolder in the plugin table.
     *
     * @param string $subfolder
     * @return bool
     */
    public function subfolder_exists( $subfolder ) {
        $sites = get_sites();
        foreach ( $sites as $site ) {
            if ( stripos( $site->domain . '/' . $site->path, $subfolder ) !== false ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Action for creating plugin pages.
     */
    public function create_settings_page_admin() {
        add_menu_page(
            __( 'Sites generator', SITES_GENERATOR_MENU_SLUG ),
            __( 'Sites Generator', SITES_GENERATOR_MENU_SLUG ),
            'manage_options',
            SITES_GENERATOR_MENU_SLUG,
            array( $this, 'show_sites_admin_page_view' ),
            ''
        );

        add_submenu_page(
            SITES_GENERATOR_MENU_SLUG,
            'Add sites',
            'Add sites',
            'manage_options',
            'add-sites',
            array( $this, 'add_sites_admin_page_view' ),
		);

    }

    /**
     * Returns view of the main site page.
     */
    public function show_sites_admin_page_view() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/views/sites-generator-render-sites-list-table.php';
        sites_generator_render_sites_list_table();
    }

    /**
     * Returns view of the create sites page.
     */
    public function add_sites_admin_page_view() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/views/sites-generator-render-add-sites-form.php';
        sites_generator_render_add_sites_form();
    }

    /**
     * Removes site.
     *
     * @param object $old_site
     */
    public function wp_site_deletion_action( $old_site ) {
        global $wpdb;
        $wpdb->delete( SITES_GENERATOR_DB_NAME, array( 'blog_id' => $old_site->blog_id ) );
    }

    /**
     * The action gets data from the create sites
     * form and return the result (null or Exception).
     */
    public function wp_ajax_add_sites_function() {
        try {
            if ( empty( $_POST ) || ! wp_verify_nonce( $_POST['unique_code'], 'wp_ajax_add_sites' ) ) {
                throw new Exception( __( 'Sorry, the verification data does not match.', SITES_GENERATOR_MENU_SLUG ) );
            }
            if ( empty( $_POST['sitesData'] ) ) {
                throw new Exception( __( 'Sorry, an unknown error occurred while transferring data.', SITES_GENERATOR_MENU_SLUG ) );
            }

            $sites_data = isset( $_REQUEST['sitesData'] ) ? (array) $_REQUEST['sitesData'] : array();

            if ( is_array( $sites_data ) ) {
                foreach ( $sites_data as $site ) {
                    $site = array_map( 'sanitize_text_field', $site );
                }
                unset($item);
            }

            $errors = array(
                'error'          => false,
                'errors_details' => array(),
            );

            foreach ( $sites_data as $site ) {
                if ( empty( $site['name'] ) || empty( $site['slug'] ) ) {

                    $errors['error'] = true;
                    array_push(
                        $errors['errors_details'],
                        array(
                            'site_id' => $site['id'],
                            'message' => __( 'Site name and slug are required fields.', 'sites_generator' ),
                        )
                    );
                } else {
                    $result = $this->create_site( $site );
                    if ( $result['error'] ) {
                        $errors['error'] = $result['error'];
                        array_push( $errors['errors_details'], $result['errors_details'] );
                    }
                }
            }
            if ( true === $errors['error'] ) {
                throw new Exception( __( 'There were errors while creating sites.', 'sites_generator' ) );
            }

            $response = $this->generate_response( false, '', null );
            exit( wp_json_encode( $response ) );
        } catch ( Exception $e ) {
            $response = $this->generate_response( true, $e->getMessage(), $errors );
            exit( wp_json_encode( $response ) );
        }
    }

    public function wp_ajax_quickedit_site_function() {
        try {
            if ( empty( $_POST['siteData'] ) ) {
                throw new Exception( __( 'Sorry, an unknown error occurred while transferring data.', 'sites_generator' ) );
            }
            global $wpdb;

            $blog_data        = sanitize_text_field($_POST['siteData']);
            $blog_id          = sanitize_text_field($blog_data['siteId']);
            $blog_name        = sanitize_text_field($blog_data['siteName']);
            $blog_slug        = sanitize_text_field($blog_data['siteSlug']);
            $table_name       = SITES_GENERATOR_DB_NAME;
            $site_general_url = DOMAIN_CURRENT_SITE;

            $errors = array(
                'error'          => false,
                'errors_details' => array(),
            );

            switch_to_blog( $blog_id );

            $new_site = get_blog_details();
            $protocol = is_ssl() ? 'https://' : 'http://';

            if ( SUBDOMAIN_INSTALL ) {
                $url  = "{$blog_slug}.{$site_general_url}";
                $data = array( 'domain' => $url );
                wp_update_site( $blog_id, $data );
                $wpdb->update( $table_name, array( 'domain' => $url ), array( 'blog_id' => $blog_id ) );
            } else {
                $url  = "{$site_general_url}/{$blog_slug}/";
                $data = array( 'path' => "/{$blog_slug}/" );
                wp_update_site( $blog_id, $data );
            }
            $new_site->blogname = $blog_name;
            $blog_full_url      = $protocol . $url;

            if ( SUBDOMAIN_INSTALL ) {
                update_option( 'domain', $blog_full_url );
            }

            update_option( 'home', $blog_full_url );
            update_option( 'siteurl', $blog_full_url );
            update_option( 'blogname', $blog_name );

            $dataa = get_blog_details();
            wpmu_update_blogs_date();

            restore_current_blog();

            $response = $this->generate_response(
                false,
                '',
                array(
                    'data' => $dataa,
                    'slug' => $blog_slug,
                    'url'  => $url,
                )
            );
            exit( wp_json_encode( $response ) );
        } catch ( Exception $e ) {
            $response = $this->generate_response( true, $e->getMessage(), $errors );
            exit( wp_json_encode( $response ) );
        }
    }

    /**
     * @param bool $error
     * @param string $error_message
     * @param $data
     * @return array
     */
    private function generate_response( $error, $error_message, $data ) {
        return array(
            'error'         => $error,
            'error_message' => $error_message,
            'data'          => $data,
        );
    }

    /**
     * Creates a page.
     *
     * @param int|string $site_id
     * @param array $page
     */
    public function create_page( $site_id, $page ) {
        switch_to_blog( $site_id );

        $default_page = array(
            'post_title'   => $page['name'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_name'    => $page['slug'],
        );
        $new_page_id  = wp_insert_post( $default_page );

        update_post_meta( $new_page_id, '_wp_page_template', $page['template'] );
    }

    /**
     * Creates a site.
     *
     * @param array $site
     * @return array
     */
    private function create_site( $site ) {
        if ( ! function_exists( 'wpmu_create_blog' ) ) {
            require_once ABSPATH . WPINC . '/ms-functions.php';
        }

        $general_site_url_with_protocol = site_url( get_main_site_id() );
        $general_site_url               = trim( parse_url( $general_site_url_with_protocol )['host'], '\\' );
        $errors                         = array(
            'error'          => false,
            'errors_details' => array(),
        );

        if ( defined( 'SUBDOMAIN_INSTALL' ) ) {
            if ( SUBDOMAIN_INSTALL ) {
                $subdomain = $site['slug'] . '.' . $general_site_url;
                $path      = '/';
                if ( $this->subdomain_exists( $subdomain ) ) {
                    $message         = $subdomain . __( ' already exists', 'sites-generator' );
                    $errors['error'] = true;
                    array_push(
                        $errors['errors_details'],
                        array(
                            'site_id'       => $site['id'],
                            'error_message' => $message,
                        )
                    );
                } else {
                    $new_site_id = wpmu_create_blog( $subdomain, $path, $site['name'], 1 );
                    $this->add_data_to_db( $new_site_id );
                    switch_to_blog( $new_site_id );
                    foreach ( $site['pages'] as $page ) {
                        $this->create_page( $new_site_id, $page );
                    }
                    restore_current_blog();
                }
            } else {
                $subfolder      = $site['name'];
                $subfolder_slug = $site['slug'];
                if ( $this->subfolder_exists( $subfolder ) == true ) {
                    $message         = $subfolder . __( ' already exists', 'sites-generator' );
                    $errors['error'] = true;
                    array_push(
                        $errors['errors_details'],
                        array(
                            'site_id'       => $site['id'],
                            'error_message' => $message,
                        )
                    );
                } else {
                    //                  $new_site_id = wpmu_create_blog( $general_site_url.'/'.$subfolder_slug, '/', $subfolder, 1 );
                    $new_site_id = wpmu_create_blog( $general_site_url, $subfolder_slug, $subfolder, 1 );
                    $this->add_data_to_db( $new_site_id );
                    switch_to_blog( $new_site_id );
                    foreach ( $site['pages'] as $page ) {
                        $this->create_page( $new_site_id, $page );
                    }
                    restore_current_blog();
                }
            }
        }

        return $errors;
    }
}