<?php
/**
 * PO Header Manager Class
 * 
 * Handles all PO file header operations including extraction, formatting, and validation.
 * This class centralizes header management to prevent inconsistencies across the plugin.
 */
class HostifyBooking_PO_Header_Manager {
    
    /**
     * Extract headers from POT file content
     * 
     * @param string $pot_content The POT file content
     * @return string The extracted headers (everything before the first #: line)
     */
    public function extract_headers_from_pot($pot_content) {
        if (!$pot_content) {
            return '';
        }
        
        $pot_lines = explode("\n", $pot_content);
        $header_lines = array();
        
        foreach ($pot_lines as $line) {
            $trimmed_line = trim($line);
            
            // Stop at the first #: line (source comment)
            if (strpos($trimmed_line, '#:') === 0) {
                break;
            }
            
            // Also stop at empty lines that come after the header
            if (empty($trimmed_line) && count($header_lines) > 0) {
                // Check if the previous line was the end of the header (X-Generator)
                $prev_line = end($header_lines);
                if (strpos($prev_line, 'X-Generator:') !== false) {
                    break;
                }
            }
            
            $header_lines[] = $line;
        }
        
        return implode("\n", $header_lines);
    }
    
    /**
     * Create properly formatted PO headers for a language
     * 
     * @param string $language_code The language code (e.g., sv_SE)
     * @param string $pot_content The POT file content
     * @return string Properly formatted PO headers
     */
    public function create_headers($language_code, $pot_content) {
        if (!$pot_content) {
            return $this->create_fallback_headers($language_code);
        }
        
        // Extract headers from POT file
        $headers = $this->extract_headers_from_pot($pot_content);
        
        // Update language-specific fields
        $headers = $this->update_language_fields($headers, $language_code);
        
        // Ensure proper formatting
        $headers = $this->format_headers($headers);
        
        // Add proper spacing
        $headers = rtrim($headers, "\n") . "\n\n";
        
        return $headers;
    }
    
    /**
     * Create fallback headers when no POT file is available
     * 
     * @param string $language_code The language code
     * @return string Fallback headers
     */
    public function create_fallback_headers($language_code) {
        $current_date = date('Y-m-d H:i:sO');
        return '# Copyright (C) 2025 Hostify Booking Engine' . "\n"
             . '# This file is distributed under the same license as the Hostify Booking Engine package.' . "\n"
             . 'msgid ""' . "\n"
             . 'msgstr ""' . "\n"
             . '"Project-Id-Version: Hostify Booking Engine\\n"' . "\n"
             . '"Report-Msgid-Bugs-To: https://hostify.com\\n"' . "\n"
             . '"POT-Creation-Date: ' . $current_date . '\\n"' . "\n"
             . '"PO-Revision-Date: ' . $current_date . '\\n"' . "\n"
             . '"Last-Translator: \\n"' . "\n"
             . '"Language-Team: ' . $this->get_language_team($language_code) . '\\n"' . "\n"
             . '"MIME-Version: 1.0\\n"' . "\n"
             . '"Content-Type: text/plain; charset=UTF-8\\n"' . "\n"
             . '"Content-Transfer-Encoding: 8bit\\n"' . "\n"
             . '"X-Generator: Hostify Booking Engine String Extractor\\n"' . "\n\n";
    }
    
    /**
     * Update language-specific fields in headers
     * 
     * @param string $headers The headers to update
     * @param string $language_code The language code
     * @return string Updated headers
     */
    private function update_language_fields($headers, $language_code) {
        // Update language team
        $headers = str_replace('LANGUAGE <LL@li.org>', $this->get_language_team($language_code), $headers);
        $headers = str_replace('FULL NAME <EMAIL@ADDRESS>', '', $headers);
        
        // Update PO-Revision-Date
        $current_date = date('Y-m-d H:i:sO');
        $headers = preg_replace('/PO-Revision-Date: [^\n]*/', 'PO-Revision-Date: ' . $current_date . "\n", $headers);
        
        // Add Language field if it doesn't exist
        if (strpos($headers, '"Language:') === false) {
            $headers = str_replace(
                '"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"',
                '"Last-Translator: \n"\n"Language: ' . $language_code . '\n"',
                $headers
            );
        }
        
        return $headers;
    }
    
    /**
     * Format headers to ensure proper structure
     * 
     * @param string $headers The headers to format
     * @return string Formatted headers
     */
    private function format_headers($headers) {
        $header_lines = explode("\n", $headers);
        $fixed_header_lines = array();
        
        foreach ($header_lines as $line) {
            $trimmed_line = trim($line);
            
            // Skip empty lines
            if (empty($trimmed_line)) {
                $fixed_header_lines[] = '';
                continue;
            }
            
            // Handle quoted header lines
            if (strpos($trimmed_line, '"') === 0) {
                // If line doesn't end with \n", fix it
                if (!preg_match('/\\\\n"$/', $trimmed_line)) {
                    if (substr($trimmed_line, -1) === '"') {
                        // Line ends with quote but no \n, add \n"
                        $trimmed_line = substr($trimmed_line, 0, -1) . '\\n"';
                    } else {
                        // Line doesn't end with quote, add \n"
                        $trimmed_line .= '\\n"';
                    }
                }
            }
            
            $fixed_header_lines[] = $trimmed_line;
        }
        
        return implode("\n", $fixed_header_lines);
    }
    
    /**
     * Get the language team string for a given language code
     * 
     * @param string $language_code The language code
     * @return string The language team string
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
     * Validate if headers are properly formatted
     * 
     * @param string $headers The headers to validate
     * @return array Array with 'valid' => boolean and 'errors' => array of error messages
     */
    public function validate_headers($headers) {
        $errors = array();
        
        // Check if headers contain #: lines
        if (strpos($headers, '#:') !== false) {
            $errors[] = 'Headers contain source comment lines (#:)';
        }
        
        // Check if all quoted lines end with \n"
        $lines = explode("\n", $headers);
        foreach ($lines as $line_num => $line) {
            if (strpos($line, '"') === 0 && !preg_match('/\\\\n"$/', $line)) {
                $errors[] = 'Line ' . ($line_num + 1) . ' doesn\'t end with \\n": ' . $line;
            }
        }
        
        // Check if headers end with proper spacing
        if (substr($headers, -2) !== "\n\n") {
            $errors[] = 'Headers don\'t end with proper spacing (two newlines)';
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
    
    /**
     * Update headers for an existing PO file
     * 
     * @param string $file_path Path to the PO file
     * @param string $pot_content POT file content
     * @return bool Success status
     */
    public function update_po_file_headers($file_path, $pot_content) {
        // Extract language code from filename
        $filename = basename($file_path);
        if (!preg_match('/hostifybooking-([a-z]{2}_[A-Z]{2})\.po/', $filename, $matches)) {
            return false;
        }
        
        $language_code = $matches[1];
        
        // Create new headers
        $new_headers = $this->create_headers($language_code, $pot_content);
        
        // Read existing PO file
        $po_content = file_get_contents($file_path);
        if (!$po_content) {
            return false;
        }
        
        // Extract translations (everything after headers)
        $po_lines = explode("\n", $po_content);
        $translation_lines = array();
        $in_translations = false;
        
        foreach ($po_lines as $line) {
            $trimmed_line = trim($line);
            
            if (strpos($trimmed_line, 'msgid "') === 0) {
                $msgid_content = substr($trimmed_line, 7, -1);
                if ($msgid_content === '') {
                    // This is the header msgid, skip it
                    continue;
                } else {
                    // This is the first real msgid, start collecting translations
                    $in_translations = true;
                }
            }
            
            if ($in_translations) {
                $translation_lines[] = $line;
            }
        }
        
        // Combine new headers with existing translations
        $new_content = $new_headers . implode("\n", $translation_lines);
        
        // Write the updated file
        return file_put_contents($file_path, $new_content) !== false;
    }
} 