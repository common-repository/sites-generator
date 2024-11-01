<?php
global $wpdb;

define('SITES_GENERATOR_DB_NAME', $wpdb->prefix . "sites_generator_sites");
define('SITES_GENERATOR_MENU_SLUG', 'sites-generator');
define('SITES_GENERATOR_URL', admin_url( 'admin.php?page=') . SITES_GENERATOR_MENU_SLUG);