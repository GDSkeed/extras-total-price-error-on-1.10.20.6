<?php

/**
 * AJAX Handlers for Translation Manager
 * Handles all AJAX requests for the translation manager functionality
 */
class HostifyBooking_Translation_Ajax_Handlers {

    /**
     * AJAX handler for extracting strings
     */
    public static function ajax_extract_strings() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            require_once __DIR__ . '/../class-string-extractor.php';
            
            $extractor = new HostifyBooking_String_Extractor();
            $result = $extractor->run_complete_update();
            

            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for getting available languages
     */
    public static function ajax_get_languages() {
        // Check if nonce is present
        if (!isset($_POST['nonce'])) {
            wp_die('No nonce provided');
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hostifybooking_ajax_nonce')) {
            wp_die('Nonce verification failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            require_once __DIR__ . '/../class-string-extractor.php';
            
            // Clear any potential WordPress transients that might cache the results
            delete_transient('hostifybooking_languages_cache');
            
            $editor = new HostifyBooking_Translation_Editor();
            $languages = $editor->get_available_languages();
            
            // Get translation progress for each language
            $languages_with_progress = array();
            
            // Get total strings from current extraction to ensure consistency
            $extractor = new HostifyBooking_String_Extractor();
            $extracted_strings = $extractor->extract_strings();
            $total_strings = count($extracted_strings);
            
            // After restore, we need to check if the restored files have a different total
            // If all restored files have the same count and it's different from extraction, use that
            $restored_total = null;
            $po_files = glob($editor->get_lang_dir() . 'hostifybooking-*.po');
            if (!empty($po_files)) {
                $first_po_file = $po_files[0];
                clearstatcache(true, $first_po_file);
                $po_translations = $editor->load_existing_translations($first_po_file);
                $restored_total = count($po_translations);
                
                // If restored files have a different count, use that for consistency
                if ($restored_total != $total_strings) {
                    $total_strings = $restored_total;
                }
            }
            
            foreach ($languages as $lang_code) {
                $po_file = $editor->get_lang_dir() . 'hostifybooking-' . $lang_code . '.po';
                
                if (file_exists($po_file)) {
                    // Clear file system cache for the PO file
                    clearstatcache(true, $po_file);
                    
                    // Use the exact same logic as the extraction process
                    $extractor = new HostifyBooking_String_Extractor();
                    $existing_translations = $extractor->load_existing_translations($po_file);
                    
                    // Count actual translations (non-empty msgstr values) in this PO file
                    $translated_count = 0;
                    $total_in_file = count($existing_translations);
                    
                    foreach ($existing_translations as $msgid => $msgstr) {
                        // Check if this is a plural form (contains | separator)
                        if (strpos($msgstr, '|') !== false) {
                            // For plural forms, check if both parts are empty
                            $parts = explode('|', $msgstr);
                            $has_content = false;
                            foreach ($parts as $part) {
                                if (!empty(trim($part))) {
                                    $has_content = true;
                                    break;
                                }
                            }
                            if ($has_content) {
                                $translated_count++;
                            }
                        } else {
                            // For singular forms, check if not empty
                            if (!empty(trim($msgstr))) {
                                $translated_count++;
                            }
                        }
                    }
                    
                    $percentage = $total_in_file > 0 ? min(floor(($translated_count / $total_in_file) * 1000) / 10, 100) : 0;
                    

            
                    
                    $languages_with_progress[] = array(
                        'code' => $lang_code,
                        'translated' => $translated_count,
                        'total' => $total_in_file,
                        'percentage' => $percentage
                    );
                } else {
                    $languages_with_progress[] = array(
                        'code' => $lang_code,
                        'translated' => 0,
                        'total' => $total_strings,
                        'percentage' => 0
                    );
                }
            }
            
            wp_send_json_success(array('languages' => $languages_with_progress));
        } catch (Exception $e) {
    
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for loading translations from a PO file
     */
    public static function ajax_load_translations() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $file = sanitize_text_field($_POST['file']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $parsed_data = $editor->load_translations($file);
            
            wp_send_json_success($parsed_data);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for saving translations to a PO file
     */
    public static function ajax_save_translations() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $file = sanitize_text_field($_POST['file']);
        $translations = $_POST['translations'];
        $comments = isset($_POST['comments']) ? $_POST['comments'] : array();
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->save_translations($file, $translations, $comments);
            
            // Return save result
            $response = array(
                'translations_count' => count($translations),
                'comments_count' => count($comments),
                'file' => $file,
                'save_result' => $result
            );
            
            wp_send_json_success($response);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for downloading a PO file
     */
    public static function ajax_download_po() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $file = sanitize_text_field($_POST['file']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $file_data = $editor->download_po_file($file);
            
            // Set headers for file download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_data['filename'] . '"');
            header('Content-Length: ' . $file_data['size']);
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            echo $file_data['content'];
            exit;
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for uploading a PO file
     */
    public static function ajax_upload_po() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!isset($_FILES['po_file']) || !isset($_POST['filename'])) {
            wp_send_json_error('No file uploaded or filename missing');
        }
        
        $filename = sanitize_text_field($_POST['filename']);
        $create_backup = isset($_POST['create_backup']) ? (bool)$_POST['create_backup'] : true;
        $backup_title = isset($_POST['backup_title']) ? sanitize_text_field($_POST['backup_title']) : '';
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->upload_po_file($_FILES['po_file'], $filename, $create_backup, $backup_title);
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for creating backup
     */
    public static function ajax_create_backup() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            
            // Get backup title from POST data
            $backup_title = isset($_POST['backup_title']) ? sanitize_text_field($_POST['backup_title']) : '';
            
            $result = $editor->create_backup($backup_title);
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for getting backups list
     */
    public static function ajax_get_backups() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $backups = $editor->get_backups();
            
            wp_send_json_success(array('backups' => $backups));
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for restoring from backup
     */
    public static function ajax_restore_backup() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $backup_name = sanitize_text_field($_POST['backup_name']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->restore_backup($backup_name);
            
            wp_send_json_success(array('restored' => $result));
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for deleting backup
     */
    public static function ajax_delete_backup() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $backup_name = sanitize_text_field($_POST['backup_name']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->delete_backup($backup_name);
            
            wp_send_json_success('Backup deleted successfully');
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for downloading backup as ZIP
     */
    public static function ajax_download_backup() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $backup_name = sanitize_text_field($_POST['backup_name']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $backup_data = $editor->download_backup($backup_name);
            
            // Set headers for ZIP download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $backup_data['filename'] . '"');
            header('Content-Length: ' . $backup_data['size']);
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            // Output ZIP file and clean up
            readfile($backup_data['temp_file']);
            unlink($backup_data['temp_file']);
            exit;
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for getting backup path info
     */
    public static function ajax_get_backup_path() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $backup_name = sanitize_text_field($_POST['backup_name']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $path_info = $editor->get_backup_path($backup_name);
            $size = $editor->get_backup_size($backup_name);
            
            $path_info['size'] = $size;
            $path_info['size_formatted'] = size_format($size);
            
            wp_send_json_success($path_info);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for compiling all PO files to MO files
     */
    public static function ajax_compile_mo_files() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->compile_all_po_files();
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for deleting all backups
     */
    public static function ajax_delete_all_backups() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->delete_all_backups();
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for creating new language file
     */
    public static function ajax_create_language() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $language_code = sanitize_text_field($_POST['language_code']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->create_language_file($language_code);
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for cleaning up duplicate comments in a PO file
     */
    public static function ajax_cleanup_file() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $file = sanitize_text_field($_POST['file']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->cleanup_file($file);
            
            if ($result) {
                wp_send_json_success('File cleaned up successfully');
            } else {
                wp_send_json_error('Failed to clean up file');
            }
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * AJAX handler for comprehensive cleanup of all PO files
     */
    public static function ajax_cleanup_all_po_files() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        try {
            $lang_dir = plugin_dir_path(__FILE__) . '../../lang/';
            $po_files = glob($lang_dir . '*.po');
            
            if (empty($po_files)) {
                wp_send_json_error('No PO files found');
            }
            
            $total_duplicates_removed = 0;
            $total_test_strings_removed = 0;
            $cleaned_files = array();
            
            foreach ($po_files as $po_file) {
                $filename = basename($po_file);
                
                // Create backup
                $backup_file = $po_file . '.backup-' . date('Y-m-d-H-i-s');
                if (!copy($po_file, $backup_file)) {
                    continue;
                }
                
                $content = file_get_contents($po_file);
                $lines = explode("\n", $content);
                $new_lines = array();
                $seen_entries = array();
                $duplicates_removed = 0;
                $test_strings_removed = 0;
                
                $i = 0;
                while ($i < count($lines)) {
                    $line = $lines[$i];
                    
                    // Check if this is a msgid line (not the header msgid)
                    if (strpos($line, 'msgid "') === 0 && $line !== 'msgid ""') {
                        $msgid = substr($line, 7, -1); // Remove msgid " and "
                        $msgid = stripslashes($msgid);
                        
                        // Check if this is a test string
                        if ($msgid === 'singular' || $msgid === 'plural') {
                            $test_strings_removed++;
                            
                            // Skip this entry and its associated lines
                            $i = self::skip_to_next_entry($lines, $i);
                            continue;
                        }
                        
                        // Check if this is a plural form
                        $is_plural = false;
                        $plural_key = '';
                        
                        // Look ahead to see if there's a msgid_plural
                        if ($i + 1 < count($lines) && strpos($lines[$i + 1], 'msgid_plural "') === 0) {
                            $msgid_plural = substr($lines[$i + 1], 13, -1);
                            $msgid_plural = stripslashes($msgid_plural);
                            $plural_key = $msgid . '|' . $msgid_plural;
                            $is_plural = true;
                        }
                        
                        // Check if we've seen this entry before
                        $entry_key = $is_plural ? $plural_key : $msgid;
                        
                        if (isset($seen_entries[$entry_key])) {
                            $duplicates_removed++;
                            
                            // Skip this entry and its associated lines
                            $i = self::skip_to_next_entry($lines, $i);
                            continue;
                        }
                        
                        // Mark this entry as seen
                        $seen_entries[$entry_key] = true;
                    }
                    
                    // Add this line to the new content
                    $new_lines[] = $line;
                    $i++;
                }
                
                // Write the cleaned content back to the file
                $new_content = implode("\n", $new_lines);
                if (file_put_contents($po_file, $new_content)) {
                    $cleaned_files[] = $filename;
                    $total_duplicates_removed += $duplicates_removed;
                    $total_test_strings_removed += $test_strings_removed;
                }
            }
            
            $result = array(
                'success' => true,
                'message' => sprintf(
                    'Cleaned %d files. Removed %d duplicates and %d test strings.',
                    count($cleaned_files),
                    $total_duplicates_removed,
                    $total_test_strings_removed
                ),
                'cleaned_files' => $cleaned_files,
                'duplicates_removed' => $total_duplicates_removed,
                'test_strings_removed' => $total_test_strings_removed
            );
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    
    /**
     * Skip to the next entry (after msgstr[1] for plural forms or msgstr for regular strings)
     */
    private static function skip_to_next_entry($lines, $current_index) {
        $i = $current_index;
        
        // Skip the current msgid line
        $i++;
        
        // If this is a plural form, skip msgid_plural and msgstr[0] and msgstr[1]
        if ($i < count($lines) && strpos($lines[$i], 'msgid_plural "') === 0) {
            $i++; // Skip msgid_plural
            while ($i < count($lines) && strpos($lines[$i], 'msgstr[') === 0) {
                $i++; // Skip msgstr[0] and msgstr[1]
            }
        } else {
            // Regular string, skip msgstr
            while ($i < count($lines) && strpos($lines[$i], 'msgstr "') === 0) {
                $i++; // Skip msgstr
            }
        }
        
        // Skip any empty lines
        while ($i < count($lines) && trim($lines[$i]) === '') {
            $i++;
        }
        
        return $i;
    }

    /**
     * AJAX handler for cleaning up malformed entries in a PO file
     */
    public static function ajax_cleanup_malformed_entries() {
        check_ajax_referer('hostifybooking_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $file = sanitize_text_field($_POST['file']);
        
        try {
            require_once __DIR__ . '/../class-translation-editor.php';
            
            $editor = new HostifyBooking_Translation_Editor();
            $result = $editor->cleanup_malformed_entries($file);
            
            wp_send_json_success('Malformed entries cleaned up successfully');
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Initialize AJAX handlers
     */
    public static function init() {
        add_action('wp_ajax_hostifybooking_extract_strings', array(__CLASS__, 'ajax_extract_strings'));
        add_action('wp_ajax_hostifybooking_get_languages', array(__CLASS__, 'ajax_get_languages'));
        add_action('wp_ajax_hostifybooking_load_translations', array(__CLASS__, 'ajax_load_translations'));
        add_action('wp_ajax_hostifybooking_save_translations', array(__CLASS__, 'ajax_save_translations'));
        add_action('wp_ajax_hostifybooking_download_po', array(__CLASS__, 'ajax_download_po'));
        add_action('wp_ajax_hostifybooking_upload_po', array(__CLASS__, 'ajax_upload_po'));
        add_action('wp_ajax_hostifybooking_create_backup', array(__CLASS__, 'ajax_create_backup'));
        add_action('wp_ajax_hostifybooking_get_backups', array(__CLASS__, 'ajax_get_backups'));
        add_action('wp_ajax_hostifybooking_restore_backup', array(__CLASS__, 'ajax_restore_backup'));
        add_action('wp_ajax_hostifybooking_delete_backup', array(__CLASS__, 'ajax_delete_backup'));
        add_action('wp_ajax_hostifybooking_download_backup', array(__CLASS__, 'ajax_download_backup'));
        add_action('wp_ajax_hostifybooking_get_backup_path', array(__CLASS__, 'ajax_get_backup_path'));
        add_action('wp_ajax_hostifybooking_compile_mo_files', array(__CLASS__, 'ajax_compile_mo_files'));
        add_action('wp_ajax_hostifybooking_delete_all_backups', array(__CLASS__, 'ajax_delete_all_backups'));
        add_action('wp_ajax_hostifybooking_create_language', array(__CLASS__, 'ajax_create_language'));
        add_action('wp_ajax_hostifybooking_cleanup_file', array(__CLASS__, 'ajax_cleanup_file'));
        add_action('wp_ajax_hostifybooking_cleanup_all_po_files', array(__CLASS__, 'ajax_cleanup_all_po_files'));
        add_action('wp_ajax_hostifybooking_cleanup_malformed_entries', array(__CLASS__, 'ajax_cleanup_malformed_entries'));
    }
} 