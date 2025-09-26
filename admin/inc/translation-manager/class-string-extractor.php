<?php

/**
 * String Extractor for Hostify Booking Plugin
 * Extracts all translatable strings from the plugin and updates translation files
 */
require_once __DIR__ . '/class-mo-compiler.php';

class HostifyBooking_String_Extractor {

    private $plugin_dir;
    private $lang_dir;
    private $text_domain = 'hostifybooking';
    private $extracted_strings = array();
    private $string_locations = array(); // Track source locations for each string

    /**
     * Get string locations for testing
     */
    public function get_string_locations() {
        return $this->string_locations;
    }

    public function __construct() {
        $this->plugin_dir = dirname(__FILE__, 4) . '/';
        $this->lang_dir = $this->plugin_dir . 'lang/';
    }

    /**
     * Extract all translatable strings from the plugin
     */
    public function extract_strings() {
        $this->extracted_strings = array();
        $this->string_locations = array(); // Reset locations
        
        // Scan all PHP files in the plugin directory
        $php_files = $this->get_php_files($this->plugin_dir);
        
        foreach ($php_files as $file) {
            $this->extract_strings_from_file($file);
        }
        
        // Remove duplicates and sort
        $this->extracted_strings = array_unique($this->extracted_strings);
        sort($this->extracted_strings);
        
        // Add manual strings that might not be found by regex
        $this->add_manual_strings();
        
        // Remove duplicates again and sort
        $this->extracted_strings = array_unique($this->extracted_strings);
        sort($this->extracted_strings);
        
        return $this->extracted_strings;
    }

    /**
     * Get all PHP files in a directory recursively
     */
    private function get_php_files($dir) {
        $files = array();
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filepath = $file->getPathname();
                $filename = basename($filepath);
                
                // Exclude files that shouldn't be scanned
                if ($filename === 'strings_to_translate.php' ||
                    strpos($filepath, '/vendor/') !== false ||
                    strpos($filepath, '/node_modules/') !== false ||
                    strpos($filepath, '/.git/') !== false ||
                    strpos($filepath, '/Backup/') !== false ||
                    strpos($filepath, '/backup/') !== false ||
                    strpos($filepath, '/tests/') !== false ||
                    strpos($filepath, '/test/') !== false) {
                    continue;
                }
                $files[] = $filepath;
            }
        }
        
        return $files;
    }

    /**
     * Extract translatable strings from a single file
     */
    private function extract_strings_from_file($file_path) {
        $content = file_get_contents($file_path);
        
        if (!$content) {
            return;
        }

        $lines = explode("\n", $content);
        $relative_path = str_replace($this->plugin_dir, '', $file_path);

        // Pattern for __( 'string', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/__\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for _e( 'string', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/_e\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for esc_html__( 'string', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/esc_html__\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for esc_attr__( 'string', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/esc_attr__\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for esc_html_e( 'string', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/esc_html_e\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for esc_attr_e( 'string', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/esc_attr_e\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for _x( 'string', 'context', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/_x\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for _ex( 'string', 'context', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/_ex\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for esc_html_x( 'string', 'context', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/esc_html_x\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for esc_attr_x( 'string', 'context', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/esc_attr_x\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for sprintf( __( 'string', 'hostifybooking' ), ... ) - handle both single and double quotes
        // But exclude _n() calls which are handled separately
        $this->extract_strings_with_locations($lines, $relative_path, '/sprintf\(\s*(?!_n\()__\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for sprintf( _x( 'string', 'context', 'hostifybooking' ), ... ) - handle both single and double quotes
        $this->extract_strings_with_locations($lines, $relative_path, '/sprintf\(\s*_x\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for _n( 'singular', 'plural', count, 'hostifybooking' ) - handle both single and double quotes
        $this->extract_plural_strings_with_locations($lines, $relative_path, '/_n\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*,\s*[^,]+,\s*[\'"]hostifybooking[\'"]\s*\)/');

        // Pattern for _nx( 'singular', 'plural', count, 'context', 'hostifybooking' ) - handle both single and double quotes
        $this->extract_plural_strings_with_locations($lines, $relative_path, '/_nx\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*,\s*[^,]+,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]hostifybooking[\'"]\s*\)/');
    }

    /**
     * Extract strings with their line numbers and file locations
     */
    private function extract_strings_with_locations($lines, $relative_path, $pattern) {
        foreach ($lines as $line_number => $line) {
            $line_number++; // Convert to 1-based line numbers
            preg_match_all($pattern, $line, $matches, PREG_OFFSET_CAPTURE);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $string = $this->normalize_quotes($match[0]);
                    
                    // Skip empty strings
                    if (empty(trim($string))) {
                        continue;
                    }

                    // Only add the string once to the main array
                    if (!in_array($string, $this->extracted_strings)) {
                        $this->extracted_strings[] = $string;
                    }
                    // Store location information (avoid duplicates)
                    if (!isset($this->string_locations[$string])) {
                        $this->string_locations[$string] = array();
                    }
                    $location = $relative_path . ':' . $line_number;
                    if (!in_array($location, $this->string_locations[$string])) {
                        $this->string_locations[$string][] = $location;
                    }
                }
            }
        }
    }

    /**
     * Extract plural strings with their line numbers and file locations
     */
    private function extract_plural_strings_with_locations($lines, $relative_path, $pattern) {
        foreach ($lines as $line_number => $line) {
            $line_number++; // Convert to 1-based line numbers
            preg_match_all($pattern, $line, $matches, PREG_OFFSET_CAPTURE);
            
            if (!empty($matches[1]) && !empty($matches[2])) {
                foreach ($matches[1] as $index => $singular_match) {
                    $plural_match = $matches[2][$index];
                    $singular = $this->normalize_quotes($singular_match[0]);
                    $plural = $this->normalize_quotes($plural_match[0]);
                    
                    // Skip empty strings
                    if (empty(trim($singular)) || empty(trim($plural))) {
                        continue;
                    }
                    
                    // Create a combined key for plural forms
                    $plural_key = $singular . '|' . $plural;
                    
                    // Only add the plural key once to the main array
                    if (!in_array($plural_key, $this->extracted_strings)) {
                        $this->extracted_strings[] = $plural_key;
                    }
                    
                    // Store location information (avoid duplicates)
                    if (!isset($this->string_locations[$plural_key])) {
                        $this->string_locations[$plural_key] = array();
                    }
                    
                    $location = $relative_path . ':' . $line_number;
                    if (!in_array($location, $this->string_locations[$plural_key])) {
                        $this->string_locations[$plural_key][] = $location;
                    }
                }
            }
        }
    }

    /**
     * Normalize quotes to straight quotes for consistency
     */
    private function normalize_quotes($string) {
        // Replace curly quotes with straight quotes
        $string = str_replace(
            array("\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x98", "\xE2\x80\x99"), // Unicode for curly quotes
            array('"', '"', "'", "'"),
            $string
        );
        return $string;
    }

    /**
     * Add manually defined strings that might not be found by regex patterns
     */
    private function add_manual_strings() {
        $manual_strings = array(
            'Translations saved successfully!',
            'Loaded %d translations from %s',
            'Compiling MO files...',
            'Successfully compiled %d MO files.',
            'Errors: %s',
            'Failed to compile MO files',
            'Extracting...',
            'Extract Strings',
            'Extraction completed successfully!',
            'Total strings found: %d',
            'Files with new strings added: %s',
            'Files updated with source location comments: %s',
            'PO files with updated headers: %d files',
            'Missing Translations Report:',
            '%s: %d missing translations (%d/%d translated - %s%%)',
            'Uploading...',
            'Upload & Restore',
            'File uploaded and restored successfully!',
            'Backup created: %s',
            'Compiling...',
            'Compile MO Files'
        );
        
        foreach ($manual_strings as $string) {
            if (!in_array($string, $this->extracted_strings)) {
                $this->extracted_strings[] = $string;
                
                // Add location for manual strings
                if (!isset($this->string_locations[$string])) {
                    $this->string_locations[$string] = array();
                }
                $this->string_locations[$string][] = 'admin/inc/translation-manager/translation-manager.php:67-87';
            }
        }
    }



    /**
     * Update the strings_to_translate.php file
     */
    public function update_strings_file() {
        $strings_file = $this->lang_dir . 'strings_to_translate.php';
        $content = "<?php\n";
        
        foreach ($this->extracted_strings as $string) {
            $content .= "__('" . addslashes($string) . "', 'hostifybooking');\n";
        }
        
        file_put_contents($strings_file, $content);
        
        return $strings_file;
    }

    /**
     * Update POT file
     */
    public function update_pot_file() {
        // First extract strings to populate locations
        $this->extract_strings();
        
        $pot_file = $this->lang_dir . 'hostifybooking.pot';
        
        // Preserve the original POT-Creation-Date if the file already exists
        $original_creation_date = null;
        if (file_exists($pot_file)) {
            $pot_content = file_get_contents($pot_file);
            if (preg_match('/POT-Creation-Date: ([^\n]+)/', $pot_content, $matches)) {
                $original_creation_date = $matches[1];
        
            } else {
        
            }
        } else {
    
        }
        
        $content = $this->generate_pot_content($original_creation_date);
        
        file_put_contents($pot_file, $content);
        
        return $pot_file;
    }

    /**
     * Generate POT file content
     */
    private function generate_pot_content($original_creation_date = null) {
        $content = "# Copyright (C) 2025 Hostify Booking Engine\n";
        $content .= "# This file is distributed under the same license as the Hostify Booking Engine package.\n";
        $content .= "msgid \"\"\n";
        $content .= "msgstr \"\"\n";
        $content .= "\"Project-Id-Version: Hostify Booking Engine\\n\"\n";
        $content .= "\"Report-Msgid-Bugs-To: https://hostify.com\\n\"\n";
        
        // Use original creation date if provided, otherwise use current date
        if ($original_creation_date) {
            $content .= "\"POT-Creation-Date: " . $original_creation_date . "\\n\"\n";
    
        } else {
            $current_date = date('Y-m-d H:i:sO');
            $content .= "\"POT-Creation-Date: " . $current_date . "\\n\"\n";
    
        }
        
        $content .= "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n";
        $content .= "\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"\n";
        $content .= "\"Language-Team: LANGUAGE <LL@li.org>\\n\"\n";
        $content .= "\"MIME-Version: 1.0\\n\"\n";
        $content .= "\"Content-Type: text/plain; charset=UTF-8\\n\"\n";
        $content .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
        $content .= "\"X-Generator: Hostify Booking Engine String Extractor\\n\"\n\n";

        foreach ($this->extracted_strings as $string) {
            
            // Add source location comments if available
            if (isset($this->string_locations[$string]) && !empty($this->string_locations[$string])) {
                $locations = array_unique($this->string_locations[$string]);
                foreach ($locations as $location) {
                    $content .= "#: " . $location . "\n";
                }
            }
            
            // Check if this is a plural form (contains | separator)
            if (strpos($string, '|') !== false) {
                $parts = explode('|', $string);
                if (count($parts) === 2) {
                    $singular = $parts[0];
                    $plural = $parts[1];
                    $content .= "msgid \"" . addslashes($singular) . "\"\n";
                    $content .= "msgid_plural \"" . addslashes($plural) . "\"\n";
                    $content .= "msgstr[0] \"\"\n";
                    $content .= "msgstr[1] \"\"\n\n";
                } else {
                    // Fallback for malformed plural strings
                    $content .= "msgid \"" . addslashes($string) . "\"\n";
                    $content .= "msgstr \"\"\n\n";
                }
            } else {
                $content .= "msgid \"" . addslashes($string) . "\"\n";
                $content .= "msgstr \"\"\n\n";
            }
        }

        return $content;
    }

    /**
     * Update existing PO files with missing strings
     */
    public function update_po_files() {
        $po_files = glob($this->lang_dir . '*.po');
        $updated_files = array();

        foreach ($po_files as $po_file) {
            if (basename($po_file) === 'hostifybooking.pot') {
                continue;
            }

            $updated = $this->update_po_file($po_file);
            if ($updated) {
                $updated_files[] = basename($po_file);
            }
        }

        return $updated_files;
    }

    /**
     * Update a single PO file with missing strings
     */
    private function update_po_file($po_file) {
        // Load existing translations from the PO file
        
        // Read the existing PO file content first
        $content = file_get_contents($po_file);
        if (!$content) {
            return false;
        }
        
        try {
            $existing_translations = $this->load_existing_translations($po_file);
        } catch (Exception $e) {
            $existing_translations = array();
        }
        
        // Find missing strings by checking if they already exist in the PO file content
        $missing_strings = array();
        foreach ($this->extracted_strings as $string) {
            $found = false;

            // For plural forms, check if both singular and plural parts exist in the file
            if (strpos($string, '|') !== false) {
                $parts = explode('|', $string);
                if (count($parts) === 2) {
                    $singular = $parts[0];
                    $plural = $parts[1];
                    
                    // Check if this exact plural block already exists in the file
                    $plural_block = 'msgid "' . addslashes($singular) . '"' . "\n" . 
                                   'msgid_plural "' . addslashes($plural) . '"';
                    
                    if (strpos($content, $plural_block) !== false) {
                        $found = true;
                    }
                }
            } else {
                // For singular forms, check if the msgid already exists in the file
                $msgid_line = 'msgid "' . addslashes($string) . '"';
                if (strpos($content, $msgid_line) !== false) {
                    $found = true;
                }
            }

            if (!$found) {
                $missing_strings[] = $string;
            }
        }

        if (empty($missing_strings)) {
            return false;
        }

        // Add missing strings to the end of the PO file (DON'T touch headers)
        $new_content = '';
        foreach ($missing_strings as $string) {
            // Add source location comments if available
            if (isset($this->string_locations[$string]) && !empty($this->string_locations[$string])) {
                $locations = array_unique($this->string_locations[$string]);
                foreach ($locations as $location) {
                    $new_content .= "#: " . $location . "\n";
                }
            }
            
            // Check if this is a plural form (contains | separator)
            if (strpos($string, '|') !== false) {
                $parts = explode('|', $string);
                if (count($parts) === 2) {
                    $singular = $parts[0];
                    $plural = $parts[1];
                    $new_content .= "msgid \"" . addslashes($singular) . "\"\n";
                    $new_content .= "msgid_plural \"" . addslashes($plural) . "\"\n";
                    $new_content .= "msgstr[0] \"\"\n";
                    $new_content .= "msgstr[1] \"\"\n\n";
                } else {
                    // Fallback for malformed plural strings
                    $new_content .= "msgid \"" . addslashes($string) . "\"\n";
                    $new_content .= "msgstr \"\"\n\n";
                }
            } else {
                $new_content .= "msgid \"" . addslashes($string) . "\"\n";
                $new_content .= "msgstr \"\"\n\n";
            }
        }
        
        // Append new strings to existing content (preserve headers completely)
        // Ensure there's proper spacing between existing content and new strings
        $content = rtrim($content, "\n") . "\n\n" . $new_content;

        // Save the PO file
        $result = file_put_contents($po_file, $content);
        if ($result === false) {
            throw new Exception('Could not write PO file');
        }
        
        return true;
    }

    /**
     * Generate a report of missing translations
     */
    public function generate_missing_translations_report() {
        $report = array();
        $po_files = glob($this->lang_dir . '*.po');

        foreach ($po_files as $po_file) {
            if (basename($po_file) === 'hostifybooking.pot') {
                continue;
            }

            $language = basename($po_file, '.po');
            $language = str_replace('hostifybooking-', '', $language);
            
            $content = file_get_contents($po_file);
            $existing_strings = array();

            // Parse PO file properly
            $lines = explode("\n", $content);
            $current_msgid = '';
            $current_msgstr = '';
            $in_msgid = false;
            $in_msgstr = false;
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip comments and empty lines
                if (empty($line) || $line[0] === '#') {
                    continue;
                }
                
                // Check for msgid
                if (strpos($line, 'msgid "') === 0) {
                    // Save previous entry if exists
                    if (!empty($current_msgid) && $current_msgid !== '""') {
                        $existing_strings[stripslashes($current_msgid)] = stripslashes($current_msgstr);
                    }
                    
                    $current_msgid = substr($line, 7, -1); // Remove msgid " and "
                    $current_msgstr = '';
                    $in_msgid = true;
                    $in_msgstr = false;
                }
                // Check for msgstr
                elseif (strpos($line, 'msgstr "') === 0) {
                    $current_msgstr = substr($line, 8, -1); // Remove msgstr " and "
                    $in_msgid = false;
                    $in_msgstr = true;
                }
                // Handle multi-line strings
                elseif ($line[0] === '"' && $line[-1] === '"') {
                    $string_content = substr($line, 1, -1);
                    if ($in_msgid) {
                        $current_msgid .= $string_content;
                    } elseif ($in_msgstr) {
                        $current_msgstr .= $string_content;
                    }
                }
            }
            
            // Save the last entry
            if (!empty($current_msgid) && $current_msgid !== '""') {
                $existing_strings[stripslashes($current_msgid)] = stripslashes($current_msgstr);
            }

            // Find missing translations
            $missing_translations = array();
            foreach ($this->extracted_strings as $string) {
                if (!isset($existing_strings[$string]) || empty($existing_strings[$string])) {
                    $missing_translations[] = $string;
                }
            }

            if (!empty($missing_translations)) {
                $report[$language] = $missing_translations;
            }
        }

        return $report;
    }

    /**
     * Add source location comments to existing PO files
     */
    public function add_source_locations_to_po_files() {
        $po_files = glob($this->lang_dir . '*.po');
        $updated_files = array();

        foreach ($po_files as $po_file) {
            if (basename($po_file) === 'hostifybooking.pot') {
                continue;
            }

            $updated = $this->add_source_locations_to_po_file($po_file);
            if ($updated) {
                $updated_files[] = basename($po_file);
            }
        }

        return $updated_files;
    }

    /**
     * Add source location comments to a single PO file
     */
    private function add_source_locations_to_po_file($po_file) {
        $content = file_get_contents($po_file);
        $lines = explode("\n", $content);
        $new_lines = array();
        $updated = false;
        
        $i = 0;
        while ($i < count($lines)) {
            $line = $lines[$i];
            $new_lines[] = $line;
            
            // Check if this is a msgid line (not the header msgid)
            if (strpos($line, 'msgid "') === 0 && $line !== 'msgid ""') {
                $msgid = substr($line, 7); // Remove 'msgid "'
                // Only remove the last quote if it exists
                if (substr($msgid, -1) === '"') {
                    $msgid = substr($msgid, 0, -1);
                }
                $msgid = stripslashes($msgid);
                
                // Check if we have source location for this string
                if (isset($this->string_locations[$msgid]) && !empty($this->string_locations[$msgid])) {
                    // Check if there are already source location comments before this msgid
                    $has_source_comments = false;
                    $j = count($new_lines) - 2; // Go back to check previous lines
                    while ($j >= 0 && trim($new_lines[$j]) === '') {
                        $j--;
                    }
                    if ($j >= 0 && strpos($new_lines[$j], '#: ') === 0) {
                        $has_source_comments = true;
                    }
                    
                    // If no source comments exist, add them
                    if (!$has_source_comments) {
                        // Insert source location comments before the msgid
                        $locations = array_unique($this->string_locations[$msgid]);
                        foreach ($locations as $location) {
                            array_splice($new_lines, count($new_lines) - 1, 0, "#: " . $location);
                        }
                        $updated = true;
                    }
                }
            }
            
            $i++;
        }
        
        if ($updated) {
            $new_content = implode("\n", $new_lines);
            $result = file_put_contents($po_file, $new_content);
            if ($result === false) {
                throw new Exception('Could not write PO file');
            }
            
            // Compile PO to MO after updating
            try {
                $this->compile_po_to_mo($po_file);
            } catch (Exception $e) {
        
            }
        }
        
        return $updated;
    }

    /**
     * Run the complete extraction and update process
     */
    public function run_complete_update() {
        // Extract strings
        $strings = $this->extract_strings();
        
        // Update strings file
        $strings_file = $this->update_strings_file();
        
        // Update POT file
        $pot_file = $this->update_pot_file();
        
        // Update PO files
        $updated_files = $this->update_po_files();
        
        // Add source location comments to existing PO files
        $files_with_locations = $this->add_source_locations_to_po_files();
        
        // IMPORTANT: Don't update headers during extraction to prevent corruption
        // Headers should only be updated when explicitly requested via fix-po-headers.php
        // $updated_headers = $this->update_all_po_headers();
        $updated_headers = 0;
        
        // Generate missing translations report with percentages
        $missing_translations = array();
        $total_strings = count($strings);
        
        // Check if restored files have a different count and use that for consistency
        // This matches the logic in ajax_get_languages()
        $po_files = glob($this->lang_dir . '*.po');
        if (!empty($po_files)) {
            $first_po_file = $po_files[0];
            clearstatcache(true, $first_po_file);
            $po_translations = $this->load_existing_translations($first_po_file);
            $restored_total = count($po_translations);
            
            // If restored files have a different count, use that for consistency
            if ($restored_total != $total_strings) {
                $total_strings = $restored_total;
            }
        }
        
        // Use the same total for all calculations to ensure consistency
        $extracted_strings = $strings; // Keep the original extracted strings for comparison
        $po_files = glob($this->lang_dir . '*.po');
        
        foreach ($po_files as $po_file) {
            if (basename($po_file) === 'hostifybooking.pot') {
                continue;
            }
            
            $filename = basename($po_file);
            $lang_code = str_replace(array('hostifybooking-', '.po'), '', $filename);
            
            // Load existing translations
            $existing_translations = $this->load_existing_translations($po_file);
            
            // Count actual translations (non-empty msgstr values) in this PO file
            $translated_count = 0;
            $total_in_file = count($existing_translations);
            
            if ($total_in_file > 0) {
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
                
                $percentage = min(floor(($translated_count / $total_in_file) * 1000) / 10, 100);
                
                // Only include in missing translations report if there are actually missing translations
                $missing_count = $total_in_file - $translated_count;
                if ($missing_count > 0) {
                    $missing_translations[$lang_code] = array(
                        'missing' => $missing_count, // Just the count, not the actual strings
                        'total' => $total_in_file,
                        'translated' => $translated_count,
                        'percentage' => $percentage
                    );
                }
            }
        }
        
        return array(
            'total_strings' => count($strings),
            'updated_files' => $updated_files,
            'files_with_locations' => $files_with_locations,
            'updated_headers' => $updated_headers,
            'missing_translations' => $missing_translations
        );
    }

    /**
     * Load existing translations from a PO file
     */
    public function load_existing_translations($po_file) {
        $content = file_get_contents($po_file);
        if (!$content) {
            return array();
        }
        
        $existing_strings = array();

        // Parse PO file properly
        $lines = explode("\n", $content);
        $current_msgid = '';
        $current_msgid_plural = '';
        $current_msgstr = '';
        $current_msgstr_plural = array();
        $in_msgid = false;
        $in_msgid_plural = false;
        $in_msgstr = false;
        $in_msgstr_plural = false;
        $in_header = true;
        $is_plural_form = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments and empty lines
            if (empty($line) || $line[0] === '#') {
                continue;
            }
            
            // Check for msgid
            if (strpos($line, 'msgid "') === 0) {
                // Before starting a new entry, save the previous one
                if (!empty($current_msgid) && !$in_header) {
                    if ($is_plural_form) {
                        $plural_key = $current_msgid . '|' . $current_msgid_plural;
                        $combined_msgstr = implode('|', $current_msgstr_plural);
                        $existing_strings[stripslashes($plural_key)] = stripslashes($combined_msgstr);
                    } else {
                        $existing_strings[stripslashes($current_msgid)] = stripslashes($current_msgstr);
                    }
                }
                $msgid_content = substr($line, 7, -1); // Remove msgid " and "
                // Check if this is the header msgid (empty)
                if ($msgid_content === '') {
                    $in_header = true;
                    continue;
                } else {
                    $in_header = false;
                }
                $current_msgid = $msgid_content;
                $current_msgid_plural = '';
                $current_msgstr = '';
                $current_msgstr_plural = array();
                $in_msgid = true;
                $in_msgid_plural = false;
                $in_msgstr = false;
                $in_msgstr_plural = false;
                $is_plural_form = false;
            }
            // Check for msgid_plural
            elseif (strpos($line, 'msgid_plural "') === 0) {
                $current_msgid_plural = substr($line, 13, -1); // Remove msgid_plural " and "
                $is_plural_form = true;
            }
            // Check for msgstr
            elseif (strpos($line, 'msgstr "') === 0) {
                $current_msgstr = substr($line, 8, -1); // Remove msgstr " and "
                $in_msgid = false;
                $in_msgid_plural = false;
                $in_msgstr = true;
                $in_msgstr_plural = false;
            }
            // Check for msgstr[0], msgstr[1], etc.
            elseif (preg_match('/^msgstr\[(\d+)\] "/', $line, $matches)) {
                $plural_index = $matches[1];
                $msgstr_content = substr($line, strlen($matches[0]), -1); // Remove msgstr[X] " and "
                $current_msgstr_plural[$plural_index] = $msgstr_content;
                $in_msgid = false;
                $in_msgid_plural = false;
                $in_msgstr = false;
                $in_msgstr_plural = true;
            }
            // Handle multi-line strings
            elseif ($line[0] === '"' && $line[-1] === '"') {
                $string_content = substr($line, 1, -1);
                if ($in_msgid) {
                    $current_msgid .= $string_content;
                } elseif ($in_msgid_plural) {
                    $current_msgid_plural .= $string_content;
                } elseif ($in_msgstr) {
                    $current_msgstr .= $string_content;
                } elseif ($in_msgstr_plural) {
                    // Add to the last plural msgstr
                    $last_index = array_key_last($current_msgstr_plural);
                    if ($last_index !== null) {
                        $current_msgstr_plural[$last_index] .= $string_content;
                    }
                }
            }
        }
        
        // Save the last entry (but not header entries)
        if (!empty($current_msgid) && !$in_header) {
            if ($is_plural_form) {
                $plural_key = $current_msgid . '|' . $current_msgid_plural;
                $combined_msgstr = implode('|', $current_msgstr_plural);
                $existing_strings[stripslashes($plural_key)] = stripslashes($combined_msgstr);
            } else {
                $existing_strings[stripslashes($current_msgid)] = stripslashes($current_msgstr);
            }
        }

        return $existing_strings;
    }

    /**
     * Update headers for all PO files using the POT file as template
     */
    private function update_all_po_headers() {
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

    private function compile_po_to_mo($po_file) {
        $mo_file = str_replace('.po', '.mo', $po_file);
        
        // Read PO file content
        $po_content = file_get_contents($po_file);
        if (!$po_content) {
            throw new Exception('Could not read PO file');
        }
        
        // Create MO file content (simplified version)
        $content = '';
        $content .= "# Translation file for Hostify Booking\n";
        $content .= "# Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Parse PO content and add to MO
        $lines = explode("\n", $po_content);
        $msgid = '';
        $msgstr = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strpos($line, 'msgid "') === 0) {
                $msgid = substr($line, 7); // Remove 'msgid "'
                // Only remove the last quote if it exists
                if (substr($msgid, -1) === '"') {
                    $msgid = substr($msgid, 0, -1);
                }
            } elseif (strpos($line, 'msgstr "') === 0) {
                $msgstr = substr($line, 8); // Remove 'msgstr "'
                // Only remove the last quote if it exists
                if (substr($msgstr, -1) === '"') {
                    $msgstr = substr($msgstr, 0, -1);
                }
                
                if ($msgid !== '') {
                    $content .= "msgid \"" . addslashes($msgid) . "\"\n";
                    $content .= "msgstr \"" . addslashes($msgstr) . "\"\n\n";
                }
                
                $msgid = '';
                $msgstr = '';
            }
        }
        
        // Write MO file
        $result = file_put_contents($mo_file, $content);
        if ($result === false) {
            throw new Exception('Could not write MO file');
        }
        
        return $mo_file;
    }
}