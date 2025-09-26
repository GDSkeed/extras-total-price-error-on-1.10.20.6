<?php

/**
 * MO File Compiler for Hostify Booking Plugin
 * Shared utility class for compiling PO files to MO files
 */
class HostifyBooking_MO_Compiler {

    /**
     * Compile a PO file to MO file
     * 
     * @param string $po_file_path Path to the PO file
     * @return string Path to the created MO file
     * @throws Exception If compilation fails
     */
    public static function compile_po_to_mo($po_file_path) {
        $mo_file_path = str_replace('.po', '.mo', $po_file_path);
        
        // Try to use system msgfmt command first (preferred method)
        $msgfmt_command = 'msgfmt -o ' . escapeshellarg($mo_file_path) . ' ' . escapeshellarg($po_file_path);
        $output = array();
        $return_var = 0;
        
        exec($msgfmt_command . ' 2>&1', $output, $return_var);
        
        if ($return_var === 0) {
            // Successfully compiled with msgfmt
            return $mo_file_path;
        }
        
        // Fallback: Use PHP to create a basic MO file structure
        // This is a simplified binary format that should work for basic translations
        $po_content = file_get_contents($po_file_path);
        if (!$po_content) {
            throw new Exception('Could not read PO file');
        }
        
        // Parse PO content to extract msgid/msgstr pairs
        $translations = self::parse_po_content($po_content);
        
        // Create a simple binary MO file structure
        $mo_data = self::create_mo_binary($translations);
        
        // Write MO file
        $result = file_put_contents($mo_file_path, $mo_data);
        if ($result === false) {
            throw new Exception('Could not write MO file');
        }
        
        return $mo_file_path;
    }

    /**
     * Parse PO file content to extract translations
     * 
     * @param string $po_content The PO file content
     * @return array Array of msgid => msgstr translations
     */
    private static function parse_po_content($po_content) {
        $translations = array();
        $lines = explode("\n", $po_content);
        $current_msgid = '';
        $current_msgstr = '';
        $in_msgid = false;
        $in_msgstr = false;
        
        foreach ($lines as $line) {
            $line = rtrim($line); // Remove trailing whitespace but keep leading
            
            // Skip comments and empty lines
            if (empty($line) || $line[0] === '#') {
                // If we encounter an empty line, save the current entry
                if (!empty($current_msgid) && $current_msgid !== '""') {
                    $translations[stripslashes($current_msgid)] = stripslashes($current_msgstr);
                }
                $current_msgid = '';
                $current_msgstr = '';
                $in_msgid = false;
                $in_msgstr = false;
                continue;
            }
            
            // Check for msgid
            if (strpos($line, 'msgid "') === 0) {
                // Save previous entry if exists
                if (!empty($current_msgid) && $current_msgid !== '""') {
                    $translations[stripslashes($current_msgid)] = stripslashes($current_msgstr);
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
            // Handle multi-line strings (quoted strings)
            elseif ($line[0] === '"' && substr($line, -1) === '"') {
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
            $translations[stripslashes($current_msgid)] = stripslashes($current_msgstr);
        }
        
        return $translations;
    }

    /**
     * Create binary MO file content from translations
     * 
     * @param array $translations Array of msgid => msgstr translations
     * @return string Binary MO file content
     */
    private static function create_mo_binary($translations) {
        // Add headers as the first entry (empty msgid)
        $headers = array(
            'Project-Id-Version' => 'PACKAGE VERSION',
            'Report-Msgid-Bugs-To' => '',
            'POT-Creation-Date' => date('Y-m-d H:i:sO'),
            'PO-Revision-Date' => date('Y-m-d H:i:sO'),
            'Last-Translator' => 'FULL NAME <EMAIL@ADDRESS>',
            'Language-Team' => 'LANGUAGE <LL@li.org>',
            'Language' => '',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
            'Plural-Forms' => 'nplurals=2; plural=(n != 1);'
        );
        
        $headers_string = '';
        foreach ($headers as $name => $value) {
            $headers_string .= "$name: $value\n";
        }
        
        // Add headers as first entry
        $all_translations = array('' => $headers_string) + $translations;
        
        $count = count($all_translations);
        
        // Calculate offsets
        $originals_addr = 28; // Header size
        $translations_addr = $originals_addr + ($count * 8); // 8 bytes per entry
        $hash_addr = $translations_addr + ($count * 8);
        $hash_size = 0; // No hash table for simplicity
        
        // Calculate string offsets
        $current_offset = $hash_addr + $hash_size;
        $originals_table = array();
        $translations_table = array();
        
        // First pass: calculate offsets for original strings
        foreach (array_keys($all_translations) as $msgid) {
            $originals_table[] = array(
                'length' => strlen($msgid),
                'offset' => $current_offset
            );
            $current_offset += strlen($msgid) + 1; // +1 for null terminator
        }
        
        // Second pass: calculate offsets for translated strings
        foreach (array_values($all_translations) as $msgstr) {
            $translations_table[] = array(
                'length' => strlen($msgstr),
                'offset' => $current_offset
            );
            $current_offset += strlen($msgstr) + 1; // +1 for null terminator
        }
        
        // Build MO file
        $mo_data = '';
        
        // Header (7 * 4 bytes)
        $mo_data .= pack('V', 0x950412de); // Magic number (little endian)
        $mo_data .= pack('V', 0); // Revision
        $mo_data .= pack('V', $count); // Number of strings
        $mo_data .= pack('V', $originals_addr); // Offset of original strings table
        $mo_data .= pack('V', $translations_addr); // Offset of translated strings table
        $mo_data .= pack('V', $hash_size); // Size of hash table
        $mo_data .= pack('V', $hash_addr); // Offset of hash table
        
        // Original strings table
        foreach ($originals_table as $original) {
            $mo_data .= pack('V', $original['length']);
            $mo_data .= pack('V', $original['offset']);
        }
        
        // Translated strings table
        foreach ($translations_table as $translation) {
            $mo_data .= pack('V', $translation['length']);
            $mo_data .= pack('V', $translation['offset']);
        }
        
        // Original strings
        foreach (array_keys($all_translations) as $msgid) {
            $mo_data .= $msgid . "\0";
        }
        
        // Translated strings
        foreach (array_values($all_translations) as $msgstr) {
            $mo_data .= $msgstr . "\0";
        }
        
        return $mo_data;
    }

    /**
     * Compile all PO files in a directory to MO files
     * 
     * @param string $lang_dir Directory containing PO files
     * @return array Array with 'compiled' and 'errors' keys
     */
    public static function compile_all_po_files($lang_dir) {
        $po_files = glob($lang_dir . '*.po');
        $compiled = array();
        $errors = array();
        
        foreach ($po_files as $po_file) {
            try {
                $mo_file = self::compile_po_to_mo($po_file);
                $compiled[] = basename($mo_file);
            } catch (Exception $e) {
                $errors[] = basename($po_file) . ': ' . $e->getMessage();
            }
        }
        
        return array(
            'compiled' => $compiled,
            'errors' => $errors
        );
    }

    /**
     * Extract clean headers from POT file (everything before the first #: line)
     * 
     * @param string $pot_content The POT file content
     * @return string Clean headers without source comments
     */
    public static function extract_clean_headers($pot_content) {
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
     * Format headers to ensure proper quoting and line endings
     * 
     * @param string $headers Raw headers
     * @return string Properly formatted headers
     */
    private static function format_headers($headers) {
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
     * @return string Language team string
     */
    private static function get_language_team($language_code) {
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
} 