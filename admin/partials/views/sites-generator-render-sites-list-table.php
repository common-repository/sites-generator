<?php
if ( ! class_exists( 'Sites_Generator_List_Table' ) ) {
    require_once __DIR__ . '/../../../includes/class-sites-generator-list-table.php';
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Shows sites list
 */
function sites_generator_render_sites_list_table() {


    $table = new Sites_Generator_List_Table();
    $table->prepare_items();
    ?>

    <div class="wrap">
        <div id="icon-users" class="icon32"><br/></div>
        <div class="title">
            <h1 class="wp-heading-inline"><?php _e( 'Add new sites', SITES_GENERATOR_MENU_SLUG ); ?></h1>
            <a href="#" class="page-title-action"><?php _e( 'Add Sites', SITES_GENERATOR_MENU_SLUG ); ?></a>
            <a href="#" class="page-title-action"><?php _e( 'Add Pages', SITES_GENERATOR_MENU_SLUG ); ?></a>
        </div>
        <form id="sites-generator-form" method="post">
            <?php wp_nonce_field(); ?>
            <?php $table->search_box( 'Search', 'search' ); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $table->display(); ?>
        </form>
    </div>
    <?php
}