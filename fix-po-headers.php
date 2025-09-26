<?php
/**
 * Fix PO File Headers Script
 * This script will fix the malformed PO file headers
 */

// Only run in admin
if (!is_admin()) {
    return;
}

// Add admin notice to show the fix button
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        echo '<div class="notice notice-warning">';
        echo '<p><strong>🔧 PO File Headers Need Fixing!</strong> The PO files have malformed headers (missing quotes/newlines).</p>';
        echo '<p><a href="' . add_query_arg('fix_po_headers', '1') . '" class="button button-primary">🔧 Fix PO Headers Now</a></p>';
        echo '</div>';
    }
});

// Handle the fix action
add_action('admin_init', function() {
    if (isset($_GET['fix_po_headers']) && current_user_can('manage_options')) {
        
        // Include the string extractor
        require_once __DIR__ . '/admin/inc/translation-manager/class-string-extractor.php';
        
        $extractor = new HostifyBooking_String_Extractor();
        
        // Update all PO file headers by running the complete update
        $result = $extractor->run_complete_update();
        
        if ($result) {
            echo '<div class="notice notice-success">';
            echo '<p><strong>🎉 Success!</strong> Fixed PO file headers.</p>';
            echo '<p><strong>🔄 Next Steps:</strong></p>';
            echo '<ol>';
            echo '<li>Recompile MO files using the "Compile MO Files" button</li>';
            echo '<li>Test the Bulgarian translations again</li>';
            echo '</ol>';
            echo '</div>';
        } else {
            echo '<div class="notice notice-error">';
            echo '<p><strong>❌ Error:</strong> Failed to fix PO file headers.</p>';
            echo '</div>';
        }
        
        // Remove the fix parameter from URL
        wp_redirect(remove_query_arg('fix_po_headers'));
        exit;
    }
}); 