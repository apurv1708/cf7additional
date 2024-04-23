<?php
/**
 * Create database table on plugin activation callbacks
 * aws_cf7_entries
 * aws_cf7_entry_meta
 */

// Plugin activate create tabale database
function cf_addons_table_cf7_entries() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cf_addons_entries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        ID mediumint(9) NOT NULL AUTO_INCREMENT,
        form_id mediumint(9) NOT NULL,
        form_flag enum('pending','approved','disapproved') DEFAULT 'pending',
        submission_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (ID)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function  cf_addons_table_cf7_entry_meta() {
    global $wpdb;
    $meta_table_name = $wpdb->prefix . 'cf_addons_entry_meta';
    $charset_collate = $wpdb->get_charset_collate();

    $meta_sql = "CREATE TABLE $meta_table_name (
        ID bigint(20) NOT NULL AUTO_INCREMENT,
        entry_form_id int(20) NOT NULL,
        form_field varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
        form_value longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
        field_type varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
        field_value longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
        PRIMARY KEY (`ID`)
      ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $meta_sql );
}