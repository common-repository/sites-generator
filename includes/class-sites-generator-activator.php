<?php

/**
 * Fired during plugin activation
 *
 * @link       http://test.com
 * @since      1.0.0
 *
 * @package    Sites_Generator
 * @subpackage Sites_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sites_Generator
 * @subpackage Sites_Generator/includes
 * @author     Author <author@site.com>
 */
class Sites_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    self::create_plugin_table();
	}

	private static function create_plugin_table () {
        global $wpdb;

        $table_name = SITES_GENERATOR_DB_NAME;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          blog_id mediumint(9) NOT NULL,
          domain text NOT NULL,      
          PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }


}
