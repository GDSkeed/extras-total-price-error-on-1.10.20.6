<?php
/**
 * String Extraction Script for Hostify Booking Plugin
 * 
 * Usage: php extract-strings.php
 * 
 * This script will scan all PHP files in the plugin and extract translatable strings,
 * then update the translation files accordingly.
 * 
 * Location: admin/inc/translation-manager/extract-strings.php
 * 
 * NOTE: This file has been commented out to prevent execution.
 * Uncomment the code below if you need to run the extraction manually.
 */

/*
// Define plugin directory (go up 4 levels from this file)
$plugin_dir = dirname(__FILE__, 4);
$lang_dir = $plugin_dir . '/lang/';

// Include WordPress functions if not already loaded
if (!function_exists('__')) {
    // Simple fallback for __ function
    function __($text, $domain = '') {
        return $text;
    }
}

// Include the string extractor class (now in the same directory)
require_once __DIR__ . '/class-string-extractor.php';

echo "Hostify Booking Plugin - String Extraction Tool\n";
echo "===============================================\n\n";

try {
    // Initialize the extractor
    $extractor = new HostifyBooking_String_Extractor();
    
    // Run the complete extraction process
    $result = $extractor->run_complete_update();
    
    echo "\nExtraction completed successfully!\n";
    echo "===================================\n";
    echo "Total strings found: " . $result['total_strings'] . "\n";
    
    if (!empty($result['updated_files'])) {
        echo "Updated files: " . implode(', ', $result['updated_files']) . "\n";
    } else {
        echo "No files needed updating.\n";
    }
    
    if (!empty($result['missing_translations'])) {
        echo "\nMissing translations report:\n";
        echo "============================\n";
        foreach ($result['missing_translations'] as $language => $missing) {
            echo "- $language: " . count($missing) . " missing translations\n";
        }
    }
    
    echo "\nNext steps:\n";
    echo "1. Review the updated files in the lang/ directory\n";
    echo "2. Use a translation tool like Poedit to translate the strings\n";
    echo "3. Generate .mo files from the .po files\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
*/

// Display a message that the file is disabled
echo "Hostify Booking Plugin - String Extraction Tool\n";
echo "===============================================\n\n";
echo "This extraction script has been disabled.\n";
echo "To run the extraction manually, uncomment the code in this file.\n";
echo "\nAlternatively, use the web-based Translation Manager in the WordPress admin:\n";
echo "1. Go to Hostify Booking > Settings > Translation Manager\n";
echo "2. Click 'Extract Strings' button\n";
echo "3. View results in the web interface\n";
?> 