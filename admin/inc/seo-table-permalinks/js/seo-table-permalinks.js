/**
 * SEO Permalinks Table JavaScript
 * 
 * Handles the functionality for the SEO permalinks table
 */

(function($) {
    'use strict';

    // Store permalink data for client-side filtering
    let allPermalinks = [];
    let isDataLoaded = false;
    let searchTimeout = null;
    let regenerationTimer = null;
    let regenerationStartTime = 0;

    // Initialize the table when document is ready
    $(document).ready(function() {
        // Only execute if the table container exists
        if ($('#hfy-seo-table-placeholder').length) {
            initPermalinksTable();
        }
    });

    /**
     * Initialize the permalinks table
     */
    function initPermalinksTable() {
        // Track table loading start time
        const tableLoadStartTime = Date.now();
        
        // Show loading message
        $('#hfy-seo-table-placeholder').html('<p class="loading">' + hfyPermalinksData.strings.loading + '</p>');
        
        // Fetch initial data - this call won't attach events until data is loaded
        $.ajax({
            url: hfyPermalinksData.ajax_url,
            type: 'POST',
            data: {
                action: 'hfy_get_permalinks_data',
                nonce: hfyPermalinksData.nonce,
                search_query: ''
            },
            success: function(response) {
                // Calculate table load time
                const tableLoadTime = Math.round((Date.now() - tableLoadStartTime) / 1000);
                const serverProcessingTime = response.data.server_time || 0;
                
                if (response.success) {
                    // Store data for client-side filtering
                    allPermalinks = response.data.permalinks || [];
                    isDataLoaded = true;
                    
                    // Store processing times
                    $(document).data('tableLoadTime', tableLoadTime);
                    $(document).data('serverProcessingTime', serverProcessingTime);
                    
                    // Now render the table with the initial data
                    renderInitialTable(response.data);
                    
                    // Attach events after the table is rendered
                    initializeSearchEvents();
                } else {
                    $('#hfy-seo-table-placeholder').html('<p class="error">' + (response.data || hfyPermalinksData.strings.error) + '</p>');
                }
            },
            error: function() {
                $('#hfy-seo-table-placeholder').html('<p class="error">' + hfyPermalinksData.strings.error + '</p>');
            }
        });
    }
    
    /**
     * Render the initial table structure
     */
    function renderInitialTable(data) {
        let html = '<div class="hfy-seo-table-wrapper">';
        
        // Add notice about regenerating permalinks
        html += `
            <div class="hfy-permalink-notice notice notice-info">
                <p><strong>Note:</strong> If you change the Listing Slug type, please press Save Settings then press the Regenerate button to update the table data.</p>
            </div>
        `;
        
        // Add search box
        html += `
            <div class="hfy-search-box">
                <input type="text" id="hfy-permalink-search" placeholder="Search by ID or name...">
                <button id="hfy-permalink-search-btn" class="button">Search</button>
                <button id="hfy-permalink-clear-btn" class="button">Clear</button>
            </div>
        `;
        
        // Add table container
        html += '<div id="hfy-table-content"></div>';
        
        // Add progress container
        html += '<div id="hfy-regeneration-progress" class="hfy-regeneration-progress" style="display:none;"></div>';
        
        html += '</div>';
        
        // Set initial HTML
        $('#hfy-seo-table-placeholder').html(html);
        
        // Render the initial table data
        renderTableContent(data);
    }
    
    /**
     * Initialize all search events
     */
    function initializeSearchEvents() {
        // Search button
        $(document).off('click', '#hfy-permalink-search-btn').on('click', '#hfy-permalink-search-btn', function(e) {
            e.preventDefault();
            performSearch();
        });
        
        // Clear button
        $(document).off('click', '#hfy-permalink-clear-btn').on('click', '#hfy-permalink-clear-btn', function(e) {
            e.preventDefault();
            $('#hfy-permalink-search').val('');
            performSearch();
        });
        
        // Enter key
        $(document).off('keydown', '#hfy-permalink-search').on('keydown', '#hfy-permalink-search', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                performSearch();
            }
        });
        
        // Handle paste events
        $(document).off('paste', '#hfy-permalink-search').on('paste', '#hfy-permalink-search', function() {
            // Use setTimeout to get the value after paste is complete
            setTimeout(function() {
                performSearch();
            }, 100);
        });
        
        // Handle input events with debounce
        $(document).off('input', '#hfy-permalink-search').on('input', '#hfy-permalink-search', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performSearch();
            }, 400); // Longer delay for better performance
        });
        
        // Regenerate button
        $(document).off('click', '#hfy-regenerate-permalinks').on('click', '#hfy-regenerate-permalinks', function(e) {
            e.preventDefault();
            regeneratePermalinks($(this));
        });
    }
    
    /**
     * Perform search and update results
     */
    function performSearch() {
        const searchValue = $('#hfy-permalink-search').val().trim().toLowerCase();
        
        if (!isDataLoaded || allPermalinks.length === 0) {
            return;
        }
        
        // Track search processing time
        const searchStartTime = Date.now();
        
        // Original search implementation using filter
        const filteredPermalinks = !searchValue ? allPermalinks : 
            allPermalinks.filter(function(permalink) {
                return String(permalink.listing_id).toLowerCase().includes(searchValue) || 
                       (permalink.listing_name && permalink.listing_name.toLowerCase().includes(searchValue)) ||
                       (permalink.permalink && permalink.permalink.toLowerCase().includes(searchValue)) ||
                       (permalink.permalink_url && permalink.permalink_url.toLowerCase().includes(searchValue));
            });
        
        // Calculate search processing time
        const searchProcessingTime = (Date.now() - searchStartTime) / 1000;
        
        // Create data object
        const filteredData = {
            permalinks: filteredPermalinks,
            total_count: allPermalinks.length,
            found_count: filteredPermalinks.length,
            search_query: searchValue,
            search_time: searchProcessingTime.toFixed(3)
        };
        
        // Keep the server processing time from the original data
        $(document).data('searchProcessingTime', searchProcessingTime);
        
        // Render results
        renderTableContent(filteredData);
    }

    /**
     * Render the table content
     * 
     * @param {Object} data The data containing permalinks and counts
     */
    function renderTableContent(data) {
        // Extract permalinks and count data
        const permalinks = data.permalinks || [];
        const totalCount = data.total_count || 0;
        const foundCount = data.found_count || 0;
        const searchQuery = data.search_query || '';
        const searchTime = data.search_time || null;
        const regenerationTime = data.regeneration_time || $(document).data('regenerationTime') || null;
        const regenerationServerTime = data.regeneration_server_time || $(document).data('regenerationServerTime') || null;
        
        let tableHtml = '';
        
        // No data
        if (!permalinks || permalinks.length === 0) {
            tableHtml += '<p class="no-data">' + hfyPermalinksData.strings.no_data + '</p>';
        } else {
            // Create table
            tableHtml += `
                <div class="hfy-seo-table-container">
                    <table class="hfy-seo-permalinks-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Listing ID</th>
                                <th>${hfyPermalinksData.use_nickname ? 'Listing Nickname' : 'Listing Name'}</th>
                                <th>Permalink</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            // Add rows
            permalinks.forEach(function(permalink, index) {
                // Use the permalink URL from the data or fallback to building it
                const fullUrl = permalink.permalink_url || (hfyPermalinksData.site_url + permalink.permalink);
                
                // If we have a search query, highlight matches
                let listingId = permalink.listing_id;
                let listingName = permalink.listing_name || 'N/A';
                let permalinkText = fullUrl;
                
                if (searchQuery) {
                    // Highlight matching parts
                    if (String(listingId).toLowerCase().includes(searchQuery.toLowerCase())) {
                        listingId = highlightText(String(listingId), searchQuery);
                    }
                    
                    if (listingName.toLowerCase().includes(searchQuery.toLowerCase())) {
                        listingName = highlightText(listingName, searchQuery);
                    }
                    
                    if (permalinkText.toLowerCase().includes(searchQuery.toLowerCase())) {
                        permalinkText = highlightText(permalinkText, searchQuery);
                    }
                }
                
                tableHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${listingId}</td>
                        <td>${listingName}</td>
                        <td>
                            <a href="${fullUrl}" class="permalink-link" target="_blank" rel="noopener noreferrer">
                                ${permalinkText}
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            // Close table
            tableHtml += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        // Add bottom row with count info and regenerate button
        tableHtml += `
            <div class="hfy-bottom-row">
                <div class="hfy-left-section">
                    <button id="hfy-regenerate-permalinks" class="hfy-permalinks-regenerate-btn">
                        <span class="dashicon dashicons dashicons-update"></span>${hfyPermalinksData.strings.regenerate_button}
                    </button>
                    <div class="hfy-processing-times">
                        <span class="hfy-time-label">Table load time:</span> <span class="hfy-time-value">${formatTime($(document).data('tableLoadTime') || 0)}</span> | 
                        <span class="hfy-time-label">Query time:</span> <span class="hfy-time-value">${($(document).data('serverProcessingTime') || 0).toFixed(2)}s</span>
                        ${searchTime ? ` | <span class="hfy-time-label">Search time:</span> <span class="hfy-time-value">${searchTime}s</span>` : ''}
                        ${regenerationTime ? `<br><span class="hfy-time-label">Last regeneration:</span> <span class="hfy-time-value">${formatTime(regenerationTime)}</span> | 
                        <span class="hfy-time-label">Regeneration server time:</span> <span class="hfy-time-value">${regenerationServerTime.toFixed(2)}s</span>` : ''}
                    </div>
                </div>
                <div class="hfy-count-info">
                    Found ${foundCount} ${foundCount !== totalCount ? ' out of ' + totalCount : ''} total
                </div>
            </div>
        `;
        
        // Render the table content only
        $('#hfy-table-content').html(tableHtml);
    }
    
    /**
     * Highlight search text in a string
     * 
     * @param {String} text The original text
     * @param {String} query The search query to highlight
     * @return {String} HTML with highlighted text
     */
    function highlightText(text, query) {
        if (!query) return text;
        
        // Case insensitive search
        const regex = new RegExp('(' + escapeRegExp(query) + ')', 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }
    
    /**
     * Escape special characters for use in regex
     * 
     * @param {String} string The string to escape
     * @return {String} Escaped string
     */
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    /**
     * Stop all timers and cleanup
     */
    function stopAllTimers() {
        // Clear the regeneration timer
        if (regenerationTimer) {
            clearInterval(regenerationTimer);
            regenerationTimer = null;
        }
        
        // Clear the progress check timer
        const progressCheckTimer = $(document).data('progressCheckTimer');
        if (progressCheckTimer) {
            clearInterval(progressCheckTimer);
            $(document).data('progressCheckTimer', null);
        }
    }
    
    /**
     * Regenerate the permalinks
     * 
     * @param {Object} button The button element that was clicked
     */
    function regeneratePermalinks(button) {
        // Stop any existing timers first
        stopAllTimers();
        
        // Show loading state
        button.addClass('loading').html('<span class="dashicon dashicons dashicons-update dashicons-spin"></span>' + hfyPermalinksData.strings.regenerating).prop('disabled', true);
        
        // Show and initialize progress container
        const progressContainer = $('#hfy-regeneration-progress');
        progressContainer.html(`
            <div class="hfy-progress-info">
                <div class="hfy-progress-bar-container">
                    <div class="hfy-progress-bar" style="width: 0%"></div>
                </div>
                <div class="hfy-progress-text">
                    <span class="hfy-elapsed-time">0s</span> | 
                    <span class="hfy-processed-count">0</span> listings processed
                </div>
            </div>
        `).show();
        
        // Start counting time
        regenerationStartTime = Date.now();
        startRegenerationTimer();
        
        // Start checking progress
        checkRegenerationProgress();
        
        // Call the regenerate AJAX endpoint
        $.ajax({
            url: hfyPermalinksData.ajax_url,
            type: 'POST',
            data: {
                action: 'hfy_regenerate_permalinks',
                nonce: hfyPermalinksData.nonce
            },
            success: function(response) {
                // Stop the timer
                stopAllTimers();
                
                if (response.success) {
                    // Update progress with final count
                    const count = response.data.count || 0;
                    const newItems = response.data.new_items || 0;
                    const processingTime = response.data.processing_time || 0;
                    updateProgressBar(100, count);
                    
                    // Show success message and reload table
                    button.html('<span class="dashicon dashicons dashicons-yes"></span>' + hfyPermalinksData.strings.regenerate_success);
                    
                    // Add final message to progress container
                    const elapsedTime = Math.round((Date.now() - regenerationStartTime) / 1000);
                    progressContainer.append(`
                        <div class="hfy-progress-complete">
                            <p>✅ Successfully regenerated ${count} permalinks (${newItems} new) in ${formatTime(elapsedTime)}</p>
                            <p class="hfy-server-time">Regeneration server time: ${processingTime.toFixed(2)} seconds</p>
                        </div>
                    `);
                    
                    setTimeout(function() {
                        // Save the current processing times before resetting
                        const savedRegenerationTime = elapsedTime;
                        const savedServerProcessingTime = processingTime;
                        
                        // Reset data for table reload
                        isDataLoaded = false;
                        
                        // Load the table with additional data
                        initPermalinksTableAfterRegeneration(savedRegenerationTime, savedServerProcessingTime);
                    }, 2000);
                } else {
                    // Show error
                    progressContainer.append(`
                        <div class="hfy-progress-error">
                            <p>❌ Error: ${response.data || hfyPermalinksData.strings.error}</p>
                        </div>
                    `);
                    
                    button.removeClass('loading').html('<span class="dashicon dashicons dashicons-warning"></span>' + hfyPermalinksData.strings.regenerate_error).prop('disabled', false);
                    alert(response.data || hfyPermalinksData.strings.error);
                }
            },
            error: function() {
                // Stop the timer
                stopAllTimers();
                
                // Show error
                progressContainer.append(`
                    <div class="hfy-progress-error">
                        <p>❌ Error: Network or server error</p>
                    </div>
                `);
                
                button.removeClass('loading').html('<span class="dashicon dashicons dashicons-warning"></span>' + hfyPermalinksData.strings.regenerate_error).prop('disabled', false);
                alert(hfyPermalinksData.strings.error);
            }
        });
    }
    
    /**
     * Start the timer for tracking regeneration time
     */
    function startRegenerationTimer() {
        // Clear any existing timer
        if (regenerationTimer) {
            clearInterval(regenerationTimer);
        }
        
        // Start a new timer
        regenerationTimer = setInterval(function() {
            const elapsedSeconds = Math.round((Date.now() - regenerationStartTime) / 1000);
            $('.hfy-elapsed-time').text(formatTime(elapsedSeconds));
        }, 1000);
    }
    
    /**
     * Format seconds into a human-readable time string
     */
    function formatTime(seconds) {
        if (seconds < 60) {
            return seconds + 's';
        } else {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return minutes + 'm ' + remainingSeconds + 's';
        }
    }
    
    /**
     * Check regeneration progress
     */
    function checkRegenerationProgress() {
        let lastCount = 0;
        let progressCheckTimer = setInterval(function() {
            $.ajax({
                url: hfyPermalinksData.ajax_url,
                type: 'POST',
                data: {
                    action: 'hfy_get_permalinks_data',
                    nonce: hfyPermalinksData.nonce,
                    search_query: ''
                },
                success: function(response) {
                    if (response.success) {
                        const count = response.data.total_count || 0;
                        if (count > lastCount) {
                            lastCount = count;
                            // Estimate progress (can't know total, so we're simulating)
                            const progress = Math.min(95, Math.floor((count / (count + 10)) * 100));
                            updateProgressBar(progress, count);
                        }
                    }
                }
            });
        }, 2000); // Check every 2 seconds
        
        // Store the timer so we can clear it when done
        $(document).data('progressCheckTimer', progressCheckTimer);
    }
    
    /**
     * Update the progress bar and count
     */
    function updateProgressBar(percent, count) {
        $('.hfy-progress-bar').css('width', percent + '%');
        $('.hfy-processed-count').text(count);
    }

    /**
     * Initialize the table after regeneration with preserved timing info
     * 
     * @param {Number} regenerationTime The time it took to regenerate the permalinks
     * @param {Number} serverProcessingTime The server processing time for regeneration
     */
    function initPermalinksTableAfterRegeneration(regenerationTime, serverProcessingTime) {
        // Track table loading start time
        const tableLoadStartTime = Date.now();
        
        // Show loading message
        $('#hfy-seo-table-placeholder').html('<p class="loading">' + hfyPermalinksData.strings.loading + '</p>');
        
        // Hide the regeneration progress
        $('#hfy-regeneration-progress').hide();
        
        // Fetch initial data - this call won't attach events until data is loaded
        $.ajax({
            url: hfyPermalinksData.ajax_url,
            type: 'POST',
            data: {
                action: 'hfy_get_permalinks_data',
                nonce: hfyPermalinksData.nonce,
                search_query: ''
            },
            success: function(response) {
                // Calculate table load time
                const tableLoadTime = Math.round((Date.now() - tableLoadStartTime) / 1000);
                
                if (response.success) {
                    // Store data for client-side filtering
                    allPermalinks = response.data.permalinks || [];
                    isDataLoaded = true;
                    
                    // Store timing data
                    $(document).data('tableLoadTime', tableLoadTime);
                    $(document).data('serverProcessingTime', response.data.server_time || 0);
                    $(document).data('regenerationTime', regenerationTime);
                    $(document).data('regenerationServerTime', serverProcessingTime);
                    
                    // Add regeneration data to the response
                    response.data.regeneration_time = regenerationTime;
                    response.data.regeneration_server_time = serverProcessingTime;
                    
                    // Now render the table with the initial data
                    renderInitialTable(response.data);
                    
                    // Attach events after the table is rendered
                    initializeSearchEvents();
                } else {
                    $('#hfy-seo-table-placeholder').html('<p class="error">' + (response.data || hfyPermalinksData.strings.error) + '</p>');
                }
            },
            error: function() {
                $('#hfy-seo-table-placeholder').html('<p class="error">' + hfyPermalinksData.strings.error + '</p>');
            }
        });
    }

})(jQuery); 