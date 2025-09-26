<?php

/**
 * Translation Manager for Hostify Booking Plugin
 * Main loader file that includes all translation manager functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include translation manager classes
try {
    require_once __DIR__ . '/class-string-extractor.php';
    require_once __DIR__ . '/class-translation-editor.php';
    require_once __DIR__ . '/ajax/class-ajax-handlers.php';
} catch (Exception $e) {
    // Silent error handling
}

/**
 * Initialize AJAX handlers early
 */
function hostifybooking_init_translation_ajax() {
    try {
        // Make sure the class exists
        if (class_exists('HostifyBooking_Translation_Ajax_Handlers')) {
            HostifyBooking_Translation_Ajax_Handlers::init();
        }
    } catch (Exception $e) {
        // Silent error handling
    }
}

/**
 * Initialize the translation manager scripts and styles
 */
function hostifybooking_init_translation_manager() {
    // Only load scripts and styles on the plugin admin page
    $screen = get_current_screen();
    if ($screen && $screen->base === 'settings_page_hostifybooking-plugin') {
        
        // Ensure textdomain is loaded before localizing strings
        load_plugin_textdomain('hostifybooking', false, dirname(plugin_basename(HOSTIFYBOOKING_DIR . 'hostifybooking.php')) . '/lang/');
        
        // Enqueue translation manager scripts and styles
        wp_enqueue_script(
            'hostifybooking-translation-manager',
            plugin_dir_url(__FILE__) . 'js/translation-manager.js',
            array('jquery'),
            HOSTIFYBOOKING_VERSION,
            false
        );
        
        wp_enqueue_style(
            'hostifybooking-translation-manager',
            plugin_dir_url(__FILE__) . 'css/translation-manager.css',
            array(),
            HOSTIFYBOOKING_VERSION
        );
        
        // Localize script with AJAX data and translated strings
        wp_localize_script('hostifybooking-translation-manager', 'hostifybooking_ajax', array(
            'nonce' => wp_create_nonce('hostifybooking_ajax_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'strings' => array(
                'translations_saved' => __('Translations saved successfully!', 'hostifybooking'),
                'loaded_translations' => __('Loaded %d translations from %s', 'hostifybooking'),
                'compiling_mo_files' => __('Compiling MO files...', 'hostifybooking'),
                'successfully_compiled' => __('Successfully compiled %d MO files.', 'hostifybooking'),
                'compilation_errors' => __('Errors: %s', 'hostifybooking'),
                'failed_to_compile' => __('Failed to compile MO files', 'hostifybooking'),
                'extracting_strings' => __('Extracting...', 'hostifybooking'),
                'extract_strings' => __('Extract Strings', 'hostifybooking'),
                'extraction_completed' => __('Extraction completed successfully!', 'hostifybooking'),
                'total_strings_found' => __('Total strings found: %d', 'hostifybooking'),
                'files_with_new_strings' => __('Files with new strings added: %s', 'hostifybooking'),
                'files_with_locations' => __('Files updated with source location comments: %s', 'hostifybooking'),
                'po_files_updated' => __('PO files with updated headers: %d files', 'hostifybooking'),
                'missing_translations_report' => __('Missing Translations Report:', 'hostifybooking'),
                'missing_translations_line' => __('%s: %d missing translations (%d/%d translated - %s%%)', 'hostifybooking'),
                'uploading' => __('Uploading...', 'hostifybooking'),
                'upload_restore' => __('Upload & Restore', 'hostifybooking'),
                'file_uploaded_successfully' => __('File uploaded and restored successfully!', 'hostifybooking'),
                'backup_created' => __('Backup created: %s', 'hostifybooking'),
                'compiling' => __('Compiling...', 'hostifybooking'),
                'compile_mo_files' => __('Compile MO Files', 'hostifybooking')
            )
        ));
        

    }
}

// Initialize AJAX handlers early - use both init and admin_init to ensure it runs
add_action('init', 'hostifybooking_init_translation_ajax');
add_action('admin_init', 'hostifybooking_init_translation_ajax');

// Hook into admin_enqueue_scripts for scripts and styles
add_action('admin_enqueue_scripts', 'hostifybooking_init_translation_manager');

/**
 * Auto-backup translations before plugin updates
 */
function hostifybooking_auto_backup_before_update() {
    // Only run on plugin update pages
    if (!isset($_GET['action']) || $_GET['action'] !== 'upgrade-plugin') {
        return;
    }
    
    // Check if this is our plugin being updated
    if (!isset($_GET['plugin']) || strpos($_GET['plugin'], 'hostify-booking') === false) {
        return;
    }
    
            try {
            require_once __DIR__ . '/class-translation-editor.php';
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->create_backup('Auto-backup before plugin update');
        
        // Store backup info in transient for display
        set_transient('hostifybooking_auto_backup', array(
            'backup_dir' => $result['backup_dir'],
            'files' => $result['files'],
            'timestamp' => current_time('mysql')
        ), 3600); // Keep for 1 hour
        

    } catch (Exception $e) {
        // Silent error handling
    }
}

// Hook into plugin update process
add_action('admin_init', 'hostifybooking_auto_backup_before_update');

/**
 * Display auto-backup notification
 */
function hostifybooking_auto_backup_notice() {
    $backup_info = get_transient('hostifybooking_auto_backup');
    if ($backup_info) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Hostify Booking:</strong> Automatic backup created before plugin update. ';
        echo 'Backup: <code>' . $backup_info['backup_dir'] . '</code> ';
        echo '(' . count($backup_info['files']) . ' files) - ';
        echo '<a href="' . admin_url('options-general.php?page=hostifybooking-plugin#translation_manager') . '">View in Translation Manager</a></p>';
        echo '</div>';
        delete_transient('hostifybooking_auto_backup');
    }
}

add_action('admin_notices', 'hostifybooking_auto_backup_notice'); 