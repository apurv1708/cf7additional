<?php 
add_action( 'wpcf7_before_send_mail', 'save_cf7_entry_data' );
    function save_cf7_entry_data($form) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'cf_addons_entries';
            
            // Get submitted form data
            $submission = WPCF7_Submission::get_instance();

            if ( $submission ) {

                $wpcf7 = WPCF7_ContactForm::get_current();
                $form_id = $wpcf7->id;
                    $data = $submission->get_posted_data();
                    $form_tags = $form->scan_form_tags();

                    $form_field_count = count($form_tags);
                    
                    $final_arr = array();

                    for($j = 0; $j < $form_field_count; $j++) {
                        $form_tags[$j]->name;
                        if(in_array($form_tags[$j]->name, array_keys($data))){
                            $currentField = array();
                            $currentField['type'] = $form_tags[$j]->basetype; 
                            $currentField['name'] = $form_tags[$j]->name;
                            $currentField['value'] = $form_tags[$j]->values;
                            // echo $form_tags[$j]->basetype . " <br> ";
                            $final_arr[ $form_tags[$j]->name] = $currentField;
                        }
                    }

                    $wpcf7 = WPCF7_ContactForm::get_current();
                    $form_id = $wpcf7->id;

                    $entry_time = current_time('mysql');
                    $wpdb->insert(
                        $table_name,
                        array(
                            'form_id' => $form_id,
                            'form_flag' => 'pending',
                            'submission_date' => $entry_time,
                        )
                    );

                    $lastid = $wpdb->insert_id;

                    $meta_table = $wpdb->prefix . 'cf_addons_entry_meta';

        //==============================================================================

        $uploaded_files = $submission->uploaded_files();

        $upload_dir = wp_upload_dir();
        
        if ($uploaded_files) {
            foreach ($uploaded_files as $field_name => $file_paths) {

                $custom_cf7_folder = $upload_dir['basedir'] . '/cf-addons/' . $form_id;

                if (!file_exists($custom_cf7_folder)) {
                    wp_mkdir_p($custom_cf7_folder);
                }

                // Define your custom folder path
                $custom_folder_path = $upload_dir['basedir'] . '/cf-addons/' . $form_id;
                $file_db_path = array();
                foreach ($file_paths as $file_path) {

                    // Get just the file name
                    $file_name = basename($file_path);
                
                    $new_file_path = $custom_folder_path . '/' . $file_name;
                    $file_db_path[] = $new_file_path;
                    rename($file_paths[0], $new_file_path);

                }
            }
        }

        //==============================================================================

        foreach($data as $form_key => $field_value) {

            if(is_array($field_value)){
                $field_value = implode(', ', $field_value);
            }
            
            $field_type = $final_arr[$form_key];

            if(is_array($field_type['value'])){
                $field_val = implode(', ', $field_type['value']);
            }

            if($field_type['type'] == "file"){
                $field_value = $file_db_path[0];
            }

            $wpdb->insert(
                $meta_table,
                array(
                    'entry_form_id' => $lastid,
                    'form_field' => $form_key,
                    'form_value' => $field_value,
                    'field_type' => $field_type['type'],
                    'field_value' => $field_val,
                )
            );
        }
    }
}

add_action( 'wpcf7_before_send_mail', 'contactform7_before_send_mail' );

// Add custom script
function aws_enqueue_custom_script() {
    wp_enqueue_style('aws-style', plugin_dir_url(__FILE__) . 'css/cusstom.css', '1.0', true);
}
add_action('admin_enqueue_scripts', 'aws_enqueue_custom_script');
