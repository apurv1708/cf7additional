<?php
/*
Plugin Name:  cf7addons
Plugin URI:   https://www.jb.com
Description:  Show in Admin panel
Version:      1.0.0
Author:       Jb
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

*/
require_once(ABSPATH . 'wp-load.php');

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

require plugin_dir_path(__FILE__) . 'insert-db.php';

require plugin_dir_path(__FILE__) . 'create-tables/createtable.php';

function callback_login_functions() {
    if (is_user_logged_in() && is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        add_submenu_page('wpcf7', 'CF7 Submenu Page', 'CF7 Submenu', 'manage_options', 'cf7-data-submenu', 'cf7_submenu_page_callback');
    } else {
        // Plugin is not active or user is not logged in, display error notice
        add_action('admin_notices', 'cf7_activation_error_notice');
    }
}
add_action('admin_menu', 'callback_login_functions');

function cf7_submenu_page_callback() {
    // Your callback function implementation goes here
    // This function should output the content of your submenu page
    echo '<div class="wrap"><h2>CF7 Submenu Page</h2><p>This is the content of your submenu page.</p></div>';
}

function cf7_activation_error_notice() {
    echo '<div class="notice notice-error is-dismissible">';
    echo "<p>Please activate CF7 plugin and make sure you are logged in to access this page.</p>";
    echo '</div>';
}




/**
 * Activation hook
 */

register_activation_hook(__FILE__, 'aws_create_db_table_for_cf7_entries');

function aws_create_db_table_for_cf7_entries()
{
	cf_addons_table_cf7_entries();
	cf_addons_table_cf7_entry_meta();
}
