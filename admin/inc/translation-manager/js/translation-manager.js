"use strict";

(function ($) {
    // Show message function with auto-dismiss (global scope)
    function showMessage(message, type, targetTab) {

        
        var noticeClass = 'notice-' + type;
        var html = '<div class="notice ' + noticeClass + ' is-dismissible" style="margin: 10px 0;">';
        html += '<p>' + message + '</p>';
        html += '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        html += '</div>';
        
        // Find the most appropriate container based on target tab or current tab
        var targetContainer = null;
        
        // If a specific target tab is provided, use it
        if (targetTab) {
            targetContainer = $('#' + targetTab);
        }
        // Otherwise, check which tab is currently visible
        else {
            // Check if we're in the translation editor tab
            if ($('#translation-editor-content').is(':visible')) {
                targetContainer = $('#translation-editor-content');
            }
            // Check if we're in the backup tab
            else if ($('#translation-backup-content').is(':visible')) {
                targetContainer = $('#translation-backup-content');
            }
            // Check if we're in the string extractor tab
            else if ($('#translation-manager-content').is(':visible')) {
                targetContainer = $('#translation-manager-content');
            }
            // Fallback to the main container
            else {
                targetContainer = $('.exopite-sof-content');
            }
        }
        
        // If no container found, use body as last resort
        if (!targetContainer || targetContainer.length === 0) {
            targetContainer = $('body');
        }
        
        // Insert notice in the appropriate location
        if (targetTab === 'translation-backup-content') {
            // For backup section, insert after the backup controls
            $('.backup-controls').after(html);
        } else if (targetTab === 'translation-editor-content') {
            // For translation editor section, insert after the translation editor controls
            $('.translation-editor-controls').after(html);
        } else if (targetTab === 'translation-manager-content') {
            // For translation manager section, insert after the compile MO button
            $('#compile-mo-btn').after(html);
        } else {
            // For other sections, insert at the top of the container
            targetContainer.prepend(html);
        }

        
        // Auto-dismiss after 6 seconds for success, info, and warning notices
        if (type === 'success' || type === 'info' || type === 'warning') {
            setTimeout(function() {
                $('.notice.' + noticeClass).fadeOut(500, function() {
                    $(this).remove();
                });
            }, 6000);
        }
        
        // Add click handler for manual dismiss
        $(document).on('click', '.notice-dismiss', function() {
            $(this).closest('.notice').fadeOut(500, function() {
                $(this).remove();
            });
        });
    }

    $(window).load(function () {
        

        
        // Translation Manager functionality
        $('#extract-strings-btn').on('click', function(){
            var btn = $(this);
            var resultsDiv = $('#extraction-results');
            
            btn.prop('disabled', true).text(hostifybooking_ajax.strings && hostifybooking_ajax.strings.extracting_strings ? hostifybooking_ajax.strings.extracting_strings : 'Extracting...');
            resultsDiv.hide();
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_extract_strings',
                    nonce: hostifybooking_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var html = '<div class="notice notice-success"><p><strong>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.extraction_completed ? hostifybooking_ajax.strings.extraction_completed : 'Extraction completed successfully!') + '</strong></p>';
                        

                        
                        // Display corruption check results if available
                        if (response.data.corruption_check) {
                            var corruption = response.data.corruption_check;
                            if (corruption.po_files_corrupted > 0 || corruption.mo_files_corrupted > 0) {
                                html += '<div class="notice notice-warning"><p><strong>Corruption Detection Results:</strong></p>';
                                html += '<ul>';
                                if (corruption.po_files_corrupted > 0) {
                                    html += '<li>PO files checked: ' + corruption.po_files_checked + ', Corrupted: ' + corruption.po_files_corrupted + ', Fixed: ' + corruption.po_files_fixed + '</li>';
                                }
                                if (corruption.mo_files_corrupted > 0) {
                                    html += '<li>MO files checked: ' + corruption.mo_files_checked + ', Corrupted: ' + corruption.mo_files_corrupted + ', Fixed: ' + corruption.mo_files_fixed + '</li>';
                                }
                                html += '</ul>';
                                
                                // Show detailed corruption information
                                if (corruption.details && corruption.details.length > 0) {
                                    html += '<p><strong>Details:</strong></p><ul>';
                                    corruption.details.forEach(function(detail) {
                                        html += '<li>' + detail + '</li>';
                                    });
                                    html += '</ul>';
                                }
                                html += '</div>';
                            } else {
                                html += '<p><em>✓ No corruption detected in translation files</em></p>';
                            }
                        }
                        
                        // Display extraction results
                        var extractionData = response.data.extraction_results || response.data;
                        html += '<p><strong>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.total_strings_found ? hostifybooking_ajax.strings.total_strings_found.replace('%d', extractionData.total_strings) : 'Total strings found: ' + extractionData.total_strings) + '</strong></p>';
                        
                        if (extractionData.updated_files && extractionData.updated_files.length > 0) {
                            html += '<p><strong>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.files_with_new_strings ? hostifybooking_ajax.strings.files_with_new_strings.replace('%s', extractionData.updated_files.join(', ')) : 'Files with new strings added: ' + extractionData.updated_files.join(', ')) + '</strong></p>';
                        }
                        
                        if (extractionData.files_with_locations && extractionData.files_with_locations.length > 0) {
                            html += '<p><strong>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.files_with_locations ? hostifybooking_ajax.strings.files_with_locations.replace('%s', extractionData.files_with_locations.join(', ')) : 'Files updated with source location comments: ' + extractionData.files_with_locations.join(', ')) + '</strong></p>';
                        }
                        
                        if (extractionData.updated_headers !== undefined) {
                            html += '<p><strong>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.po_files_updated ? hostifybooking_ajax.strings.po_files_updated.replace('%d', extractionData.updated_headers) : 'PO files with updated headers: ' + extractionData.updated_headers + ' files') + '</strong></p>';
                        }
                        
                        if (extractionData.missing_translations && Object.keys(extractionData.missing_translations).length > 0) {
                            html += '<h4>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.missing_translations_report ? hostifybooking_ajax.strings.missing_translations_report : 'Missing Translations Report:') + '</h4><ul>';
                            for (var lang in extractionData.missing_translations) {
                                var data = extractionData.missing_translations[lang];
                                var lineText = '';
                                if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.missing_translations_line) {
                                    lineText = hostifybooking_ajax.strings.missing_translations_line
                                        .replace('%s', lang)
                                        .replace('%d', data.missing)
                                        .replace('%d', data.translated)
                                        .replace('%d', data.total)
                                        .replace('%s', data.percentage);
                                } else {
                                    lineText = lang + ': ' + data.missing + ' missing translations (' + data.translated + '/' + data.total + ' translated - ' + data.percentage + '%)';
                                }
                                html += '<li><strong>' + lang + ':</strong> ' + lineText + '</li>';
                            }
                            html += '</ul>';
                        }
                        
                        html += '</div>';
                        resultsDiv.html(html).show();
                        
                        // Clear translation editor to ensure fresh state
                        resetTranslationEditor();
                        
                        // Refresh the available languages display to show updated percentages
                        // Add a small delay to ensure PO files are fully updated
                        setTimeout(function() {
                            reloadLanguages();
                        }, 500);
                        
                        // No need for page reload - translations are already updated via cache clearing
                    } else {
                        resultsDiv.html('<div class="notice notice-error"><p>Error: ' + (response.data || 'Unknown error') + '</p></div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    resultsDiv.html('<div class="notice notice-error"><p>AJAX error occurred. Please try again.</p></div>').show();
                },
                complete: function() {
                    btn.prop('disabled', false).text(hostifybooking_ajax.strings && hostifybooking_ajax.strings.extract_strings ? hostifybooking_ajax.strings.extract_strings : 'Extract Strings');
                }
            });
        });



        // Reset translation editor on page load
        resetTranslationEditor();
        
        // Load available languages on page load
        if ($('#po-file-selector').length || $('#available-languages').length) {
            // console.log('Translation manager detected, loading languages...');
            // console.log('AJAX URL:', hostifybooking_ajax.ajaxurl);
            // console.log('Nonce:', hostifybooking_ajax.nonce);
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_get_languages',
                    nonce: hostifybooking_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.languages) {
                        // Populate the available languages list if it exists
                        if ($('#available-languages').length) {
                            var html = '';
                            response.data.languages.forEach(function(lang) {
                                var progressClass = '';
                                if (lang.percentage >= 80) {
                                    progressClass = 'high-progress';
                                } else if (lang.percentage >= 50) {
                                    progressClass = 'medium-progress';
                                } else if (lang.percentage >= 10) {
                                    progressClass = 'low-progress';
                                } else {
                                    progressClass = 'very-low-progress';
                                }
                                
                                html += '<span class="language-badge ' + progressClass + '">';
                                html += '<strong>' + lang.code + '</strong>';
                                html += '<div class="percentage">' + lang.percentage + '%</div>';
                                html += '</span>';
                            });
                            $('#available-languages').html(html);
                        }
                        
                        // Populate the PO file selector if it exists
                        var selector = $('#po-file-selector');
                        if (selector.length) {
                            // console.log('Populating selector with', response.data.languages.length, 'languages');
                            // Clear existing options first
                            selector.empty();
                            // Add default option
                            selector.append('<option value="">Select a language file...</option>');
                            // Add language options
                            response.data.languages.forEach(function(lang) {
                                selector.append('<option value="hostifybooking-' + lang.code + '.po">' + lang.code + ' (' + lang.percentage + '%)</option>');
                            });
                        } else {
                            // console.log('PO file selector not found');
                        }
                    } else {
                        // console.log('No languages found or error in response');
                        // console.log('Response success:', response.success);
                        // console.log('Response data:', response.data);
                        if ($('#available-languages').length) {
                            $('#available-languages').html('<p>No translation files found.</p>');
                        }
                        if ($('#po-file-selector').length) {
                            $('#po-file-selector').html('<option value="">No translation files found</option>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // console.log('AJAX Error Details:');
                    // console.log('Status:', status);
                    // console.log('Error:', error);
                    // console.log('Response Text:', xhr.responseText);
                    // console.log('Status Code:', xhr.status);
                    // console.log('Ready State:', xhr.readyState);
                    
                    if ($('#available-languages').length) {
                        $('#available-languages').html('<p>Error loading languages.</p>');
                    }
                    if ($('#po-file-selector').length) {
                        $('#po-file-selector').html('<option value="">Error loading languages</option>');
                    }
                }
            });
        } else {
            // console.log('Translation manager elements not found');
        }

        // Translation Editor functionality
        var currentTranslations = {};
        var currentComments = {};
        
        $('#po-file-selector').on('change', function() {
            var selectedFile = $(this).val();
            $('#load-translations-btn').prop('disabled', !selectedFile);
            $('#download-po-btn').prop('disabled', !selectedFile);
            
            // Clear translation counter when switching files
            clearTranslationCounter();
        });
        
        $('#load-translations-btn').on('click', function() {
            var selectedFile = $('#po-file-selector').val();
            if (!selectedFile) return;
            
            var btn = $(this);
            btn.prop('disabled', true).text('Loading...');
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_load_translations',
                    nonce: hostifybooking_ajax.nonce,
                    file: selectedFile
                },
                success: function(response) {
                    if (response.success) {
                        currentTranslations = response.data.translations;
                        currentComments = response.data.comments || {};
                        displayTranslations(currentTranslations, currentComments);
                        $('#translation-editor-form').show();
                        $('#save-translations-btn').prop('disabled', false);
                        
                        // Rebind the save button event handler after loading translations
                        bindSaveButtonHandler();
                        
                        // Clear the search filter when loading new translations
                        $('#translation-filter').val('').trigger('input');
                        

                        
                        // Show success message
                        let successMessage = '';
                        if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.loaded_translations) {
                            successMessage = hostifybooking_ajax.strings.loaded_translations.replace('%d', Object.keys(currentTranslations).length).replace('%s', selectedFile);
                        } else {
                            successMessage = 'Loaded ' + Object.keys(currentTranslations).length + ' translations from ' + selectedFile;
                        }
                        showMessage(successMessage, 'success', 'translation-editor-content');
                    } else {
                        showMessage('Error: ' + (response.data || 'Unknown error'), 'error', 'translation-editor-content');
                    }
                },
                error: function(xhr, status, error) {
                    showMessage('AJAX error occurred. Please try again.', 'error', 'translation-editor-content');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Load Translations');
                }
            });
        });
        
        $('#download-po-btn').on('click', function() {
            var selectedFile = $('#po-file-selector').val();
            if (!selectedFile) return;
            
            // Create a form to submit the download request
            var form = $('<form>', {
                'method': 'POST',
                'action': hostifybooking_ajax.ajaxurl
            });
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'action',
                'value': 'hostifybooking_download_po'
            }));
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'nonce',
                'value': hostifybooking_ajax.nonce
            }));
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'file',
                'value': selectedFile
            }));
            
            $('body').append(form);
            form.submit();
            form.remove();
        });
        
        // Add New Language functionality
        $('#add-new-language-btn').on('click', function() {
            var modalHtml = '<div id="new-language-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">';
            modalHtml += '<div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">';
            modalHtml += '<h3>Add New Language</h3>';
            modalHtml += '<p>Enter the language code (e.g., fr_FR, es_ES, de_DE):</p>';
            modalHtml += '<input type="text" id="new-language-code" placeholder="e.g., fr_FR" style="width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">';
            modalHtml += '<p><small>Language codes should follow the format: language_COUNTRY (e.g., fr_FR, es_ES, pl_PL)</small></p>';
            modalHtml += '<div style="margin-top: 20px; text-align: right;">';
            modalHtml += '<button type="button" class="button" onclick="jQuery(\'#new-language-modal\').remove();">Cancel</button> ';
            modalHtml += '<button type="button" class="button button-primary" id="confirm-new-language-btn">Create Language File</button>';
            modalHtml += '</div>';
            modalHtml += '</div></div>';
            
            $('body').append(modalHtml);
            $('#new-language-code').focus();
            
            // Handle Enter key
            $('#new-language-code').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    $('#confirm-new-language-btn').click();
                }
            });
            
            // Handle Escape key
            $(document).on('keydown', function(e) {
                if (e.which === 27) { // Escape key
                    $('#new-language-modal').remove();
                }
            });
        });
        
        // Handle confirm new language button
        $(document).on('click', '#confirm-new-language-btn', function() {
            var langCode = $('#new-language-code').val().trim();
            if (!langCode) {
                alert('Please enter a language code.');
                return;
            }
            
            // Basic validation for language code format (lowercase language, uppercase country)
            if (!/^[a-z]{2}_[A-Z]{2}$/.test(langCode)) {
                alert('Please enter a valid language code in the format: language_COUNTRY (e.g., fr_FR, es_ES, pl_PL)');
                return;
            }
            
            var btn = $('#confirm-new-language-btn');
            btn.prop('disabled', true).text('Creating...');
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_create_language',
                    nonce: hostifybooking_ajax.nonce,
                    language_code: langCode
                },
                success: function(response) {
                    if (response.success) {
                        var message = 'Language file created successfully: hostifybooking-' + langCode + '.po';
                        if (response.data.strings_count) {
                            message += ' (' + response.data.strings_count + ' strings)';
                        }
                        if (response.data.mo_created) {
                            message += ' and .mo file';
                        } else {
                            message += ' (MO file compilation failed, but PO file was created)';
                        }
                        showMessage(message, 'success', 'translation-editor-content');
                        $('#new-language-modal').remove();
                        
                        // Refresh both the available languages display and the dropdown
                        refreshTranslationEditor();
                        reloadLanguages();
                    } else {
                        showMessage('Error: ' + (response.data || 'Unknown error'), 'error', 'translation-editor-content');
                    }
                },
                error: function() {
                    showMessage('Failed to create language file', 'error', 'translation-editor-content');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Create Language File');
                }
            });
        });
        
        // Function to bind save button event handler
        function bindSaveButtonHandler() {
            // console.log('Binding save button handler...');
            // Use event delegation to handle clicks on the save button
            $(document).off('click', '#save-translations-btn').on('click', '#save-translations-btn', function() {
                // console.log('Save button clicked!');
                // console.log('Button element:', $(this).length > 0 ? 'found' : 'not found');
                
                // Clear any existing notices first
                $('#translation-editor-content .notice').remove();
                
                // Capture the selected file immediately and store it
                var fileSelector = $('#po-file-selector');
                // console.log('File selector element:', fileSelector.length > 0 ? 'found' : 'not found');
                var selectedFile = fileSelector.val();
                // console.log('Selected file:', selectedFile);
                // console.log('File selector options:', fileSelector.find('option').length);
                
                if (!selectedFile) {
                    showMessage('Please select a language file first.', 'error', 'translation-editor-content');
                    return;
                }
                
                // Check if button is already disabled
                if ($(this).prop('disabled')) {
                    // console.log('Button already disabled, ignoring click');
                    return;
                }
                
                // Store the selected file in a variable that won't change
                var fileToSave = selectedFile;
                
                // Start with all current translations and update only the ones that were modified
                var translations = {};
                var comments = {};
                
                // Copy all existing translations first
                for (var msgid in currentTranslations) {
                    if (currentTranslations.hasOwnProperty(msgid)) {
                        translations[msgid] = currentTranslations[msgid];
                    }
                }
                
                // Update translations from visible DOM elements (only the ones that were actually changed)
                $('.translation-input').each(function() {
                    var msgid = $(this).data('msgid');
                    var translation = $(this).val();
                    
                    // Skip if this is a plural form (contains | separator)
                    if (msgid.indexOf('|') !== -1) {
                        return;
                    }
                    
                    // Find the comment from the 3rd td in the same row
                    var row = $(this).closest('.translation-row');
                    var commentTd = row.find('td:nth-child(3)');
                    var comment = commentTd.find('input').val() || commentTd.text().trim();
                    
                    translations[msgid] = translation;
                    // Always include the comment, even if empty, so the backend knows to clear it
                    comments[msgid] = comment.trim();
                });
                
                // Handle plural form translations
                $('.translation-input-singular').each(function() {
                    var msgid = $(this).data('msgid');
                    var singularTranslation = $(this).val();
                    
                    // Find the corresponding plural input
                    var row = $(this).closest('.translation-row');
                    var pluralInput = row.find('.translation-input-plural');
                    var pluralTranslation = pluralInput.val();
                    
                    // Find the comment from the 3rd td in the same row
                    var commentTd = row.find('td:nth-child(3)');
                    var comment = commentTd.find('input').val() || commentTd.text().trim();
                    
                    // Create the plural form structure
                    translations[msgid] = {
                        singular: singularTranslation,
                        plural: {
                            0: singularTranslation,
                            1: pluralTranslation
                        }
                    };
                    
                    // Use the singular part of the msgid for comments
                    var parts = msgid.split('|');
                    if (parts.length === 2) {
                        comments[parts[0]] = comment.trim();
                    } else {
                        comments[msgid] = comment.trim();
                    }
                });
                
                // Debug: Log what we're sending
                // console.log('Save Changes - Translations:', translations);
                // console.log('Save Changes - Comments:', comments);
                // console.log('Save Changes - Comments count:', Object.keys(comments).length);
                // console.log('Save Changes - AJAX URL:', hostifybooking_ajax.ajaxurl);
                // console.log('Save Changes - File to save:', fileToSave);
                
                var btn = $(this);
                var originalText = btn.text();
                btn.prop('disabled', true).text('Saving...');
                
                $.ajax({
                    url: hostifybooking_ajax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'hostifybooking_save_translations',
                        nonce: hostifybooking_ajax.nonce,
                        file: fileToSave, // Use the captured file name
                        translations: translations,
                        comments: comments
                    },
                                    success: function(response) {
                    // console.log('Save response:', response);
                    // Clear any existing notices before showing new one
                        $('#translation-editor-content .notice').remove();
                        
                        if (response.success) {
                            let successMessage = '';
                            if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.translations_saved) {
                                successMessage = hostifybooking_ajax.strings.translations_saved;
                            } else {
                                successMessage = 'Translations saved successfully!';
                            }
                            showMessage(successMessage, 'success', 'translation-editor-content');
                            currentTranslations = translations;
                            

                            
                            // Update translation counter after saving
                            updateTranslationCounter();
                            
                            // Update dropdown percentage to reflect current UI state
                            updateDropdownPercentage();
                            
                            // Don't reload the entire file - just update the local UI state
                            // The translations are already saved and the UI is up to date
                        } else {
                            showMessage('Error: ' + (response.data || 'Unknown error'), 'error', 'translation-editor-content');
                        }
                    },
                                                        error: function(xhr, status, error) {
                        // Clear any existing notices before showing new one
                        $('#translation-editor-content .notice').remove();
                        showMessage('AJAX error occurred. Please try again.', 'error', 'translation-editor-content');
                    },
                                                        complete: function() {
                        // Restore button to original state
                        btn.prop('disabled', false).text(originalText);
                    }
                });
            });
        }
        
        // Bind save button event handler initially
        bindSaveButtonHandler();
        
        // Also bind it when the DOM is ready to ensure it's available
        $(document).ready(function() {
            bindSaveButtonHandler();
        });
        
        // Clear Comments button functionality - COMMENTED OUT (was used for debugging)
        /*
        $('#clear-comments-btn').off('click').on('click', function() {
            // console.log('Clear Comments button clicked!');
            
            if (!confirm('Are you sure you want to clear all comments? This action cannot be undone.')) {
                return;
            }
            
            var selectedFile = $('#po-file-selector').val();
            if (!selectedFile) {
                showMessage('Please select a language file first.', 'error', 'translation-editor-content');
                return;
            }
            
            var btn = $(this);
            btn.prop('disabled', true).text('Clearing...');
            
            // Clear all comment inputs in the table
            $('.translation-comment').val('').prop('value', '');
            $('.translation-row td:nth-child(3)').each(function() {
                var $td = $(this);
                var $input = $td.find('input');
                if ($input.length) {
                    $input.val('').prop('value', '');
                } else {
                    $td.text('');
                }
            });
            
            // Force a small delay to ensure the DOM is updated
            setTimeout(function() {
                // Double-check that all comments are cleared
                $('.translation-comment').val('');
            }, 100);
            
            // Collect all translations (without comments)
            var translations = {};
            $('.translation-input').each(function() {
                var msgid = $(this).data('msgid');
                var translation = $(this).val();
                translations[msgid] = translation;
            });
            
            // Force empty comments object to clear all comments from the file
            var emptyComments = {};
            
            // Debug: Log what we're sending
            // console.log('Clear Comments - Translations:', translations);
            // console.log('Clear Comments - Empty Comments:', emptyComments);
            // console.log('Clear Comments - AJAX URL:', hostifybooking_ajax.ajaxurl);
            // console.log('Clear Comments - Selected file:', selectedFile);
            
            // Save with empty comments to clear them from the file
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_save_translations',
                    nonce: hostifybooking_ajax.nonce,
                    file: selectedFile,
                    translations: translations,
                    comments: emptyComments // Force empty comments object to clear all comments
                },
                success: function(response) {
                    // console.log('Clear Comments - AJAX Success Response:', response);
                    if (response.success) {
                        showMessage('All comments have been cleared from the file!', 'success', 'translation-editor-content');
                        currentTranslations = translations;
                        currentComments = {}; // Update current comments
                    } else {
                        showMessage('Error: ' + (response.data || 'Unknown error'), 'error', 'translation-editor-content');
                    }
                },
                error: function(xhr, status, error) {
                    // console.log('Clear Comments - AJAX Error:', status, error);
                    // console.log('Clear Comments - XHR Response:', xhr.responseText);
                    showMessage('AJAX error occurred. Please try again.', 'error', 'translation-editor-content');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Clear Comments');
                }
            });
        });
        */
        
        // Filter functionality
        $('#translation-filter').on('input', function() {
            filterTranslations();
        });
        
        $('#show-untranslated-only').on('change', function() {
            filterTranslations();
        });
        
        // Handle real-time translation input changes for singular fields
        $(document).on('input', '.translation-input', function() {
            var input = $(this);
            var row = input.closest('.translation-row');
            var statusSpan = row.find('.translation-status');
            var translation = input.val().trim();
            var isTranslated = translation !== '';
            
            // Update row class
            row.removeClass('translated untranslated').addClass(isTranslated ? 'translated' : 'untranslated');
            
            // Update status text and class
            statusSpan.removeClass('translated untranslated').addClass(isTranslated ? 'translated' : 'untranslated');
            statusSpan.text(isTranslated ? 'Translated' : 'Untranslated');
            
            // Update counter in real-time
            updateTranslationCounter();
            updateDropdownPercentage();
        });
        
        // Handle real-time translation input changes for plural fields
        $(document).on('input', '.translation-input-singular, .translation-input-plural', function() {
            var input = $(this);
            var row = input.closest('.translation-row');
            var statusSpan = row.find('.translation-status');
            
            // Get both singular and plural translations
            var singularInput = row.find('.translation-input-singular');
            var pluralInput = row.find('.translation-input-plural');
            var singularTranslation = singularInput.val().trim();
            var pluralTranslation = pluralInput.val().trim();
            
            // Consider translated only if BOTH singular and plural have content
            var isTranslated = singularTranslation !== '' && pluralTranslation !== '';
            
            // Update row class
            row.removeClass('translated untranslated').addClass(isTranslated ? 'translated' : 'untranslated');
            
            // Update status text and class
            statusSpan.removeClass('translated untranslated').addClass(isTranslated ? 'translated' : 'untranslated');
            statusSpan.text(isTranslated ? 'Translated' : 'Untranslated');
            
            // Update counter in real-time
            updateTranslationCounter();
            updateDropdownPercentage();
        });
        
        function updateTranslationCounter() {
            console.log('Updating translation counter...');
            console.log('Editor visible:', $('#translation-editor-form').is(':visible'));
            console.log('Translation rows:', $('.translation-row').length);
            
            // Only update counter if translation editor is visible and has data
            if ($('#translation-editor-form').is(':visible') && $('.translation-row').length > 0) {
                var totalCount = $('.translation-row').length;
                var totalTranslatedCount = $('.translation-row.translated').length;
                var totalPercentage = totalCount > 0 ? (Math.floor((totalTranslatedCount / totalCount) * 1000) / 10).toFixed(1) : '0.0';
                $('#translation-counter').text(totalTranslatedCount + '/' + totalCount + ' (' + totalPercentage + '%)');
                console.log('Counter updated:', totalTranslatedCount + '/' + totalCount + ' (' + totalPercentage + '%)');
            } else {
                // Hide or clear counter when editor is not active
                $('#translation-counter').text('');
                console.log('Counter cleared');
            }
        }
        
        function clearTranslationCounter() {
            $('#translation-counter').text('');
            // Also clear any loaded translation data
            $('.translation-row').remove();
            $('#translation-editor-form').hide();
            $('#translations-list').empty();
            // Force clear any cached data
            currentTranslations = {};
            currentComments = {};
        }
        
        function resetTranslationEditor() {
            console.log('Resetting translation editor...');
            // Reset file selector
            $('#po-file-selector').val('');
            $('#load-translations-btn').prop('disabled', true);
            $('#download-po-btn').prop('disabled', true);
            clearTranslationCounter();
            console.log('Translation editor reset complete');
        }
        
        function updateDropdownPercentage() {
            var selectedFile = $('#po-file-selector').val();
            if (selectedFile) {
                var totalCount = $('.translation-row').length;
                var totalTranslatedCount = $('.translation-row.translated').length;
                var totalPercentage = totalCount > 0 ? (Math.floor((totalTranslatedCount / totalCount) * 1000) / 10).toFixed(1) : '0.0';
                
                // Update the selected option in the dropdown
                var option = $('#po-file-selector option[value="' + selectedFile + '"]');
                var langCode = selectedFile.replace('hostifybooking-', '').replace('.po', '');
                option.text(langCode + ' (' + totalPercentage + '%)');
            }
        }
        
        function reloadLanguages() {
            // Add aggressive cache-busting parameter to prevent caching
            var cacheBuster = new Date().getTime() + Math.random();
            console.log('Reloading languages with cache buster:', cacheBuster);
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_get_languages',
                    nonce: hostifybooking_ajax.nonce,
                    _cb: cacheBuster // Cache buster
                },
                success: function(response) {
                    console.log('Server response:', response);
                    if (response.success && response.data.languages) {

                        // Log individual language percentages
                        response.data.languages.forEach(function(lang) {

                        });
                        // Populate the available languages list if it exists
                        if ($('#available-languages').length) {
                            var html = '';
                            response.data.languages.forEach(function(lang) {
                                var progressClass = '';
                                if (lang.percentage >= 80) {
                                    progressClass = 'high-progress';
                                } else if (lang.percentage >= 50) {
                                    progressClass = 'medium-progress';
                                } else if (lang.percentage >= 10) {
                                    progressClass = 'low-progress';
                                } else {
                                    progressClass = 'very-low-progress';
                                }
                                
                                html += '<span class="language-badge ' + progressClass + '">';
                                html += '<strong>' + lang.code + '</strong>';
                                html += '<div class="percentage">' + lang.percentage + '%</div>';
                                html += '</span>';
                            });
                            $('#available-languages').html(html);
                        }
                        
                        // Update extract strings report if it's visible
                        updateExtractStringsReport();
                        
                        // Populate the PO file selector if it exists
                        var selector = $('#po-file-selector');
                        if (selector.length) {
                            // Store the currently selected value
                            var currentSelection = selector.val();
                            // console.log('ReloadLanguages - Current selection:', currentSelection);
                            
                            // Clear existing options first
                            selector.empty();
                            // Add default option
                            selector.append('<option value="">Select a language file...</option>');
                            // Add language options
                            response.data.languages.forEach(function(lang) {
                                var optionValue = 'hostifybooking-' + lang.code + '.po';
                                var isSelected = (optionValue === currentSelection) ? ' selected' : '';
                                
                                // If this is the currently selected file and translation editor is active,
                                // use the UI state instead of server state
                                var displayPercentage = lang.percentage;
                                if (optionValue === currentSelection && $('#translation-editor-form').is(':visible')) {
                                    // Use the same logic as updateTranslationCounter()
                                    var totalCount = $('.translation-row').length;
                                    var totalTranslatedCount = $('.translation-row.translated').length;
                                    if (totalCount > 0) {
                                        var uiPercentage = (Math.floor((totalTranslatedCount / totalCount) * 1000) / 10).toFixed(1);
                                        displayPercentage = uiPercentage;
                                    }
                                }
                                
                                selector.append('<option value="' + optionValue + '"' + isSelected + '>' + lang.code + ' (' + displayPercentage + '%)</option>');
                            });
                            
                            // Restore the selection if it was previously set
                            if (currentSelection) {
                                selector.val(currentSelection);
                                // console.log('ReloadLanguages - Restored selection to:', currentSelection);
                                
                                // Update button states based on the restored selection
                                $('#load-translations-btn').prop('disabled', false);
                                $('#download-po-btn').prop('disabled', false);
                                
                                // Update translation counter if we have UI data
                                if ($('.translation-row').length > 0) {
                                    updateTranslationCounter();
                                }
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // console.log('Error reloading languages:', error);
                }
            });
        }
        
        function updateExtractStringsReport() {
            // Check if extract strings results are visible
            var resultsDiv = $('#extract-strings-results');
            if (resultsDiv.is(':visible') && resultsDiv.find('.missing-translations-report').length > 0) {
                // Fetch fresh extraction data to update the report
                $.ajax({
                    url: hostifybooking_ajax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'hostifybooking_extract_strings',
                        nonce: hostifybooking_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            var extractionData = response.data.extraction_results || response.data;
                            
                            // Update the missing translations report section
                            if (extractionData.missing_translations && Object.keys(extractionData.missing_translations).length > 0) {
                                var reportHtml = '<h4>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.missing_translations_report ? hostifybooking_ajax.strings.missing_translations_report : 'Missing Translations Report:') + '</h4><ul>';
                                for (var lang in extractionData.missing_translations) {
                                    var data = extractionData.missing_translations[lang];
                                    var lineText = '';
                                    if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.missing_translations_line) {
                                        lineText = hostifybooking_ajax.strings.missing_translations_line
                                            .replace('%s', lang)
                                            .replace('%d', data.missing)
                                            .replace('%d', data.translated)
                                            .replace('%d', data.total)
                                            .replace('%s', data.percentage);
                                    } else {
                                        lineText = lang + ': ' + data.missing + ' missing translations (' + data.translated + '/' + data.total + ' translated - ' + data.percentage + '%)';
                                    }
                                    reportHtml += '<li><strong>' + lang + ':</strong> ' + lineText + '</li>';
                                }
                                reportHtml += '</ul>';
                                
                                // Update the report section
                                var reportSection = resultsDiv.find('.missing-translations-report');
                                if (reportSection.length) {
                                    reportSection.html(reportHtml);
                                }
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // Silently fail - don't show error for background update
                    }
                });
            }
        }
        
        function displayTranslations(translations, comments) {
            var html = '<table class="wp-list-table widefat fixed striped">';
            html += '<thead><tr><th style="width: 30%;">Source Text</th><th style="width: 30%;">Translation</th><th style="width: 15%;">Comments</th><th style="width: 15%;">Status</th><th style="width: 10%;">Type</th></tr></thead>';
            html += '<tbody>';
            
            var translatedCount = 0;
            var totalCount = Object.keys(translations).length;
            
            Object.keys(translations).forEach(function(msgid) {
                var translation = translations[msgid];
                var comment = comments[msgid] || '';
                var isTranslated = false;
                var statusClass = 'untranslated';
                var statusText = 'Untranslated';
                var typeText = 'Singular';
                var typeClass = 'type-singular';
                
                // Check if this is a plural form (contains | separator)
                if (msgid.indexOf('|') !== -1) {
                    var parts = msgid.split('|');
                    if (parts.length === 2 && typeof translation === 'object') {
                        // This is a plural form
                        typeText = 'Plural';
                        typeClass = 'type-plural';
                        
                        var singularTranslation = translation.singular || '';
                        var pluralTranslation = translation.plural && translation.plural[1] ? translation.plural[1] : '';
                        
                        isTranslated = (singularTranslation.trim() !== '' && pluralTranslation.trim() !== '');
                        
                        // Clean up the display text by removing excessive escaping
                        var cleanSingular = parts[0].replace(/\\+"/g, '"').replace(/^"+|"+$/g, '');
                        var cleanPlural = parts[1].replace(/\\+"/g, '"').replace(/^"+|"+$/g, '');
                        
                        html += '<tr class="translation-row ' + (isTranslated ? 'translated' : 'untranslated') + ' plural-form">';
                        html += '<td><strong>Singular: ' + escapeHtml(cleanSingular) + '</strong><br><em>Plural: ' + escapeHtml(cleanPlural) + '</em></td>';
                        html += '<td>';
                        html += '<div style="margin-bottom: 5px;"><strong>Singular:</strong><br><input type="text" class="translation-input-singular" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(singularTranslation) + '" style="width: 100%;" placeholder="Enter singular translation..."></div>';
                        html += '<div><strong>Plural:</strong><br><input type="text" class="translation-input-plural" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(pluralTranslation) + '" style="width: 100%;" placeholder="Enter plural translation..."></div>';
                        html += '</td>';
                        html += '<td><input type="text" class="translation-comment" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(comment) + '" placeholder="Add comment..." style="width: 100%; font-size: 12px; color: #666;"></td>';
                        html += '<td><span class="translation-status ' + (isTranslated ? 'translated' : 'untranslated') + '">' + (isTranslated ? 'Translated' : 'Untranslated') + '</span></td>';
                        html += '<td><span class="translation-type ' + typeClass + '">' + typeText + '</span></td>';
                        html += '</tr>';
                    } else {
                        // Fallback for malformed plural strings
                        isTranslated = translation && typeof translation === 'string' && translation.trim() !== '';
                        
                        // Clean up the display text
                        var cleanMsgid = msgid.replace(/\\+"/g, '"').replace(/^"+|"+$/g, '');
                        
                        html += '<tr class="translation-row ' + (isTranslated ? 'translated' : 'untranslated') + '">';
                        html += '<td><strong>' + escapeHtml(cleanMsgid) + '</strong></td>';
                        html += '<td><input type="text" class="translation-input" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(typeof translation === 'string' ? translation : '') + '" style="width: 100%;" placeholder="Enter translation..."></td>';
                        html += '<td><input type="text" class="translation-comment" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(comment) + '" placeholder="Add comment..." style="width: 100%; font-size: 12px; color: #666;"></td>';
                        html += '<td><span class="translation-status ' + (isTranslated ? 'translated' : 'untranslated') + '">' + (isTranslated ? 'Translated' : 'Untranslated') + '</span></td>';
                        html += '<td><span class="translation-type ' + typeClass + '">' + typeText + '</span></td>';
                        html += '</tr>';
                    }
                } else {
                    // Regular singular form
                    isTranslated = translation && typeof translation === 'string' && translation.trim() !== '';
                    
                    // Clean up the display text
                    var cleanMsgid = msgid.replace(/\\+"/g, '"').replace(/^"+|"+$/g, '');
                    
                    html += '<tr class="translation-row ' + (isTranslated ? 'translated' : 'untranslated') + '">';
                    html += '<td><strong>' + escapeHtml(cleanMsgid) + '</strong></td>';
                    html += '<td><input type="text" class="translation-input" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(typeof translation === 'string' ? translation : '') + '" style="width: 100%;" placeholder="Enter translation..."></td>';
                    html += '<td><input type="text" class="translation-comment" data-msgid="' + escapeHtml(msgid) + '" value="' + escapeHtml(comment) + '" placeholder="Add comment..." style="width: 100%; font-size: 12px; color: #666;"></td>';
                    html += '<td><span class="translation-status ' + (isTranslated ? 'translated' : 'untranslated') + '">' + (isTranslated ? 'Translated' : 'Untranslated') + '</span></td>';
                    html += '<td><span class="translation-type ' + typeClass + '">' + typeText + '</span></td>';
                    html += '</tr>';
                }
                
                if (isTranslated) {
                    translatedCount++;
                }
            });
            
            html += '</tbody></table>';
            $('#translations-list').html(html);
            
            // Update the translation counter
            var percentage = totalCount > 0 ? Math.min(((translatedCount / totalCount) * 100), 100).toFixed(1) : '0.0';
            $('#translation-counter').text(translatedCount + '/' + totalCount + ' (' + percentage + '%)');
        }
        
        function filterTranslations() {
            var filter = $('#translation-filter').val().toLowerCase();
            var showUntranslatedOnly = $('#show-untranslated-only').is(':checked');
            
            var visibleCount = 0;
            var visibleTranslatedCount = 0;
            
            $('.translation-row').each(function() {
                var row = $(this);
                var isTranslated = row.hasClass('translated');
                var matchesFilter = false;
                
                // Check if this is a plural form row
                if (row.hasClass('plural-form')) {
                    var msgid = row.find('.translation-input-singular').data('msgid').toLowerCase();
                    var singularTranslation = row.find('.translation-input-singular').val().toLowerCase();
                    var pluralTranslation = row.find('.translation-input-plural').val().toLowerCase();
                    var comment = row.find('.translation-comment').val().toLowerCase();
                    
                    // For plural forms, search in both singular and plural parts
                    var parts = msgid.split('|');
                    var singularText = parts[0] ? parts[0].toLowerCase() : '';
                    var pluralText = parts[1] ? parts[1].toLowerCase() : '';
                    
                    matchesFilter = singularText.includes(filter) || pluralText.includes(filter) || 
                                   singularTranslation.includes(filter) || pluralTranslation.includes(filter) || 
                                   comment.includes(filter);
                } else {
                    // Regular singular form
                    var msgid = row.find('.translation-input').data('msgid').toLowerCase();
                    var translation = row.find('.translation-input').val().toLowerCase();
                    var comment = row.find('.translation-comment').val().toLowerCase();
                    
                    matchesFilter = msgid.includes(filter) || translation.includes(filter) || comment.includes(filter);
                }
                
                var shouldShow = matchesFilter && (!showUntranslatedOnly || !isTranslated);
                
                row.toggle(shouldShow);
                
                if (shouldShow) {
                    visibleCount++;
                    if (isTranslated) {
                        visibleTranslatedCount++;
                    }
                }
            });
            
            // Update counter to show filtered results
            if (filter || showUntranslatedOnly) {
                var filteredPercentage = visibleCount > 0 ? Math.min(((visibleTranslatedCount / visibleCount) * 100), 100).toFixed(1) : '0.0';
                $('#translation-counter').text(visibleTranslatedCount + '/' + visibleCount + ' (' + filteredPercentage + '%) (filtered)');
            } else {
                // Show total count when no filters are applied
                updateTranslationCounter();
            }
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Backup and Restore functionality
        $('#create-backup-btn').on('click', function() {
            // Show modal for backup title
            var modalHtml = '<div id="backup-title-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">';
            modalHtml += '<div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">';
            modalHtml += '<h3>Create Backup</h3>';
            modalHtml += '<p>Enter a title for this backup (optional):</p>';
            modalHtml += '<input type="text" id="backup-title-input" placeholder="e.g., Before major update, Working version, etc." style="width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">';
            modalHtml += '<div style="margin-top: 20px; text-align: right;">';
            modalHtml += '<button type="button" class="button" onclick="jQuery(\'#backup-title-modal\').remove();">Cancel</button> ';
            modalHtml += '<button type="button" class="button button-primary" id="confirm-backup-btn">Create Backup</button>';
            modalHtml += '</div>';
            modalHtml += '</div></div>';
            
            $('body').append(modalHtml);
            $('#backup-title-input').focus();
            
            // Handle Enter key
            $('#backup-title-input').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    $('#confirm-backup-btn').click();
                }
            });
            
            // Handle Escape key
            $(document).on('keydown', function(e) {
                if (e.which === 27) { // Escape key
                    $('#backup-title-modal').remove();
                }
            });
        });
        
        // Handle confirm backup button
        $(document).on('click', '#confirm-backup-btn', function() {
            var backupTitle = $('#backup-title-input').val().trim();
            var btn = $('#create-backup-btn');
            btn.prop('disabled', true).text('Creating Backup...');
            
            // Remove modal
            $('#backup-title-modal').remove();
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_create_backup',
                    nonce: hostifybooking_ajax.nonce,
                    backup_title: backupTitle
                },
                success: function(response) {
                    if (response.success) {
                        var message = 'Backup created successfully: ' + response.data.backup_dir;
                        if (response.data.title) {
                            message += ' - "' + response.data.title + '"';
                        }
                        showMessage(message, 'success', 'translation-backup-content');
                        loadBackupsList();
                    } else {
                        showMessage('Error: ' + response.data, 'error', 'translation-backup-content');
                    }
                },
                error: function() {
                    showMessage('Failed to create backup', 'error', 'translation-backup-content');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Create Backup Now');
                }
            });
        });
        
        $('#refresh-backups-btn').on('click', function() {
            loadBackupsList();
        });
        
        $('#cleanup-backups-btn').on('click', function() {
            if (!confirm('This will delete ALL backup files (both individual and full backups). This action cannot be undone. Continue?')) {
                return;
            }
            
            var btn = $(this);
            btn.prop('disabled', true).text('Deleting all backups...');
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_delete_all_backups',
                    nonce: hostifybooking_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('Deleted ' + response.data.deleted_count + ' backup files and ' + response.data.deleted_dirs + ' backup directories', 'success', 'translation-backup-content');
                        loadBackupsList(); // Refresh the list
                    } else {
                        showMessage('Error: ' + response.data, 'error', 'translation-backup-content');
                    }
                },
                error: function() {
                    showMessage('Failed to delete all backups', 'error', 'translation-backup-content');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Delete All Backups');
                }
            });
        });
        
        function loadBackupsList() {
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_get_backups',
                    nonce: hostifybooking_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayBackupsList(response.data.backups);
                    } else {
                        $('#backups-list').html('<p>Error loading backups: ' + (response.data || 'Unknown error') + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    // console.log('AJAX Error:', status, error);
                    $('#backups-list').html('<p>Error loading backups. Please try again.</p>');
                }
            });
        }
        
        function displayBackupsList(backups) {
            if (backups.length === 0) {
                $('#backups-list').html('<p>No backups found. Create your first backup to protect your translations.</p>');
                return;
            }
            
            var html = '<h3>Available Backups</h3><table class="wp-list-table widefat fixed striped">';
            html += '<thead><tr><th>Backup Date</th><th>Title</th><th>Files</th><th>Actions</th></tr></thead><tbody>';
            
            backups.forEach(function(backup) {
                html += '<tr>';
                html += '<td>' + backup.date.replace(/-/g, ' ').replace(/(\d{4}) (\d{2}) (\d{2}) (\d{2}) (\d{2}) (\d{2})/, '$1-$2-$3 $4:$5:$6') + '</td>';
                html += '<td>' + (backup.title ? '<strong>' + escapeHtml(backup.title) + '</strong>' : '<em>No title</em>') + '</td>';
                html += '<td>' + backup.count + ' files</td>';
                html += '<td>';
                html += '<button type="button" class="button restore-backup-btn" data-backup="' + backup.name + '">Restore</button> ';
                html += '<button type="button" class="button download-backup-btn" data-backup="' + backup.name + '">Download ZIP</button> ';
                html += '<button type="button" class="button show-location-btn" data-backup="' + backup.name + '">Show Location</button> ';
                html += '<button type="button" class="button delete-backup-btn" data-backup="' + backup.name + '">Delete</button>';
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            $('#backups-list').html(html);
        }
        
        $(document).on('click', '.restore-backup-btn', function() {
            var backupName = $(this).data('backup');
    
            if (!confirm('Are you sure you want to restore from this backup? This will overwrite current translation files.')) {
                return;
            }
            
            var btn = $(this);
            btn.prop('disabled', true).text('Restoring...');
            
    
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_restore_backup',
                    nonce: hostifybooking_ajax.nonce,
                    backup_name: backupName
                },
                success: function(response) {
                    
                    if (response.success) {
                        // Handle different response data formats
                        var message = 'Backup restored successfully!';
                        if (response.data && Array.isArray(response.data)) {
                            message += ' Files restored: ' + response.data.join(', ');
                        } else if (response.data && typeof response.data === 'object') {
                            message += ' Files restored: ' + (response.data.restored ? response.data.restored.join(', ') : 'Unknown files');
                        } else if (response.data) {
                            message += ' ' + response.data;
                        }
                        
                        showMessage(message, 'success', 'translation-backup-content');
                        loadBackupsList();
                        
                        // Refresh the translation editor if it's currently loaded
                        refreshTranslationEditor();
                        
                        // Refresh the available languages display to show updated percentages
                        // Use a longer delay to ensure files are fully updated
                        setTimeout(function() {
            
                            reloadLanguages();
                        }, 1000);
                        
                        // No need for page reload - translations are already updated via cache clearing
                    } else {
                        showMessage('Error: ' + response.data, 'error', 'translation-backup-content');
                    }
                },
                error: function(xhr, status, error) {
                    showMessage('Failed to restore backup', 'error', 'translation-backup-content');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Restore');
                }
            });
        });
        
        $(document).on('click', '.delete-backup-btn', function() {
            var backupName = $(this).data('backup');
            if (!confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
                return;
            }
            
            var btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_delete_backup',
                    nonce: hostifybooking_ajax.nonce,
                    backup_name: backupName
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('Backup deleted successfully', 'success', 'translation-backup-content');
                        loadBackupsList();
                    } else {
                        showMessage('Error: ' + response.data, 'error', 'translation-backup-content');
                    }
                },
                error: function() {
                    showMessage('Failed to delete backup', 'error', 'translation-backup-content');
                }
            });
        });
        
        $(document).on('click', '.download-backup-btn', function() {
            var backupName = $(this).data('backup');
            var btn = $(this);
            btn.prop('disabled', true).text('Preparing...');
            
            // Get backup title from the table row for better user feedback
            var backupTitle = '';
            var titleCell = btn.closest('tr').find('td:nth-child(2)');
            if (titleCell.length && !titleCell.find('em').length) {
                backupTitle = titleCell.find('strong').text();
            }
            
            // Create a form to submit the download request
            var form = $('<form>', {
                'method': 'POST',
                'action': hostifybooking_ajax.ajaxurl
            });
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'action',
                'value': 'hostifybooking_download_backup'
            }));
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'nonce',
                'value': hostifybooking_ajax.nonce
            }));
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'backup_name',
                'value': backupName
            }));
            
            $('body').append(form);
            form.submit();
            form.remove();
            
            // Show success message with filename info
            var message = 'Download started!';
            if (backupTitle) {
                message += ' The ZIP file will be named with your backup title: "' + backupTitle + '"';
            }
            showMessage(message, 'success', 'translation-backup-content');
            
            // Re-enable button after a short delay
            setTimeout(function() {
                btn.prop('disabled', false).text('Download ZIP');
            }, 2000);
        });
        
        $(document).on('click', '.show-location-btn', function() {
            var backupName = $(this).data('backup');
            var btn = $(this);
            btn.prop('disabled', true).text('Loading...');
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_get_backup_path',
                    nonce: hostifybooking_ajax.nonce,
                    backup_name: backupName
                },
                success: function(response) {
                    if (response.success) {
                        var pathInfo = response.data;
                        
                        // Get backup title from the table row
                        var backupTitle = '';
                        var titleCell = btn.closest('tr').find('td:nth-child(2)');
                        if (titleCell.length && !titleCell.find('em').length) {
                            backupTitle = titleCell.find('strong').text();
                        }
                        
                        var modalHtml = '<div id="backup-location-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">';
                        modalHtml += '<div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80%; overflow-y: auto;">';
                        modalHtml += '<h3>Backup Location: ' + backupName + '</h3>';
                        if (backupTitle) {
                            modalHtml += '<p><strong>Title:</strong> "' + backupTitle + '"</p>';
                        }
                        modalHtml += '<p><strong>Size:</strong> ' + pathInfo.size_formatted + '</p>';
                        modalHtml += '<p><strong>Files:</strong> ' + pathInfo.files.join(', ') + '</p>';
                        modalHtml += '<p><strong>Server Path:</strong></p>';
                        modalHtml += '<code style="display: block; background: #f5f5f5; padding: 10px; margin: 10px 0; word-break: break-all;">' + pathInfo.path + '</code>';
                        modalHtml += '<p><strong>Web URL:</strong></p>';
                        modalHtml += '<code style="display: block; background: #f5f5f5; padding: 10px; margin: 10px 0; word-break: break-all;">' + pathInfo.url + '</code>';
                        modalHtml += '<div style="margin-top: 20px; text-align: center;">';
                        modalHtml += '<button type="button" class="button" onclick="jQuery(\'#backup-location-modal\').remove();">Close</button>';
                        modalHtml += '</div>';
                        modalHtml += '</div></div>';
                        
                        $('body').append(modalHtml);
                    } else {
                        $('#backup-status').html('<div class="notice notice-error"><p>Error: ' + (response.data || 'Unknown error') + '</p></div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    // console.log('AJAX Error:', status, error);
                    $('#backup-status').html('<div class="notice notice-error"><p>AJAX error occurred. Please try again.</p></div>').show();
                },
                complete: function() {
                    btn.prop('disabled', false).text('Open Location');
                }
            });
        });
        
        // File upload functionality
        $('#upload-po-file').on('change', function() {
            var file = this.files[0];
            if (file) {
                $('#upload-po-btn').prop('disabled', false);
            } else {
                $('#upload-po-btn').prop('disabled', true);
            }
        });
        
        $('#upload-po-btn').on('click', function() {
            var fileInput = $('#upload-po-file')[0];
            var file = fileInput.files[0];
            if (!file) return;
            
            var createBackup = $('#create-backup-on-upload').is(':checked');
            
            // If backup is enabled, show modal for backup title
            if (createBackup) {
                var modalHtml = '<div id="upload-backup-title-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">';
                modalHtml += '<div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">';
                modalHtml += '<h3>Upload & Restore</h3>';
                modalHtml += '<p>Enter a title for the backup of existing files (optional):</p>';
                modalHtml += '<input type="text" id="upload-backup-title-input" placeholder="e.g., Backup before upload, Previous version, etc." style="width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">';
                modalHtml += '<div style="margin-top: 20px; text-align: right;">';
                modalHtml += '<button type="button" class="button" onclick="jQuery(\'#upload-backup-title-modal\').remove();">Cancel</button> ';
                modalHtml += '<button type="button" class="button button-primary" id="confirm-upload-btn">Upload & Restore</button>';
                modalHtml += '</div>';
                modalHtml += '</div></div>';
                
                $('body').append(modalHtml);
                $('#upload-backup-title-input').focus();
                
                // Handle Enter key
                $('#upload-backup-title-input').on('keypress', function(e) {
                    if (e.which === 13) { // Enter key
                        $('#confirm-upload-btn').click();
                    }
                });
                
                // Handle Escape key
                $(document).on('keydown', function(e) {
                    if (e.which === 27) { // Escape key
                        $('#upload-backup-title-modal').remove();
                    }
                });
            } else {
                // No backup needed, proceed directly
                performUpload('', file, fileInput);
            }
        });
        
        // Handle confirm upload button
        $(document).on('click', '#confirm-upload-btn', function() {
            var backupTitle = $('#upload-backup-title-input').val().trim();
            var fileInput = $('#upload-po-file')[0];
            var file = fileInput.files[0];
            
            // Remove modal
            $('#upload-backup-title-modal').remove();
            
            // Perform the upload
            performUpload(backupTitle, file, fileInput);
        });
        
        // Function to perform the actual upload
        function performUpload(backupTitle, file, fileInput) {
            var formData = new FormData();
            formData.append('action', 'hostifybooking_upload_po');
            formData.append('nonce', hostifybooking_ajax.nonce);
            formData.append('po_file', file);
            formData.append('filename', file.name);
            formData.append('create_backup', $('#create-backup-on-upload').is(':checked') ? '1' : '0');
            formData.append('backup_title', backupTitle);
            
            var btn = $('#upload-po-btn');
            btn.prop('disabled', true).text(hostifybooking_ajax.strings && hostifybooking_ajax.strings.uploading ? hostifybooking_ajax.strings.uploading : 'Uploading...');
            
            $.ajax({
                url: hostifybooking_ajax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Clear any existing notices in the upload section
                        $('.upload-controls').nextAll('.notice').remove();
                        
                        // Show notice in the upload section specifically
                        var noticeHtml = '<div class="notice notice-success is-dismissible" style="margin: 10px 0;">';
                        noticeHtml += '<p>' + (hostifybooking_ajax.strings && hostifybooking_ajax.strings.file_uploaded_successfully ? hostifybooking_ajax.strings.file_uploaded_successfully : 'File uploaded and restored successfully!') + '</p>';
                        if (response.data.backup_created) {
                            noticeHtml += '<p><strong>' + hostifybooking_ajax.strings.backup_created.replace('%s', response.data.backup_created) + '</strong>';
                            if (backupTitle) {
                                noticeHtml += ' - "' + backupTitle + '"';
                            }
                            noticeHtml += '</p>';
                        }
                        noticeHtml += '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
                        noticeHtml += '</div>';
                        
                        // Insert after the upload controls
                        $('.upload-controls').after(noticeHtml);
                        
                        // Auto-dismiss after 6 seconds
                        setTimeout(function() {
                            $('.upload-controls').next('.notice').fadeOut(500, function() {
                                $(this).remove();
                            });
                        }, 6000);
                        
                        // Add click handler for manual dismiss
                        $(document).on('click', '.notice-dismiss', function() {
                            $(this).closest('.notice').fadeOut(500, function() {
                                $(this).remove();
                            });
                        });
                        
                        fileInput.value = '';
                        $('#upload-po-btn').prop('disabled', true);
                        
                        // Refresh the translation editor if it's currently loaded
                        refreshTranslationEditor();
                        
                        // Refresh the available languages display to show updated percentages
                        // Use a longer delay to ensure files are fully updated
                        setTimeout(function() {
                    
                            reloadLanguages();
                        }, 1000);
                        
                        // Refresh the available backups list
                        loadBackupsList();
                        
                        // No need for page reload - translations are already updated via cache clearing
                    } else {
                        // Clear any existing notices in the upload section
                        $('.upload-controls').nextAll('.notice').remove();
                        
                        var errorHtml = '<div class="notice notice-error is-dismissible" style="margin: 10px 0;">';
                        errorHtml += '<p>Error: ' + (response.data || 'Unknown error') + '</p>';
                        errorHtml += '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
                        errorHtml += '</div>';
                        $('.upload-controls').after(errorHtml);
                    }
                },
                error: function(xhr, status, error) {
                    // console.log('AJAX Error:', status, error);
                    
                    // Clear any existing notices in the upload section
                    $('.upload-controls').nextAll('.notice').remove();
                    
                    var errorHtml = '<div class="notice notice-error is-dismissible" style="margin: 10px 0;">';
                    errorHtml += '<p>AJAX error occurred. Please try again.</p>';
                    errorHtml += '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
                    errorHtml += '</div>';
                    $('.upload-controls').after(errorHtml);
                },
                complete: function() {
                    btn.prop('disabled', false).text(hostifybooking_ajax.strings && hostifybooking_ajax.strings.upload_restore ? hostifybooking_ajax.strings.upload_restore : 'Upload & Restore');
                }
            });
        }
        
        // Load backups list on page load
        loadBackupsList();
        
        // Function to refresh translation editor if it's currently loaded
        function refreshTranslationEditor() {
            var currentFile = $('#po-file-selector').val();
            
            if (currentFile && $('#translation-editor-form').is(':visible')) {
                // Reload the current file after a short delay
                setTimeout(function() {
                    $('#load-translations-btn').click();
                    
                    // Rebind the save button event handler after the translation editor is refreshed
                    setTimeout(function() {
                        bindSaveButtonHandler();
                    }, 1000);
                }, 500);
            }
        }



        // Compile MO files
        function compileMoFiles() {
            if (!confirm('This will compile all PO files to MO files. Continue?')) {
                return;
            }
            
            // Show loading state
            var btn = $('#compile-mo-btn');
            btn.prop('disabled', true).text(hostifybooking_ajax.strings && hostifybooking_ajax.strings.compiling ? hostifybooking_ajax.strings.compiling : 'Compiling...');
            
            // Clear any existing messages in the translation manager section
            $('#translation-manager-content .notice').remove();
            
            // Show loading message
            let loadingMessage = '';
            if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.compiling_mo_files) {
                loadingMessage = hostifybooking_ajax.strings.compiling_mo_files;
            } else {
                loadingMessage = 'Compiling MO files...';
            }
            showMessage(loadingMessage, 'info', 'translation-manager-content');
            
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'hostifybooking_compile_mo_files',
                    nonce: hostifybooking_ajax.nonce
                },
                success: function(response) {
                    // Clear the loading message
                    $('#translation-manager-content .notice').remove();
                    
                    // console.log('Compile MO response:', response);
                    // console.log('Localized strings:', hostifybooking_ajax.strings);
                    
                    if (response.success) {
                        // Use localized string if available, otherwise use fallback
                        let message = '';
                        if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.successfully_compiled) {
                            message = hostifybooking_ajax.strings.successfully_compiled.replace('%d', response.data.compiled.length);
                        } else {
                            message = 'Successfully compiled ' + response.data.compiled.length + ' MO files.';
                        }
                        // console.log('Generated message:', message);
                        
                        if (response.data.errors.length > 0) {
                            let errorMessage = '';
                            if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.compilation_errors) {
                                errorMessage = hostifybooking_ajax.strings.compilation_errors.replace('%s', response.data.errors.join(', '));
                            } else {
                                errorMessage = 'Errors: ' + response.data.errors.join(', ');
                            }
                            message += ' ' + errorMessage;
                            // console.log('Message with errors:', message);
                        }
                        showMessage(message, response.data.errors.length > 0 ? 'warning' : 'success', 'translation-manager-content');
                        
                        // No need for page reload - translations are already updated via cache clearing
                    } else {
                        showMessage('Error: ' + response.data, 'error', 'translation-manager-content');
                    }
                },
                error: function() {
                    // Clear the loading message
                    $('#translation-manager-content .notice').remove();
                    
                    let errorMessage = '';
                    if (hostifybooking_ajax.strings && hostifybooking_ajax.strings.failed_to_compile) {
                        errorMessage = hostifybooking_ajax.strings.failed_to_compile;
                    } else {
                        errorMessage = 'Failed to compile MO files';
                    }
                    showMessage(errorMessage, 'error', 'translation-manager-content');
                },
                complete: function() {
                    // Restore button state
                    btn.prop('disabled', false).text(hostifybooking_ajax.strings && hostifybooking_ajax.strings.compile_mo_files ? hostifybooking_ajax.strings.compile_mo_files : 'Compile MO Files');
                }
            });
        }

        // Compile MO files button
        $('#compile-mo-btn').on('click', function(){
            compileMoFiles();
        });



    });
})(jQuery); 