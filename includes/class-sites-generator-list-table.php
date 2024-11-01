<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Sites_Generator_List_Table extends WP_List_Table {

    public function __construct() {
        global $status, $page;

        parent::__construct(
            array(
                'singular' => 'site',
                'plural'   => 'sites',
                'ajax'     => true,
            )
        );

    }

    /**
     * @param object $item
     * @param string $column_name
     * @return mixed|string|true
     */
    protected function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'users':
            case 'title':
            case 'url':
            case 'slug':
            case 'pages':
            case 'last_updated':
                return $item[ $column_name ];
            default:
                return print_r( $item, true );
        }
    }
    /**
     * Generates content for a single row of the table.
     *
     * @since 3.1.0
     *
     * @param object $item The current item
     */
    public function single_row( $item ) {
        echo '<tr id="site-' . $item['ID'] . '">';
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /**
     * The function forms the column title
     *
     * @param object $item
     * @return string
     */
    public function column_title( $item ) {
        $edit_page_url = $this->get_edit_site_page_url();
        $actions = array(

            'edit'   => sprintf(
                '<a href="%s?id=%s">' . __( 'Edit', SITES_GENERATOR_MENU_SLUG ) . '</a>',
                $edit_page_url,
                $item['ID']
            ),
            'delete' => sprintf(
                '<a href="?page=%s&action=%s&id=%s">' . __( 'Delete', SITES_GENERATOR_MENU_SLUG ) . '</a>',
                sanitize_text_field( $_REQUEST['page'] ),
                'delete',
                $item['ID']
            ),
            'quick_edit' => sprintf( '<a href="#" class="quick-edit-btn">' . __( 'Quick Edit', SITES_GENERATOR_MENU_SLUG ) . '</a>' ),
        );

        return sprintf(
            '<span class="site-title">%1$s</span> <span style="color:silver">(id:<span id="site-id">%2$s</span>)</span>%3$s',
            $item['title'],
            $item['ID'],
            $this->row_actions( $actions )
		);
    }

    /**
     * @param object $item
     * @return string
     */
    protected function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['ID']
        );
    }

    /**
     * Gets a list of columns.
     *
     * The format is:
     * - `'internal-name' => 'Title'`
     *
     * @return array
     */
    public function get_columns() {
        return array(
            'cb'           => '<input type="checkbox" />',
            'title'        => __( 'Name', SITES_GENERATOR_MENU_SLUG ),
            'url'          => __( 'URL', SITES_GENERATOR_MENU_SLUG ),
            'slug'         => __( 'Slug', SITES_GENERATOR_MENU_SLUG ),
            'pages'        => __( 'Pages', SITES_GENERATOR_MENU_SLUG ),
            'last_updated' => __( 'Last Updated', SITES_GENERATOR_MENU_SLUG ),
            'users'        => __( 'Users', SITES_GENERATOR_MENU_SLUG ),
        );
    }

    /**
     * Gets a list of sortable columns.
     *
     * The format is:
     * - `'internal-name' => 'orderby'`
     * - `'internal-name' => array( 'orderby', 'asc' )` - The second element sets the initial sorting order.
     * - `'internal-name' => array( 'orderby', true )`  - The second element makes the initial order descending.
     *
     * @return array
     */
    protected function get_sortable_columns() {
        return array(
            'title'        => array( 'title', false ),
            'url'          => array( 'url', false ),
            'slug'         => array( 'slug', false ),
            'pages'        => array( 'pages', false ),
            'last_updated' => array( 'last_updated', false ),
            'users'        => array( 'users', false ),
        );
    }

    /**
     * Gets the list of bulk actions available on this table.
     *
     * The format is an associative array:
     * - `'option_name' => 'option_title'
     *
     * @return array
     */
    protected function get_bulk_actions() {
        return array(
            'deleteblogs' => __( 'Delete', SITES_GENERATOR_MENU_SLUG ),
        );
    }

    /**
     * Function process bulk actions
     */
    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            $site_id = sanitize_text_field($_REQUEST['id']);
            wp_delete_site( $site_id );
        }
        if ( 'deleteblogs' === $this->current_action() ) {
            $sites = isset( $_REQUEST['site'] ) ? (array) $_REQUEST['site'] : array();
            $sites = array_map( 'sanitize_text_field', $sites );

            foreach ( $sites as $site_id ) {
                wp_delete_site( $site_id );
            }
        }
    }

    /**
     * Sorting
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function usort_reorder( $a, $b ) {
        $orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'title';
        $order   = ( ! empty( $_REQUEST['order'] ) )   ? sanitize_text_field( $_REQUEST['order'] )   : 'asc';
        $result  = strcmp( $a[ $orderby ], $b[ $orderby ] );

        return ( 'asc' === $order ) ? $result : -$result;
    }

    /**
     * Prepares the list of items for displaying.
     */
    public function prepare_items() {
        $per_page     = 10;
        $search_value = ! empty( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();
        $db_data        = $this->get_data_from_db( $search_value );
        $formatted_data = $this->get_formatted_table_data( $db_data );
        $db_data        = ! empty( $formatted_data ) ? $formatted_data : array();
        usort( $db_data, array( $this, 'usort_reorder' ) );
        $current_page = $this->get_pagenum();
        $total_items  = count( $db_data );
        $db_data      = array_slice( $db_data, ( ( $current_page - 1 ) * $per_page ), $per_page );
        $this->items  = $db_data;
        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page ),
            )
        );
    }

    /**
     * Get the needed data from database.
     * If search value wasn't empty, function return relevant entries.
     * If search value was empty, function return all entries.
     * @param string $search_value
     * @return array|object|null
     */
    private function get_data_from_db( $search_value ) {
        global $wpdb;
        $db_name = SITES_GENERATOR_DB_NAME;

        return empty( $search_value ) ?
            $wpdb->get_results( "SELECT * FROM $db_name" )
            : $wpdb->get_results( "SELECT * FROM $db_name WHERE domain LIKE '%$search_value%'" );

    }

    /**
     * Formats data from the database to the needed format
     *
     * @param array $data
     * @return array
     */
    private function get_formatted_table_data( $data )
    {
        $formatted_data = array();

        foreach ($data as $item) {
            switch_to_blog($item->blog_id);

            $site_details = get_site();
            $site_name = get_option('blogname');
            $current_path = SUBDOMAIN_INSTALL ? $item->domain : $site_details->domain . $site_details->path;
            $current_slug = SUBDOMAIN_INSTALL ? preg_split('/[.]+/', $item->domain)[0] : trim($site_details->path, '/');

            $formatted_data[] = array(
                'ID' => $item->blog_id,
                'title' => $site_name,
                'url' => $current_path,
                'slug' => $current_slug,
                'pages' => '<a href="' . $this->get_blog_pages_url(get_site_url()) . '">' . count(get_pages()) . '</a>',
                'last_updated' => $site_details->last_updated,
                'users' => count_users('time', $item->blog_id)['total_users'],
            );

            wp_reset_postdata();
            restore_current_blog();
        }

        return $formatted_data;
    }

    /**
     * @param string $blog_url
     *
     * @return string
     */

    private function get_blog_pages_url( $blog_url )
    {
        return $blog_url . '/wp-admin/edit.php?post_type=page';
    }

    private function bool_to_string( $value ) {
        return ( '0' === $value ) ?
            '<span class="no">' . __( 'No', SITES_GENERATOR_MENU_SLUG ) . '</span>'
            : '<span class="yes">' . __( 'Yes', SITES_GENERATOR_MENU_SLUG ) . '</span>';

    }

    /**
     * @return string
     */
    private function get_edit_site_page_url() {
        return network_admin_url( 'site-info.php' );
    }
}