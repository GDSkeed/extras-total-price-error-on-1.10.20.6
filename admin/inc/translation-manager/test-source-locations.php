<?php
/**
 * Test script to verify source location comments are added to existing PO files
 * Run this from the WordPress admin or via browser
 * 
 * NOTE: This file has been commented out to prevent execution.
 * Uncomment the code below if you need to run the test again.
 */

/*
// Include WordPress
require_once('../../../../../../wp-load.php');

// Check if user is authorized
if (!current_user_can('manage_options')) {
    die('Unauthorized');
}

echo "<h1>Testing Source Location Comments</h1>";

try {
    // Include the string extractor
    require_once __DIR__ . '/class-string-extractor.php';
    
    $extractor = new HostifyBooking_String_Extractor();
    
    echo "<h2>Step 1: Extracting strings and locations</h2>";
    $strings = $extractor->extract_strings();
    $locations = $extractor->get_string_locations();
    
    echo "<p>Found " . count($strings) . " strings</p>";
    echo "<p>Found " . count($locations) . " strings with location data</p>";
    
    // Show a few examples
    echo "<h3>Sample string locations:</h3>";
    $count = 0;
    foreach ($locations as $string => $string_locations) {
        if ($count < 5) {
            echo "<p><strong>" . htmlspecialchars($string) . "</strong>: " . implode(', ', $string_locations) . "</p>";
            $count++;
        } else {
            break;
        }
    }
    
    echo "<h2>Step 2: Adding source locations to existing PO files</h2>";
    $updated_files = $extractor->add_source_locations_to_po_files();
    
    if (empty($updated_files)) {
        echo "<p>No files were updated (they may already have source location comments)</p>";
    } else {
        echo "<p>Updated files with source location comments:</p>";
        echo "<ul>";
        foreach ($updated_files as $file) {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>Step 3: Checking Bulgarian file</h2>";
    $bg_file = dirname(__FILE__, 4) . '/lang/hostifybooking-bg_BG.po';
    if (file_exists($bg_file)) {
        $content = file_get_contents($bg_file);
        $lines = explode("\n", $content);
        
        $source_comment_count = 0;
        foreach ($lines as $line) {
            if (strpos($line, '#: ') === 0) {
                $source_comment_count++;
            }
        }
        
        echo "<p>Bulgarian file has " . $source_comment_count . " source location comments</p>";
        
        if ($source_comment_count > 0) {
            echo "<h3>Sample source location comments from Bulgarian file:</h3>";
            $count = 0;
            foreach ($lines as $line) {
                if (strpos($line, '#: ') === 0 && $count < 10) {
                    echo "<p>" . htmlspecialchars($line) . "</p>";
                    $count++;
                }
            }
        }
    } else {
        echo "<p>Bulgarian file not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='admin.php?page=hostifybooking'>Back to Hostify Booking Admin</a></p>";
*/

// Display a message that the file is disabled
echo "<h1>Test Source Locations Script</h1>";
echo "<p><strong>This test script has been disabled.</strong></p>";
echo "<p>To run the test again, uncomment the code in this file.</p>";
echo "<p><a href='admin.php?page=hostifybooking'>Back to Hostify Booking Admin</a></p>";
?> 