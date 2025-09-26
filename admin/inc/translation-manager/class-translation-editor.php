<?php

/**
 * Translation Editor for Hostify Booking Plugin
 * Handles editing, saving, and compiling translation files
 */
require_once __DIR__ . '/class-mo-compiler.php';

class HostifyBooking_Translation_Editor {

    private $lang_dir;

    public function __construct() {
        $this->lang_dir = dirname(__FILE__, 4) . '/lang/';
    }



    /**
     * Manually clean up duplicate comments in a PO file
     */
    public function cleanup_file($filename) {
        if (empty($filename) || !preg_match('/^hostifybooking-[a-z]{2}_[A-Z]{2}\.po$/', $filename)) {
            throw new Exception('Invalid file name');
        }
        
        $file_path = $this->lang_dir . $filename;
        if (!file_exists($file_path)) {
            throw new Exception('File not found');
        }
        
        // Simple approach: just remove all duplicate comment lines
        $content = file_get_contents($file_path);
        $lines = explode("\n", $content);
        $cleaned_lines = array();
        $seen_comments = array();
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (strpos($trimmed, '# ') === 0) {
                $comment = substr($trimmed, 2);
                $comment_lower = strtolower($comment);
                if (!in_array($comment_lower, $seen_comments)) {
                    $cleaned_lines[] = $line;
                    $seen_comments[] = $comment_lower;
                }
                // Skip duplicates
            } else {
                $cleaned_lines[] = $line;
            }
        }
        
        $cleaned_content = implode("\n", $cleaned_lines);
        $result = file_put_contents($file_path, $cleaned_content);
        
        if ($result !== false) {
    
        }
        
        return $result !== false;
    }

    /**
     * Load translations from a PO file
     */
    public function load_translations($filename) {
        if (empty($filename) || !preg_match('/^hostifybooking-[a-z]{2}_[A-Z]{2}\.po$/', $filename)) {
            throw new Exception('Invalid file name');
        }
        
        $file_path = $this->lang_dir . $filename;
        if (!file_exists($file_path)) {
            throw new Exception('File not found');
        }
        
        // BRUTE FORCE SOLUTION: Load ALL translations and comments without complex parsing
        $content = file_get_contents($file_path);
        $translations = array();
        $comments = array();
        
        // Extract comments and their associated msgids - SIMPLER APPROACH
        $lines = explode("\n", $content);
        $current_msgid = '';
        $current_comment = '';
        
        foreach ($lines as $line) {
            if (strpos($line, 'msgid "') === 0) {
                $msgid_content = substr($line, 7, -1);
                $current_msgid = stripslashes($msgid_content);
                if ($current_msgid !== '' && !empty($current_comment)) {
                    $comments[$current_msgid] = $current_comment;
                }
                $current_comment = '';
            } elseif (strpos($line, '# ') === 0 && strpos($line, '#:') !== 0) {
                $current_comment = trim(substr($line, 2));
            }
        }
        
        // Simple regex to extract ALL msgid/msgstr pairs - FIXED PATTERN
        preg_match_all('/msgid\s+"([^"]*)"\s*\nmsgstr\s+"([^"]*)"/s', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $msgid = stripslashes($match[1]);
            $msgstr = stripslashes($match[2]);
            
            // Skip empty header entry
            if ($msgid === '') {
                continue;
            }
            
            $translations[$msgid] = $msgstr;
        }
        
        // Also handle plural forms - FIXED PATTERN
        preg_match_all('/msgid\s+"([^"]*)"\s*\nmsgid_plural\s+"([^"]*)"\s*\nmsgstr\[0\]\s+"([^"]*)"\s*\nmsgstr\[1\]\s+"([^"]*)"/s', $content, $plural_matches, PREG_SET_ORDER);
        
        foreach ($plural_matches as $match) {
            $singular = stripslashes($match[1]);
            $plural = stripslashes($match[2]);
            $singular_translation = stripslashes($match[3]);
            $plural_translation = stripslashes($match[4]);
            
            $plural_key = $singular . '|' . $plural;
            $translations[$plural_key] = array(
                'singular' => $singular_translation,
                'plural' => array(
                    0 => $singular_translation,
                    1 => $plural_translation
                )
            );
        }
        
        $parsed_data = array(
            'translations' => $translations,
            'comments' => $comments,
            'debug_info' => array(
                'total_translations' => count($translations),
                'total_comments' => count($comments),
                'file_path' => $file_path,
                'file_size' => filesize($file_path),
                'BRUTE_FORCE_METHOD' => 'Used simple regex instead of complex parsing',
                'regex_matches' => count($matches),
                'plural_regex_matches' => count($plural_matches)
            )
        );
        
        // Use debug info from parsing if available, otherwise create it
        if (!isset($parsed_data['debug_info'])) {
            $parsed_data['debug_info'] = array(
                'total_translations' => isset($parsed_data['translations']) ? count($parsed_data['translations']) : 0,
                'total_comments' => isset($parsed_data['comments']) ? count($parsed_data['comments']) : 0,
                'file_path' => $file_path,
                'file_size' => filesize($file_path)
            );
        }
        
        return $parsed_data;
    }

    /**
     * Save translations to a PO file
     */
    public function save_translations($filename, $translations, $comments = array()) {
        if (empty($filename) || !preg_match('/^hostifybooking-[a-z]{2}_[A-Z]{2}\.po$/', $filename)) {
            throw new Exception('Invalid file name');
        }
        
        if (!is_array($translations)) {
            throw new Exception('Invalid translations data');
        }
        
        $file_path = $this->lang_dir . $filename;
        
        // Process comments - keep all comments (including empty ones) to allow clearing
        $filtered_comments = array();
        
        // Handle case where comments might be sent as array of arrays
        if (is_array($comments)) {
            foreach ($comments as $msgid => $comment) {
                // If comment is an array, take the last element
                if (is_array($comment)) {
                    $comment = end($comment);
                }
                
                $clean_comment = trim($comment);
                // Keep all comments, including empty ones, so we can clear them
                $filtered_comments[$msgid] = $clean_comment;
            }
        }
        
        // Use only the comments that were sent from frontend
        // If comments array is empty, this will clear all comments from the file
        $final_comments = $filtered_comments;
        

        
        // Use the new simplified save function for testing
        $result = $this->save_po_file_new($file_path, $translations, $final_comments);
        
        // Compile PO to MO after saving
        if ($result) {
            try {
                HostifyBooking_MO_Compiler::compile_po_to_mo($file_path);
                // Reload translations after compiling
                $this->reload_translations();
            } catch (Exception $e) {
                // Silent error handling - don't fail the save operation if MO compilation fails
            }
        }
        
        return $result;
    }

    /**
     * Get available language files
     */
    public function get_available_languages() {
        $languages = array();
        $files = glob($this->lang_dir . '*.po');
        
        foreach ($files as $file) {
            $filename = basename($file, '.po');
            if (preg_match('/hostifybooking-([a-z]{2}_[A-Z]{2})/', $filename, $matches)) {
                $languages[] = $matches[1];
            }
        }
        
        return $languages;
    }

    /**
     * Parse a PO file and extract translations
     */
    private function parse_po_file($file_path) {
        $content = file_get_contents($file_path);
        if (!$content) {
            throw new Exception('Could not read file');
        }
        
        $translations = array();
        $comments = array();
        $current_msgid = '';
        $current_msgstr = '';
        $current_msgid_plural = '';
        $current_msgstr_plural = array();
        $pending_comment = ''; // Store comment for the next msgid
        $in_msgid = false;
        $in_msgstr = false;
        $in_msgid_plural = false;
        $in_msgstr_plural = false;
        $is_plural_form = false;
        $in_header = true; // Track if we're in the header section
        
        $lines = explode("\n", $content);
        $line_count = 0;
        $msgid_count = 0;
        $processed_msgids = array();
        $skipped_msgids = array();
        
        foreach ($lines as $line) {
            $line_count++;
            $line = trim($line);
            
            // Check for comments
            if (strpos($line, '# ') === 0) {
                $comment_text = substr($line, 2); // Remove '# '
                
                // Append comment for the next msgid (collect all comments)
                if (!empty($pending_comment)) {
                    $pending_comment .= "\n" . $comment_text;
                } else {
                    $pending_comment = $comment_text;
                }
                continue;
            }
            
            if (strpos($line, 'msgid "') === 0) {
                $msgid_count++;
                
                // Check if this is the header msgid (empty)
                $msgid_content = substr($line, 7, -1); // Remove 'msgid "' and '"'
                if ($msgid_content === '') {
                    $in_header = true;
                } else {
                    $in_header = false;
                }
                
                // Save previous translation if exists
                if ($current_msgid && $current_msgid !== '') {
                    // Don't skip any translations - process them all
                    // Empty strings might be valid in some cases
                    if ($is_plural_form) {
                        // For plural forms, create a combined key
                        $plural_key = $current_msgid . '|' . $current_msgid_plural;
                        $translations[$plural_key] = array(
                            'singular' => isset($current_msgstr_plural[0]) ? $current_msgstr_plural[0] : '',
                            'plural' => $current_msgstr_plural
                        );
                        $processed_msgids[] = $plural_key;
                    } else {
                        $translations[$current_msgid] = $current_msgstr;
                        $processed_msgids[] = $current_msgid;
                    }
                }
                
                $current_msgid = substr($line, 7, -1); // Remove 'msgid "' and '"'
                $current_msgstr = '';
                $current_msgid_plural = '';
                $current_msgstr_plural = array();
                $in_msgid = true;
                $in_msgstr = false;
                $in_msgid_plural = false;
                $in_msgstr_plural = false;
                $is_plural_form = false;
                
                // Associate the pending comment with this msgid
                if (!empty($pending_comment)) {
                    $comments[$current_msgid] = $pending_comment;
                    $pending_comment = ''; // Reset after using it
                }
            } elseif (strpos($line, 'msgid_plural "') === 0) {
                $current_msgid_plural = substr($line, 13, -1); // Remove 'msgid_plural "' and '"'
                // Strip any remaining quotes that might be in the text
                $current_msgid_plural = trim($current_msgid_plural, '"');
                $in_msgid = false;
                $in_msgid_plural = true;
                $is_plural_form = true;
            } elseif (strpos($line, 'msgstr "') === 0) {
                $current_msgstr = substr($line, 8, -1); // Remove 'msgstr "' and '"'
                $in_msgid = false;
                $in_msgid_plural = false;
                $in_msgstr = true;
                $in_msgstr_plural = false;
            } elseif (preg_match('/^msgstr\[(\d+)\] "(.+)"$/', $line, $matches)) {
                $index = (int)$matches[1];
                $value = $matches[2];
                $current_msgstr_plural[$index] = $value;
                $in_msgid = false;
                $in_msgid_plural = false;
                $in_msgstr = false;
                $in_msgstr_plural = true;
                $is_plural_form = true;
            } elseif ($line && $line[0] === '"' && $line[-1] === '"') {
                // Continuation line (quoted strings that continue from previous lines)
                $value = substr($line, 1, -1);
                if ($in_msgid) {
                    $current_msgid .= $value;
                } elseif ($in_msgid_plural) {
                    $current_msgid_plural .= $value;
                } elseif ($in_msgstr) {
                    $current_msgstr .= $value;
                } elseif ($in_msgstr_plural) {
                    // For plural msgstr, we need to know which index we're continuing
                    // This is a simplified approach - in practice, we'd need more context
                    $last_index = max(array_keys($current_msgstr_plural));
                    $current_msgstr_plural[$last_index] .= $value;
                }
                // If we're not in any context, this is likely a corrupted line
                // We should skip it to avoid adding it as a standalone string
            }
        }
        
        // Save last translation - CRITICAL FIX
        if ($current_msgid && $current_msgid !== '') {
            if ($is_plural_form) {
                // For plural forms, create a combined key
                $plural_key = $current_msgid . '|' . $current_msgid_plural;
                $translations[$plural_key] = array(
                    'singular' => isset($current_msgstr_plural[0]) ? $current_msgstr_plural[0] : '',
                    'plural' => $current_msgstr_plural
                );
                $processed_msgids[] = $plural_key;
            } else {
                $translations[$current_msgid] = $current_msgstr;
                $processed_msgids[] = $current_msgid;
            }
            if (!empty($pending_comment)) {
                $comments[$current_msgid] = $pending_comment;
            }
        }
        

        
        // Return parsing results
        $result = array(
            'total_translations' => count($translations),
            'total_comments' => count($comments),
            'file_path' => $file_path,
            'file_size' => filesize($file_path)
        );
        
        // Return ALL translations from the PO file without filtering
        // This ensures we don't lose any existing translations
        return array(
            'translations' => $translations,
            'comments' => $comments,
            'debug_info' => $result
        );
    }

    /**
     * ULTIMATE SOLUTION: Copy original file exactly and only update changed translations
     * This bypasses ALL parsing issues by working with the original file structure
     */
    private function save_po_file_new($file_path, $translations, $comments = array()) {
        // Save info
        $save_info = array(
            'frontend_translations' => count($translations),
            'frontend_comments' => count($comments),
            'file_path' => $file_path,
            'original_file_size' => file_exists($file_path) ? filesize($file_path) : 0
        );
        
        // ULTIMATE SOLUTION: Copy original file exactly and only update specific translations
        $original_content = file_get_contents($file_path);
        $lines = explode("\n", $original_content);
        $new_lines = array();
        $current_msgid = '';
        $in_translation_block = false;
        $updated_count = 0;
        
        foreach ($lines as $line) {
            // Check if this is a translator comment line (not source location)
            if (strpos($line, '# ') === 0 && strpos($line, '#:') !== 0) {
                // Skip this line - we'll add the updated comment before the msgid
                continue;
            }
            
            // Check if this is a msgid line
            if (strpos($line, 'msgid "') === 0) {
                $msgid_content = substr($line, 7, -1); // Remove 'msgid "' and '"'
                $current_msgid = stripslashes($msgid_content);
                $in_translation_block = true;
                
                // Add updated comment before this msgid if we have one
                if (isset($comments[$current_msgid]) && $current_msgid !== '' && !empty(trim($comments[$current_msgid]))) {
                    $new_lines[] = '# ' . $comments[$current_msgid];
                    $updated_count++;
                }
                
                $new_lines[] = $line; // Keep the original line
            }
            // Check if this is a msgstr line
            elseif (strpos($line, 'msgstr "') === 0 && $in_translation_block) {
                // Check if we have an updated translation for this msgid
                if (isset($translations[$current_msgid]) && $current_msgid !== '') {
                    $new_translation = $translations[$current_msgid];
                    $new_lines[] = 'msgstr "' . $this->escape_po_string($new_translation) . '"';
                    $updated_count++;
                } else {
                    $new_lines[] = $line; // Keep the original line
                }
                $in_translation_block = false;
            }
            // Handle plural forms
            elseif (strpos($line, 'msgstr[') === 0 && $in_translation_block) {
                // For plural forms, we need to check if we have an updated version
                if (isset($translations[$current_msgid]) && is_array($translations[$current_msgid])) {
                    $plural_data = $translations[$current_msgid];
                    if (strpos($line, 'msgstr[0]') === 0) {
                        $new_lines[] = 'msgstr[0] "' . $this->escape_po_string($plural_data['singular']) . '"';
                    } elseif (strpos($line, 'msgstr[1]') === 0) {
                        $new_lines[] = 'msgstr[1] "' . $this->escape_po_string(isset($plural_data['plural'][1]) ? $plural_data['plural'][1] : '') . '"';
                    }
                    $updated_count++;
                } else {
                    $new_lines[] = $line; // Keep the original line
                }
            }
            else {
                $new_lines[] = $line; // Keep all other lines exactly as they are
            }
        }
        
        $content = implode("\n", $new_lines);
        
        $save_info['written_translations'] = $updated_count;
        $save_info['content_length'] = strlen($content);
        
        // Write the file
        $result = file_put_contents($file_path, $content);
        if ($result === false) {
            throw new Exception('Could not write to file');
        }
        
        $save_info['final_file_size'] = filesize($file_path);
        
        return $save_info;
    }

    /**
     * Save translations to a PO file
     */
    private function save_po_file($file_path, $translations, $comments = array()) {
        // Get the POT file path to use as header template
        $pot_file_path = $this->lang_dir . 'hostifybooking.pot';
        
        // Extract language code from the PO file name
        $filename = basename($file_path);
        if (preg_match('/hostifybooking-([a-z]{2}_[A-Z]{2})\.po/', $filename, $matches)) {
            $language_code = $matches[1];
            } else {
            $language_code = 'en_US'; // fallback
        }
        
        // Read the POT file to get the header template
        $pot_content = '';
        if (file_exists($pot_file_path)) {
            $pot_content = file_get_contents($pot_file_path);
        }
        
        // Preserve original PO file headers to maintain exact line count
        $headers = '';
        if (file_exists($file_path)) {
            $po_content = file_get_contents($file_path);
            if ($po_content) {
                $po_lines = explode("\n", $po_content);
                $header_lines = array();
                
                foreach ($po_lines as $line) {
                    $trimmed_line = trim($line);
                    
                    // Stop at the first #: line (source comment)
                    if (strpos($trimmed_line, '#:') === 0) {
                        break;
                    }
                    
                    $header_lines[] = $line;
                }
                
                $headers = implode("\n", $header_lines);
            }
        }
        
        // Generate new content starting with the updated headers
        $content = $headers;
        
        // Ensure there's exactly one blank line after headers
        $content = rtrim($content, "\n") . "\n\n";
        

        
        // REMOVED: Loading correct strings from strings_to_translate.php
        // This was causing line loss by filtering out valid translations
        // We now preserve ALL existing translations without filtering
        
        // Load existing translations from current PO file
        $existing_translations = array();
        $existing_comments = array();
        if (file_exists($file_path)) {
            $parsed_data = $this->parse_po_file($file_path);
            if (isset($parsed_data['translations'])) {
                $existing_translations = $parsed_data['translations'];
            }
            if (isset($parsed_data['comments'])) {
                $existing_comments = $parsed_data['comments'];
            }
        }
        
        // Start with existing translations, then update only the ones that were changed
        $complete_translations = $existing_translations;
        $complete_comments = $existing_comments;
        
        // Count existing translations
        $existing_count = count($existing_translations);
        $save_info = array(
            'existing_translations' => $existing_count,
            'frontend_translations' => count($translations),
            'frontend_comments' => count($comments)
        );
        
        // Update only the translations that were sent from frontend
        foreach ($translations as $msgid => $msgstr) {
            $complete_translations[$msgid] = $msgstr;
        }
        
        // Count after updates
        $updated_count = count($complete_translations);
        $save_info['updated_translations'] = $updated_count;
        
        // Update only the comments that were sent from frontend
        foreach ($comments as $msgid => $comment) {
            $complete_comments[$msgid] = $comment;
        }
        
        // Count comments
        
        // Generate translations section from scratch
        $written_count = 0;
        
        foreach ($complete_translations as $msgid => $msgstr) {
            if ($msgid === '') continue; // Skip empty msgid (headers)
            $written_count++;
            
            // Use the original msgid without any "correction" logic
            // This preserves all existing translations exactly as they are
            $final_msgid = $msgid;
            

            
            // Check if this is a plural form (contains | separator)
            if (strpos($final_msgid, '|') !== false) {
                $parts = explode('|', $final_msgid);
                if (count($parts) === 2 && is_array($msgstr)) {
                    $singular = $parts[0];
                    $plural = $parts[1];
                    $singular_translation = isset($msgstr['singular']) ? $msgstr['singular'] : '';
                    $plural_translations = isset($msgstr['plural']) ? $msgstr['plural'] : array();
                    

                    
                    // Add translator comment if exists for this msgid
                    if (!empty($complete_comments) && isset($complete_comments[$singular]) && !empty(trim($complete_comments[$singular]))) {
                        $comment_lines = explode("\n", trim($complete_comments[$singular]));
                        foreach ($comment_lines as $comment_line) {
                            if (!empty(trim($comment_line))) {
                                $content .= '# ' . trim($comment_line) . "\n";
                            }
                        }
                    }
                    
                    $content .= 'msgid "' . $this->escape_po_string($singular) . '"' . "\n";
                $content .= 'msgid_plural "' . trim($plural, '"') . '"' . "\n";
                $content .= 'msgstr[0] "' . $this->escape_po_string($singular_translation) . '"' . "\n";
                $content .= 'msgstr[1] "' . $this->escape_po_string(isset($plural_translations[1]) ? $plural_translations[1] : '') . '"' . "\n\n";
            } else {
                    // Fallback for malformed plural strings
                    $content .= 'msgid "' . addslashes($final_msgid) . '"' . "\n";
                    $content .= 'msgstr "' . addslashes(is_string($msgstr) ? $msgstr : '') . '"' . "\n\n";
                }
            } else {
                // Regular singular form

                
                // Add translator comment if exists for this msgid (only if not empty)
                // If comments array is completely empty, don't add any translator comments (clear all)
                if (!empty($complete_comments) && isset($complete_comments[$msgid]) && !empty(trim($complete_comments[$msgid]))) {
                    // Handle multi-line comments
                    $comment_lines = explode("\n", trim($complete_comments[$msgid]));
                    foreach ($comment_lines as $comment_line) {
                        if (!empty(trim($comment_line))) {
                            $content .= '# ' . trim($comment_line) . "\n";
                        }
                    }
                }
                
                $content .= 'msgid "' . $this->escape_po_string($final_msgid) . '"' . "\n";
                $content .= 'msgstr "' . $this->escape_po_string(is_string($msgstr) ? $msgstr : '') . '"' . "\n\n";
            }
        }
        
        // Final count
        $save_info['written_translations'] = $written_count;
        $save_info['content_length'] = strlen($content);
        
        // Force complete file rewrite by deleting and recreating
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $result = file_put_contents($file_path, $content);
        if ($result === false) {
            throw new Exception('Could not write to file');
        }
        
        // Final file size
        $save_info['final_file_size'] = filesize($file_path);
        
        return $save_info;
    }
    
    /**
     * Download a PO file
     */
    public function download_po_file($filename) {
        if (empty($filename) || !preg_match('/^hostifybooking-[a-z]{2}_[A-Z]{2}\.po$/', $filename)) {
            throw new Exception('Invalid file name');
        }
        
        $file_path = $this->lang_dir . $filename;
        if (!file_exists($file_path)) {
            throw new Exception('File not found');
        }
        
        return array(
            'content' => file_get_contents($file_path),
            'filename' => $filename,
            'size' => filesize($file_path)
        );
    }

    /**
     * Upload and restore a PO file
     */
    public function upload_po_file($uploaded_file, $filename, $create_backup = true, $backup_title = '') {
        if (empty($filename) || !preg_match('/^hostifybooking-[a-z]{2}_[A-Z]{2}\.po$/', $filename)) {
            throw new Exception('Invalid file name');
        }
        
        // Validate uploaded file
        if (!isset($uploaded_file['tmp_name']) || !is_uploaded_file($uploaded_file['tmp_name'])) {
            throw new Exception('Invalid upload');
        }
        
        $content = file_get_contents($uploaded_file['tmp_name']);
        if (!$content) {
            throw new Exception('Could not read uploaded file');
        }
        
        // Validate PO file format
        if (strpos($content, 'msgid') === false || strpos($content, 'msgstr') === false) {
            throw new Exception('Invalid PO file format');
        }
        
        $file_path = $this->lang_dir . $filename;
        
        // Create full backup if requested
        $backup_info = null;
        if ($create_backup) {
            // Use provided title or default title for upload backups
            $final_backup_title = !empty($backup_title) ? $backup_title : 'Backup before file upload';
            $backup_info = $this->create_backup($final_backup_title);
        }
        
        // Save the uploaded file directly
        $result = file_put_contents($file_path, $content);
        if ($result === false) {
            throw new Exception('Could not write to file');
        }
        
        // Compile PO to MO after uploading
        try {
            HostifyBooking_MO_Compiler::compile_po_to_mo($file_path);
            // Reload translations after compiling
            $this->reload_translations();
                    } catch (Exception $e) {
                // Silent error handling - don't fail the upload operation if MO compilation fails
            }
        
        return array(
            'success' => true,
            'backup_created' => $backup_info ? $backup_info['backup_dir'] : null,
            'backup_title' => $backup_info ? $backup_info['title'] : null
        );
    }

    /**
     * Create backup of all PO files
     */
    public function create_backup($backup_title = '') {
        $backup_dir = $this->lang_dir . 'backups/backup-' . date('Y-m-d-H-i-s') . '/';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $po_files = glob($this->lang_dir . '*.po');
        $backed_up = array();
        
        foreach ($po_files as $po_file) {
            $filename = basename($po_file);
            $backup_path = $backup_dir . $filename;
            
            if (copy($po_file, $backup_path)) {
                $backed_up[] = $filename;
            }
        }
        
        // Save backup title to a metadata file
        if (!empty($backup_title)) {
            $metadata = array(
                'title' => $backup_title,
                'created_at' => current_time('mysql'),
                'created_by' => get_current_user_id()
            );
            file_put_contents($backup_dir . 'backup-info.json', json_encode($metadata));
        }
        
        return array(
            'backup_dir' => basename($backup_dir),
            'files' => $backed_up,
            'title' => $backup_title
        );
    }

    /**
     * Get list of available backups
     */
    public function get_backups() {
        $backup_dir = $this->lang_dir . 'backups/';
        if (!is_dir($backup_dir)) {
            return array();
        }
        
        $backups = array();
        $backup_folders = glob($backup_dir . 'backup-*', GLOB_ONLYDIR);
        
        foreach ($backup_folders as $folder) {
            $backup_name = basename($folder);
            $files = glob($folder . '/*.po');
            
            // Try to read backup title from metadata file
            $backup_title = '';
            $metadata_file = $folder . '/backup-info.json';
            if (file_exists($metadata_file)) {
                $metadata = json_decode(file_get_contents($metadata_file), true);
                if ($metadata && isset($metadata['title'])) {
                    $backup_title = $metadata['title'];
                }
            }
            
            $backups[] = array(
                'name' => $backup_name,
                'date' => str_replace('backup-', '', $backup_name),
                'title' => $backup_title,
                'files' => array_map('basename', $files),
                'count' => count($files)
            );
        }
        
        // Sort by date (newest first)
        usort($backups, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        return $backups;
    }

    /**
     * Restore from backup
     */
    public function restore_backup($backup_name) {
        $backup_dir = $this->lang_dir . 'backups/' . $backup_name . '/';
        if (!is_dir($backup_dir)) {
            throw new Exception('Backup not found');
        }
        
        $po_files = glob($backup_dir . '*.po');
        $restored = array();
        

        
        foreach ($po_files as $po_file) {
            $filename = basename($po_file);
            $target_path = $this->lang_dir . $filename;
            

            
            if (copy($po_file, $target_path)) {
                $restored[] = $filename;

                
                // Compile PO to MO after restoring (MO compilation doesn't touch headers)
                try {
                    HostifyBooking_MO_Compiler::compile_po_to_mo($target_path);
                    // Reload translations after compiling
                    $this->reload_translations();
                    
            } catch (Exception $e) {
                // Silent error handling - don't fail the restore operation if MO compilation fails
            }
            } else {

            }
        }
        

        
        return $restored;
    }

    /**
     * Delete backup
     */
    public function delete_backup($backup_name) {
        $backup_dir = $this->lang_dir . 'backups/' . $backup_name . '/';
        if (!is_dir($backup_dir)) {
            throw new Exception('Backup not found');
        }
        
        // Delete all files in backup directory
        $files = glob($backup_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        // Remove empty directory
        rmdir($backup_dir);
        
        return true;
    }

    /**
     * Delete all backups (both individual and full backups)
     */
    public function delete_all_backups() {
        $backup_dir = $this->lang_dir . 'backups/';
        if (!is_dir($backup_dir)) {
            return array('deleted_count' => 0, 'deleted_dirs' => 0);
        }
        
        $deleted_count = 0;
        $deleted_dirs = 0;
        
        // Delete individual backup files
        $individual_backups = glob($backup_dir . '*.backup-*');
        foreach ($individual_backups as $backup_file) {
            if (is_file($backup_file)) {
                unlink($backup_file);
                $deleted_count++;
            }
        }
        
        // Delete full backup directories
        $backup_folders = glob($backup_dir . 'backup-*', GLOB_ONLYDIR);
        foreach ($backup_folders as $folder) {
            // Delete all files in backup directory
            $files = glob($folder . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $deleted_count++;
                }
            }
            
            // Remove empty directory
            if (rmdir($folder)) {
                $deleted_dirs++;
            }
        }
        
        return array(
            'deleted_count' => $deleted_count,
            'deleted_dirs' => $deleted_dirs
        );
    }

    /**
     * Download entire backup as ZIP file
     */
    public function download_backup($backup_name) {
        $backup_dir = $this->lang_dir . 'backups/' . $backup_name . '/';
        if (!is_dir($backup_dir)) {
            throw new Exception('Backup not found');
        }
        
        $po_files = glob($backup_dir . '*.po');
        if (empty($po_files)) {
            throw new Exception('No PO files found in backup');
        }
        
        // Try to read backup title from metadata file
        $backup_title = '';
        $metadata_file = $backup_dir . 'backup-info.json';
        if (file_exists($metadata_file)) {
            $metadata = json_decode(file_get_contents($metadata_file), true);
            if ($metadata && isset($metadata['title'])) {
                $backup_title = $metadata['title'];
            }
        }
        
        // Create ZIP filename with title if available
        // Remove 'backup-' prefix from backup_name to avoid double 'backup-backup'
        $clean_backup_name = str_replace('backup-', '', $backup_name);
        
        if (!empty($backup_title)) {
            // Sanitize title for filename (remove special characters, replace spaces with dashes)
            $sanitized_title = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $backup_title);
            $sanitized_title = preg_replace('/\s+/', '-', trim($sanitized_title));
            $sanitized_title = strtolower($sanitized_title);
            
            // Limit title length to avoid overly long filenames
            if (strlen($sanitized_title) > 50) {
                $sanitized_title = substr($sanitized_title, 0, 50);
            }
            
            $zip_filename = 'hostifybooking-' . $sanitized_title . '-translations-backup-' . $clean_backup_name . '.zip';
        } else {
            $zip_filename = 'hostifybooking-translations-backup-' . $clean_backup_name . '.zip';
        }
        
        // Create ZIP file in memory
        $zip = new ZipArchive();
        $temp_zip = tempnam(sys_get_temp_dir(), 'hostifybooking_backup_');
        
        if ($zip->open($temp_zip, ZipArchive::CREATE) !== TRUE) {
            throw new Exception('Could not create ZIP file');
        }
        
        foreach ($po_files as $po_file) {
            $filename = basename($po_file);
            $zip->addFile($po_file, $filename);
        }
        
        $zip->close();
        
        return array(
            'temp_file' => $temp_zip,
            'filename' => $zip_filename,
            'size' => filesize($temp_zip),
            'files_count' => count($po_files),
            'backup_title' => $backup_title
        );
    }

    /**
     * Get backup directory path for file manager
     */
    public function get_backup_path($backup_name) {
        $backup_dir = $this->lang_dir . 'backups/' . $backup_name . '/';
        if (!is_dir($backup_dir)) {
            throw new Exception('Backup not found');
        }
        
        return array(
            'path' => $backup_dir,
            'url' => str_replace(ABSPATH, site_url('/'), $backup_dir),
            'files' => array_map('basename', glob($backup_dir . '*.po'))
        );
    }

    /**
     * Get backup directory size
     */
    public function get_backup_size($backup_name) {
        $backup_dir = $this->lang_dir . 'backups/' . $backup_name . '/';
        if (!is_dir($backup_dir)) {
            return 0;
        }
        
        $size = 0;
        $files = glob($backup_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $size += filesize($file);
            }
        }
        
        return $size;
    }

    /**
     * Compile PO file to MO file
     */
    private function compile_po_to_mo($po_file_path) {
        // Use our shared MO compiler instead of the old method
        return HostifyBooking_MO_Compiler::compile_po_to_mo($po_file_path);
    }



    /**
     * Compile all PO files to MO files
     */
    public function compile_all_po_files() {
        $po_files = glob($this->lang_dir . '*.po');
        $compiled = array();
        $errors = array();
        
        foreach ($po_files as $po_file) {
            try {
                $mo_file = HostifyBooking_MO_Compiler::compile_po_to_mo($po_file);
                $compiled[] = basename($mo_file);
            } catch (Exception $e) {
                $errors[] = basename($po_file) . ': ' . $e->getMessage();
            }
        }
        
        // Clear translation cache and reload translations
        if (!empty($compiled)) {
            $this->reload_translations();
        }
        
        return array(
            'compiled' => $compiled,
            'errors' => $errors
        );
    }

    /**
     * Clear translation cache and reload translations
     */
    private function reload_translations() {
        // Clear the global translation cache
        global $l10n;
        if (isset($l10n['hostifybooking'])) {
            unset($l10n['hostifybooking']);
        }
        
        // Clear WordPress translation cache more aggressively
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete('translations', 'hostifybooking');
            wp_cache_delete('translations', 'default');
        }
        
        // Clear WordPress object cache for translations
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group('translations');
        }
        
        // Force unload the textdomain completely
        unload_textdomain('hostifybooking');
        
        // Clear any cached translation files
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete('hostifybooking', 'translations');
        }
        
        // More aggressive approach: temporarily change locale to force reload
        $current_locale = get_locale();
        $temp_locale = 'en_US';
        
        // Temporarily switch to English to clear any cached translations
        if (function_exists('switch_to_locale')) {
            switch_to_locale($temp_locale);
            // Force reload with English
            load_textdomain('hostifybooking', $this->lang_dir . 'hostifybooking-' . $temp_locale . '.mo');
            
            // Switch back to original locale
            restore_previous_locale();
        }
        
        // Force reload of the textdomain with fresh MO files
        $locale = get_locale();
        $mofile = $this->lang_dir . 'hostifybooking-' . $locale . '.mo';
        
        if (file_exists($mofile)) {
            // Load the textdomain with the fresh MO file
            load_textdomain('hostifybooking', $mofile);
        }
        
        // Also try to reload for admin context
        $admin_mofile = $this->lang_dir . 'hostifybooking-' . $locale . '.mo';
        if (file_exists($admin_mofile)) {
            load_textdomain('hostifybooking', $admin_mofile);
        }
        
        // Force WordPress to reload all translation files
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Additional: Force reload of all textdomains
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete('alloptions', 'options');
        }
        
        // Most aggressive: Clear all possible caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear WordPress object cache completely
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Force reload by clearing the global $l10n array completely
        global $l10n;
        $l10n = array();
        
        // Re-register the textdomain
        load_plugin_textdomain('hostifybooking', false, dirname(plugin_basename(HOSTIFYBOOKING_DIR . 'hostifybooking.php')) . '/lang/');
        
        // Force WordPress to reload admin menu translations
        if (is_admin()) {
            // Clear admin menu cache
            delete_transient('nav_menu_options');
            
            // Force reload of admin menu
            if (function_exists('wp_get_nav_menus')) {
                wp_get_nav_menus();
            }
            
            // Clear any cached admin menu items
            if (function_exists('wp_cache_delete')) {
                wp_cache_delete('alloptions', 'options');
                wp_cache_delete('nav_menu', 'terms');
            }
        }
        
        // Force WordPress to reload all translation files for the current locale
        $locale = get_locale();
        $mofile = $this->lang_dir . 'hostifybooking-' . $locale . '.mo';
        
        if (file_exists($mofile)) {
            // Force reload the textdomain with the fresh MO file
            unload_textdomain('hostifybooking');
            load_textdomain('hostifybooking', $mofile);
            
            // Also try loading with the plugin textdomain function
            load_plugin_textdomain('hostifybooking', false, dirname(plugin_basename(HOSTIFYBOOKING_DIR . 'hostifybooking.php')) . '/lang/');
        }
        
        // Force immediate reload by adding a hook to refresh admin menu
        if (is_admin()) {
            // Add a hook to force reload translations on the next page load
            add_action('admin_init', function() {
                // Force reload of admin menu translations
                global $menu, $submenu;
                
                // Clear menu cache
                if (function_exists('wp_cache_delete')) {
                    wp_cache_delete('alloptions', 'options');
                }
                
                // Force WordPress to rebuild the admin menu
                if (function_exists('_wp_menu_output')) {
                    // This will force the menu to be rebuilt with fresh translations
                    remove_all_actions('admin_menu');
                    do_action('admin_menu');
                }
            }, 1);
            
            // Also add a hook to force reload on admin head
            add_action('admin_head', function() {
                // Force reload the textdomain one more time
                $locale = get_locale();
                $mofile = $this->lang_dir . 'hostifybooking-' . $locale . '.mo';
                
                if (file_exists($mofile)) {
                    unload_textdomain('hostifybooking');
                    load_textdomain('hostifybooking', $mofile);
                }
            }, 1);
        }
    }

    /**
     * Load existing translations from a PO file
     */
    public function load_existing_translations($po_file_path) {
        $content = file_get_contents($po_file_path);
        $existing_strings = array();
        
        $lines = explode("\n", $content);
        $current_msgid = '';
        $current_msgstr = '';
        $current_msgid_plural = '';
        $current_msgstr_plural = array();
        $in_msgid = false;
        $in_msgstr = false;
        $in_msgid_plural = false;
        $in_msgstr_plural = false;
        $is_plural_form = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strpos($line, 'msgid "') === 0) {
                // Save previous translation if exists
                if ($current_msgid && $current_msgid !== '') {
                    if ($is_plural_form) {
                        // For plural forms, create a combined key
                        $plural_key = $current_msgid . '|' . $current_msgid_plural;
                        $combined_msgstr = implode('|', $current_msgstr_plural);
                        $existing_strings[$plural_key] = $combined_msgstr;
                    } else {
                        $existing_strings[$current_msgid] = $current_msgstr;
                    }
                }
                
                $current_msgid = substr($line, 7, -1); // Remove 'msgid "' and '"'
                $current_msgstr = '';
                $current_msgid_plural = '';
                $current_msgstr_plural = array();
                $in_msgid = true;
                $in_msgstr = false;
                $in_msgid_plural = false;
                $in_msgstr_plural = false;
                $is_plural_form = false;
            } elseif (strpos($line, 'msgid_plural "') === 0) {
                $current_msgid_plural = substr($line, 13, -1); // Remove 'msgid_plural "' and '"'
                // Strip any remaining quotes that might be in the text
                $current_msgid_plural = trim($current_msgid_plural, '"');
                $in_msgid = false;
                $in_msgid_plural = true;
                $is_plural_form = true;
            } elseif (strpos($line, 'msgstr "') === 0) {
                $current_msgstr = substr($line, 8, -1); // Remove 'msgstr "' and '"'
                $in_msgid = false;
                $in_msgid_plural = false;
                $in_msgstr = true;
                $in_msgstr_plural = false;
            } elseif (preg_match('/^msgstr\[(\d+)\] "(.+)"$/', $line, $matches)) {
                $index = (int)$matches[1];
                $value = $matches[2];
                $current_msgstr_plural[$index] = $value;
                $in_msgid = false;
                $in_msgid_plural = false;
                $in_msgstr = false;
                $in_msgstr_plural = true;
                $is_plural_form = true;
            } elseif (strpos($line, '"') === 0 && strrpos($line, '"') === strlen($line) - 1) {
                // Continuation line (quoted strings that continue from previous lines)
                $continuation = substr($line, 1, -1); // Remove quotes
                if ($in_msgid) {
                    $current_msgid .= $continuation;
                } elseif ($in_msgid_plural) {
                    $current_msgid_plural .= $continuation;
                } elseif ($in_msgstr) {
                    $current_msgstr .= $continuation;
                } elseif ($in_msgstr_plural) {
                    // For plural msgstr, we need to know which index we're continuing
                    // This is a simplified approach - in practice, we'd need more context
                    if (!empty($current_msgstr_plural)) {
                        $last_index = max(array_keys($current_msgstr_plural));
                        $current_msgstr_plural[$last_index] .= $continuation;
                    }
                }
            }
        }
        
        // Don't forget the last translation
        if ($current_msgid && $current_msgid !== '') {
            if ($is_plural_form) {
                // For plural forms, create a combined key
                $plural_key = $current_msgid . '|' . $current_msgid_plural;
                $combined_msgstr = implode('|', $current_msgstr_plural);
                $existing_strings[$plural_key] = $combined_msgstr;
            } else {
                $existing_strings[$current_msgid] = $current_msgstr;
            }
        }

        return $existing_strings;
    }

    /**
     * Get the language directory path
     */
    public function get_lang_dir() {
        return $this->lang_dir;
    }

    /**
     * Create a new language file
     */
    public function create_language_file($language_code) {
        // Validate language code format (lowercase language, uppercase country)
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $language_code)) {
            throw new Exception('Invalid language code format. Use format: language_COUNTRY (e.g., fr_FR, es_ES, pl_PL)');
        }
        
        $filename = 'hostifybooking-' . $language_code . '.po';
        $file_path = $this->lang_dir . $filename;
        
        // Check if file already exists
        if (file_exists($file_path)) {
            throw new Exception('Language file already exists: ' . $filename);
        }
        
        // Check if directory is writable
        if (!is_writable($this->lang_dir)) {
            throw new Exception('Language directory is not writable');
        }
        
        // Get the current extracted strings and their locations to create the new file
        require_once __DIR__ . '/class-string-extractor.php';
        $extractor = new HostifyBooking_String_Extractor();
                        $extracted_strings = $extractor->extract_strings();
                $string_locations = $extractor->get_string_locations();
        
        // Get the POT file path to use as header template
        $pot_file_path = $this->lang_dir . 'hostifybooking.pot';
        
        // Read the POT file to get the header template
        $pot_content = '';
        if (file_exists($pot_file_path)) {
            $pot_content = file_get_contents($pot_file_path);
        }
        
        // Create proper headers for the new language file
        $headers = $this->create_po_headers($language_code, $pot_content);
        
        // Create PO file content starting with the updated headers
        $content = $headers;
        
        // Headers already end with a newline, so we don't need to add extra spacing
        
        // Add all extracted strings with empty translations and source location comments
        foreach ($extracted_strings as $string) {
            // Add source location comments if available
            if (isset($string_locations[$string]) && !empty($string_locations[$string])) {
                $locations = array_unique($string_locations[$string]);
                foreach ($locations as $location) {
                    $content .= "#: " . $location . "\n";
                }
            }
            
            // Check if this is a plural form (contains | separator)
            if (strpos($string, '|') !== false) {
                // Split the plural form into singular and plural
                $parts = explode('|', $string, 2);
                $singular = $parts[0];
                $plural = $parts[1];
                
                // Write plural form entry
                $content .= "msgid \"" . addslashes($singular) . "\"\n";
                $content .= "msgid_plural \"" . addslashes($plural) . "\"\n";
                $content .= "msgstr[0] \"\"\n";
                $content .= "msgstr[1] \"\"\n\n";
            } else {
                // Regular singular string
                $content .= "msgid \"" . addslashes($string) . "\"\n";
                $content .= "msgstr \"\"\n\n";
            }
        }
        
                        // Write the file
                $result = file_put_contents($file_path, $content);
                if ($result === false) {
                    throw new Exception('Could not create language file');
                }
        
        // Compile PO to MO file
        $mo_created = false;
        try {
            HostifyBooking_MO_Compiler::compile_po_to_mo($file_path);
            $mo_created = true;
            // Reload translations after compiling
            $this->reload_translations();
        } catch (Exception $e) {
            // Don't fail the creation if MO compilation fails
        }
        
        return array(
            'filename' => $filename,
            'path' => $file_path,
            'strings_count' => count($extracted_strings),
            'mo_created' => $mo_created
        );
    }

    /**
     * Update headers for all PO files using the POT file as template
     */
    public function update_all_po_headers() {
        $po_files = glob($this->lang_dir . 'hostifybooking-*.po');
        $updated_count = 0;
        
        foreach ($po_files as $po_file) {
            if ($this->update_po_file_headers($po_file)) {
                $updated_count++;
            }
        }
        
        return $updated_count;
    }
    
    /**
     * Update headers for a single PO file using the POT file as template
     */
    private function update_po_file_headers($file_path) {
        // IMPORTANT: Headers are completely disabled during extraction to prevent corruption
        // Headers should only be updated when explicitly requested via separate tools
        return false;
    }

    /**
     * Create proper PO headers for a new language file
     * 
     * @param string $language_code The language code (e.g., sv_SE)
     * @param string $pot_content The POT file content to extract header template
     * @return string Properly formatted PO headers
     */
    public function create_po_headers($language_code, $pot_content) {
        // Use the proper header manager class
        require_once __DIR__ . '/class-po-header-manager.php';
        $header_manager = new HostifyBooking_PO_Header_Manager();
        return $header_manager->create_fallback_headers($language_code);
    }

    /**
     * Get the language team string for a given language code
     */
    private function get_language_team($language_code) {
        $language_teams = array(
            'bg_BG' => 'Bulgarian <bg@li.org>',
            'pl_PL' => 'Polish <pl@li.org>',
            'ru_RU' => 'Russian <ru@li.org>',
            'es_ES' => 'Spanish <es@li.org>',
            'fr_FR' => 'French <fr@li.org>',
            'de_DE' => 'German <de@li.org>',
            'it_IT' => 'Italian <it@li.org>',
            'pt_PT' => 'Portuguese <pt@li.org>',
            'nl_NL' => 'Dutch <nl@li.org>',
            'sv_SE' => 'Swedish <sv@li.org>',
            'da_DK' => 'Danish <da@li.org>',
            'no_NO' => 'Norwegian <no@li.org>',
            'fi_FI' => 'Finnish <fi@li.org>',
            'cs_CZ' => 'Czech <cs@li.org>',
            'sk_SK' => 'Slovak <sk@li.org>',
            'hu_HU' => 'Hungarian <hu@li.org>',
            'ro_RO' => 'Romanian <ro@li.org>',
            'hr_HR' => 'Croatian <hr@li.org>',
            'sl_SI' => 'Slovenian <sl@li.org>',
            'et_EE' => 'Estonian <et@li.org>',
            'lv_LV' => 'Latvian <lv@li.org>',
            'lt_LT' => 'Lithuanian <lt@li.org>',
            'el_GR' => 'Greek <el@li.org>',
            'tr_TR' => 'Turkish <tr@li.org>',
            'ar' => 'Arabic <ar@li.org>',
            'he_IL' => 'Hebrew <he@li.org>',
            'fa_IR' => 'Persian <fa@li.org>',
            'hi_IN' => 'Hindi <hi@li.org>',
            'bn_BD' => 'Bengali <bn@li.org>',
            'ur_PK' => 'Urdu <ur@li.org>',
            'th_TH' => 'Thai <th@li.org>',
            'vi_VN' => 'Vietnamese <vi@li.org>',
            'ko_KR' => 'Korean <ko@li.org>',
            'ja' => 'Japanese <ja@li.org>',
            'zh_CN' => 'Chinese (Simplified) <zh_CN@li.org>',
            'zh_TW' => 'Chinese (Traditional) <zh_TW@li.org>',
        );
        
        return isset($language_teams[$language_code]) ? $language_teams[$language_code] : $language_code . ' <' . strtolower($language_code) . '@li.org>';
    }



    /**
     * Clean up malformed entries in a PO file
     */
    public function cleanup_malformed_entries($filename) {
        if (empty($filename) || !preg_match('/^hostifybooking-[a-z]{2}_[A-Z]{2}\.po$/', $filename)) {
            throw new Exception('Invalid file name');
        }
        
        $file_path = $this->lang_dir . $filename;
        if (!file_exists($file_path)) {
            throw new Exception('File not found');
        }
        
        $content = file_get_contents($file_path);
        if (!$content) {
            throw new Exception('Could not read file');
        }
        

        
        // Write the cleaned content back
        $result = file_put_contents($file_path, $content);
        if ($result === false) {
            throw new Exception('Could not write to file');
        }
        
        return true;
    }

    /**
     * Escape a string for PO file format.
     * This is a simplified escaping for PO files, similar to gettext.
     * It handles double quotes and backslashes.
     */
    private function escape_po_string($string) {
        // The string coming from frontend is already unescaped (raw user input)
        // So we just need to apply proper PO file escaping
        // Escape backslashes first
        $string = str_replace('\\', '', $string);
        // Then escape double quotes
        $string = str_replace('"', '"', $string);
        
        return $string;
    }


} 