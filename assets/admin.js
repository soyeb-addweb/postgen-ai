/**
 * SmartWriter AI Admin JavaScript
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * SmartWriter AI Admin Class
     */
    var SmartWriterAdmin = {
        
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.bindEvents();
            this.initModals();
            this.initTooltips();
            this.updateHealthScore();
        },
        
        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Settings form events
            $(document).on('change', '#api_provider', this.handleProviderChange);
            $(document).on('click', '#test-api-connection', this.testApiConnection);
            $(document).on('click', '#preview-content', this.previewContent);
            
            // Toggle events
            $(document).on('change', '#generate_images_toggle', this.toggleImageSettings);
            $(document).on('change', '#enable_backdate_toggle', this.toggleBackdateSettings);
            
            // Quick action events
            $(document).on('click', '#test-content-generation', this.showTestModal);
            $(document).on('click', '#run-test-generation', this.runTestGeneration);
            $(document).on('click', '#force-run-scheduler', this.forceRunScheduler);
            
            // Modal events
            $(document).on('click', '.modal-close', this.closeModal);
            $(document).on('click', '.smartwriter-modal', this.closeModalOnBackdrop);
            
            // Tab events
            $(document).on('click', '.nav-tab', this.switchTab);
            
            // Form validation
            $(document).on('submit', '#smartwriter-settings-form', this.validateSettingsForm);
            
            // Auto-save functionality
            $(document).on('change input', '#smartwriter-settings-form :input', 
                this.debounce(this.autoSave, 2000));
        },
        
        /**
         * Initialize modals
         */
        initModals: function() {
            // Close modal on Escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) { // Escape key
                    $('.smartwriter-modal').fadeOut();
                }
            });
        },
        
        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            // Add tooltips to help icons
            $('[data-tooltip]').on('mouseenter', function() {
                var tooltip = $('<div class="smartwriter-tooltip">' + 
                    $(this).data('tooltip') + '</div>');
                $('body').append(tooltip);
                
                var offset = $(this).offset();
                tooltip.css({
                    top: offset.top - tooltip.outerHeight() - 5,
                    left: offset.left + ($(this).outerWidth() / 2) - (tooltip.outerWidth() / 2)
                }).fadeIn();
            }).on('mouseleave', function() {
                $('.smartwriter-tooltip').remove();
            });
        },
        
        /**
         * Update health score visual
         */
        updateHealthScore: function() {
            var scoreElement = $('.score-circle');
            if (scoreElement.length) {
                var score = parseInt($('.score-number').text());
                var degrees = (score / 100) * 360;
                var color = score >= 80 ? '#28a745' : (score >= 60 ? '#ffc107' : '#dc3545');
                
                scoreElement.css({
                    '--score-deg': degrees + 'deg',
                    'background': 'conic-gradient(' + color + ' 0deg, ' + color + ' ' + 
                        degrees + 'deg, #e9ecef ' + degrees + 'deg)'
                });
            }
        },
        
        /**
         * Handle API provider change
         */
        handleProviderChange: function() {
            var provider = $(this).val();
            $('.api-provider-instructions').hide();
            $('.api-provider-instructions[data-provider="' + provider + '"]').show();
            
            // Update model options based on provider
            SmartWriterAdmin.updateModelOptions(provider);
        },
        
        /**
         * Update model options
         */
        updateModelOptions: function(provider) {
            var modelSelect = $('#model');
            var options = {
                'perplexity': [
                    { value: 'llama-3.1-sonar-small-128k-online', text: 'Llama 3.1 Sonar Small (Recommended)' },
                    { value: 'llama-3.1-sonar-large-128k-online', text: 'Llama 3.1 Sonar Large' },
                    { value: 'llama-3.1-sonar-huge-128k-online', text: 'Llama 3.1 Sonar Huge' }
                ],
                'openai': [
                    { value: 'gpt-3.5-turbo', text: 'GPT-3.5 Turbo' },
                    { value: 'gpt-4', text: 'GPT-4' },
                    { value: 'gpt-4-turbo', text: 'GPT-4 Turbo' }
                ],
                'anthropic': [
                    { value: 'claude-3-sonnet-20240229', text: 'Claude 3 Sonnet' },
                    { value: 'claude-3-opus-20240229', text: 'Claude 3 Opus' },
                    { value: 'claude-3-haiku-20240307', text: 'Claude 3 Haiku' }
                ]
            };
            
            if (options[provider]) {
                modelSelect.empty();
                options[provider].forEach(function(option) {
                    modelSelect.append('<option value="' + option.value + '">' + 
                        option.text + '</option>');
                });
            }
        },
        
        /**
         * Test API connection
         */
        testApiConnection: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var apiKey = $('#api_key').val();
            var provider = $('#api_provider').val();
            
            if (!apiKey) {
                SmartWriterAdmin.showNotice('error', smartwriterAjax.strings.api_key_required);
                return;
            }
            
            SmartWriterAdmin.setButtonLoading($button, smartwriterAjax.strings.testing);
            
            $.ajax({
                url: smartwriterAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartwriter_test_api',
                    nonce: smartwriterAjax.nonce,
                    api_key: apiKey,
                    provider: provider
                },
                success: function(response) {
                    if (response.success) {
                        $('#api-test-result').html(
                            '<div class="notice notice-success inline"><p>' + 
                            response.data.message + '</p></div>'
                        );
                        
                        // Update model options if available
                        if (response.data.models) {
                            SmartWriterAdmin.updateAvailableModels(response.data.models);
                        }
                    } else {
                        $('#api-test-result').html(
                            '<div class="notice notice-error inline"><p>' + 
                            response.data + '</p></div>'
                        );
                    }
                },
                error: function() {
                    $('#api-test-result').html(
                        '<div class="notice notice-error inline"><p>' + 
                        smartwriterAjax.strings.error + '</p></div>'
                    );
                },
                complete: function() {
                    SmartWriterAdmin.resetButton($button, 'Test Connection');
                }
            });
        },
        
        /**
         * Preview content generation
         */
        previewContent: function(e) {
            e.preventDefault();
            
            var prompt = $('#prompt_template').val();
            if (!prompt) {
                SmartWriterAdmin.showNotice('error', 'Please enter a prompt template first.');
                return;
            }
            
            $('#settings-preview-modal').fadeIn();
            $('#preview-loading').show();
            $('#preview-content').empty();
            
            $.ajax({
                url: smartwriterAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartwriter_preview_content',
                    nonce: smartwriterAjax.nonce,
                    prompt: prompt
                },
                success: function(response) {
                    $('#preview-loading').hide();
                    
                    if (response.success) {
                        var content = response.data;
                        var html = SmartWriterAdmin.formatPreviewContent(content);
                        $('#preview-content').html(html);
                    } else {
                        $('#preview-content').html(
                            '<div class="notice notice-error"><p>Error: ' + 
                            response.data + '</p></div>'
                        );
                    }
                },
                error: function() {
                    $('#preview-loading').hide();
                    $('#preview-content').html(
                        '<div class="notice notice-error"><p>Failed to generate preview.</p></div>'
                    );
                }
            });
        },
        
        /**
         * Format preview content for display
         */
        formatPreviewContent: function(content) {
            var html = '<div class="preview-result">';
            html += '<h4>' + SmartWriterAdmin.escapeHtml(content.title) + '</h4>';
            html += '<p><strong>Meta Description:</strong> ' + 
                SmartWriterAdmin.escapeHtml(content.meta_description) + '</p>';
            html += '<p><strong>Focus Keyword:</strong> ' + 
                SmartWriterAdmin.escapeHtml(content.focus_keyword) + '</p>';
            
            if (content.tags && content.tags.length > 0) {
                html += '<p><strong>Tags:</strong> ' + 
                    content.tags.map(SmartWriterAdmin.escapeHtml).join(', ') + '</p>';
            }
            
            html += '<div class="content-preview"><strong>Content Preview:</strong><br>' + 
                SmartWriterAdmin.escapeHtml(content.content.substring(0, 500)) + 
                (content.content.length > 500 ? '...' : '') + '</div>';
            html += '</div>';
            
            return html;
        },
        
        /**
         * Toggle image settings visibility
         */
        toggleImageSettings: function() {
            if ($(this).is(':checked')) {
                $('#image_api_settings').slideDown();
            } else {
                $('#image_api_settings').slideUp();
            }
        },
        
        /**
         * Toggle backdate settings visibility
         */
        toggleBackdateSettings: function() {
            if ($(this).is(':checked')) {
                $('#backdate_settings').slideDown();
            } else {
                $('#backdate_settings').slideUp();
            }
        },
        
        /**
         * Show test generation modal
         */
        showTestModal: function(e) {
            e.preventDefault();
            $('#test-generation-modal').fadeIn();
            $('#test-topic').focus();
        },
        
        /**
         * Run test content generation
         */
        runTestGeneration: function(e) {
            e.preventDefault();
            
            var topic = $('#test-topic').val();
            if (!topic) {
                SmartWriterAdmin.showNotice('error', 'Please enter a topic to test.');
                return;
            }
            
            var $button = $(this);
            SmartWriterAdmin.setButtonLoading($button, smartwriterAjax.strings.generating);
            
            $.ajax({
                url: smartwriterAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartwriter_preview_content',
                    nonce: smartwriterAjax.nonce,
                    prompt: 'Write a test blog post about ' + topic
                },
                success: function(response) {
                    if (response.success) {
                        var content = response.data;
                        var html = SmartWriterAdmin.formatPreviewContent(content);
                        $('#test-generation-result').html(html);
                    } else {
                        $('#test-generation-result').html(
                            '<div class="test-result-error">Error: ' + response.data + '</div>'
                        );
                    }
                },
                error: function() {
                    $('#test-generation-result').html(
                        '<div class="test-result-error">Failed to generate content.</div>'
                    );
                },
                complete: function() {
                    SmartWriterAdmin.resetButton($button, 'Generate Test Content');
                }
            });
        },
        
        /**
         * Force run scheduler
         */
        forceRunScheduler: function(e) {
            e.preventDefault();
            
            if (!confirm(smartwriterAjax.strings.confirm_schedule)) {
                return;
            }
            
            var $button = $(this);
            SmartWriterAdmin.setButtonLoading($button, 'Running...');
            
            $.ajax({
                url: smartwriterAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartwriter_force_run_scheduler',
                    nonce: smartwriterAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SmartWriterAdmin.showNotice('success', 'Scheduler run completed successfully!');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        SmartWriterAdmin.showNotice('error', 'Scheduler run failed: ' + response.data);
                    }
                },
                error: function() {
                    SmartWriterAdmin.showNotice('error', 'Failed to run scheduler.');
                },
                complete: function() {
                    SmartWriterAdmin.resetButton($button, 'Force Run');
                }
            });
        },
        
        /**
         * Close modal
         */
        closeModal: function(e) {
            e.preventDefault();
            $(this).closest('.smartwriter-modal').fadeOut();
        },
        
        /**
         * Close modal on backdrop click
         */
        closeModalOnBackdrop: function(e) {
            if (e.target === this) {
                $(this).fadeOut();
            }
        },
        
        /**
         * Switch tabs
         */
        switchTab: function(e) {
            e.preventDefault();
            
            var target = $(this).attr('href');
            
            // Update active tab
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show/hide content
            $('.smartwriter-tab-content').removeClass('active');
            $(target).addClass('active');
            
            // Save active tab to localStorage
            localStorage.setItem('smartwriter_active_tab', target);
        },
        
        /**
         * Validate settings form
         */
        validateSettingsForm: function(e) {
            var errors = [];
            
            // Validate API key
            var apiKey = $('#api_key').val();
            if (!apiKey) {
                errors.push('API key is required.');
            }
            
            // Validate prompt template
            var promptTemplate = $('#prompt_template').val();
            if (!promptTemplate) {
                errors.push('Prompt template is required.');
            }
            
            // Validate posts per day
            var postsPerDay = parseInt($('#posts_per_day').val());
            if (postsPerDay < 1 || postsPerDay > 24) {
                errors.push('Posts per day must be between 1 and 24.');
            }
            
            // Validate time range
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            if (startTime && endTime && startTime >= endTime) {
                errors.push('Start time must be before end time.');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                SmartWriterAdmin.showNotice('error', errors.join('<br>'));
                return false;
            }
            
            return true;
        },
        
        /**
         * Auto-save settings
         */
        autoSave: function() {
            var formData = $('#smartwriter-settings-form').serialize();
            
            $.ajax({
                url: smartwriterAjax.ajaxurl,
                type: 'POST',
                data: formData + '&action=smartwriter_auto_save&nonce=' + smartwriterAjax.nonce,
                success: function(response) {
                    if (response.success) {
                        SmartWriterAdmin.showAutoSaveIndicator();
                    }
                }
            });
        },
        
        /**
         * Show auto-save indicator
         */
        showAutoSaveIndicator: function() {
            var indicator = $('<div class="auto-save-indicator">Settings saved</div>');
            $('body').append(indicator);
            
            indicator.fadeIn().delay(2000).fadeOut(function() {
                $(this).remove();
            });
        },
        
        /**
         * Set button loading state
         */
        setButtonLoading: function($button, text) {
            $button.data('original-text', $button.text())
                   .text(text)
                   .prop('disabled', true)
                   .addClass('loading');
        },
        
        /**
         * Reset button state
         */
        resetButton: function($button, text) {
            $button.text(text || $button.data('original-text'))
                   .prop('disabled', false)
                   .removeClass('loading');
        },
        
        /**
         * Show notice
         */
        showNotice: function(type, message) {
            var notice = $('<div class="notice notice-' + type + ' is-dismissible">' +
                '<p>' + message + '</p>' +
                '<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
                '</button></div>');
            
            $('.wrap').prepend(notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Handle manual dismiss
            notice.on('click', '.notice-dismiss', function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            });
        },
        
        /**
         * Escape HTML
         */
        escapeHtml: function(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        },
        
        /**
         * Debounce function
         */
        debounce: function(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },
        
        /**
         * Update available models
         */
        updateAvailableModels: function(models) {
            var modelSelect = $('#model');
            var currentValue = modelSelect.val();
            
            modelSelect.empty();
            models.forEach(function(model) {
                modelSelect.append('<option value="' + model + '">' + model + '</option>');
            });
            
            // Restore selected value if it exists
            if (models.indexOf(currentValue) !== -1) {
                modelSelect.val(currentValue);
            }
        },
        
        /**
         * Restore active tab from localStorage
         */
        restoreActiveTab: function() {
            var activeTab = localStorage.getItem('smartwriter_active_tab');
            if (activeTab) {
                $('.nav-tab[href="' + activeTab + '"]').trigger('click');
            }
        }
    };
    
    /**
     * Real-time validation
     */
    var Validation = {
        
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            $('#api_key').on('input', this.validateApiKey);
            $('#posts_per_day').on('input', this.validatePostsPerDay);
            $('#start_time, #end_time').on('change', this.validateTimeRange);
            $('#prompt_template').on('input', this.validatePromptTemplate);
        },
        
        validateApiKey: function() {
            var value = $(this).val();
            var $field = $(this);
            
            if (value.length < 10) {
                Validation.showFieldError($field, 'API key seems too short');
            } else {
                Validation.clearFieldError($field);
            }
        },
        
        validatePostsPerDay: function() {
            var value = parseInt($(this).val());
            var $field = $(this);
            
            if (value < 1 || value > 24) {
                Validation.showFieldError($field, 'Must be between 1 and 24');
            } else {
                Validation.clearFieldError($field);
            }
        },
        
        validateTimeRange: function() {
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            
            if (startTime && endTime && startTime >= endTime) {
                Validation.showFieldError($('#end_time'), 'End time must be after start time');
            } else {
                Validation.clearFieldError($('#start_time'));
                Validation.clearFieldError($('#end_time'));
            }
        },
        
        validatePromptTemplate: function() {
            var value = $(this).val();
            var $field = $(this);
            
            if (value.length < 10) {
                Validation.showFieldError($field, 'Prompt template seems too short');
            } else {
                Validation.clearFieldError($field);
            }
        },
        
        showFieldError: function($field, message) {
            $field.addClass('error');
            var errorId = $field.attr('id') + '_error';
            $('#' + errorId).remove();
            $field.after('<div id="' + errorId + '" class="field-error">' + message + '</div>');
        },
        
        clearFieldError: function($field) {
            $field.removeClass('error');
            var errorId = $field.attr('id') + '_error';
            $('#' + errorId).remove();
        }
    };
    
    /**
     * Dashboard widgets
     */
    var Dashboard = {
        
        init: function() {
            this.initCharts();
            this.initRealTimeUpdates();
        },
        
        initCharts: function() {
            // Initialize any charts or graphs
            this.updateHealthScoreAnimation();
        },
        
        updateHealthScoreAnimation: function() {
            var $scoreCircle = $('.score-circle');
            if ($scoreCircle.length) {
                var score = parseInt($('.score-number').text());
                this.animateHealthScore($scoreCircle, score);
            }
        },
        
        animateHealthScore: function($element, score) {
            var degrees = 0;
            var targetDegrees = (score / 100) * 360;
            var color = score >= 80 ? '#28a745' : (score >= 60 ? '#ffc107' : '#dc3545');
            
            var animation = setInterval(function() {
                degrees += 5;
                if (degrees >= targetDegrees) {
                    degrees = targetDegrees;
                    clearInterval(animation);
                }
                
                $element.css({
                    'background': 'conic-gradient(' + color + ' 0deg, ' + color + ' ' + 
                        degrees + 'deg, #e9ecef ' + degrees + 'deg)'
                });
            }, 20);
        },
        
        initRealTimeUpdates: function() {
            // Update dashboard data every 5 minutes
            setInterval(function() {
                Dashboard.updateDashboardData();
            }, 300000); // 5 minutes
        },
        
        updateDashboardData: function() {
            $.ajax({
                url: smartwriterAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartwriter_get_dashboard_data',
                    nonce: smartwriterAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        Dashboard.updateDashboardElements(response.data);
                    }
                }
            });
        },
        
        updateDashboardElements: function(data) {
            // Update health score
            if (data.health) {
                $('.score-number').text(data.health.score);
                $('.score-status h3').text(data.health.status);
            }
            
            // Update stats
            if (data.stats) {
                // Update log entries count
                $('.smartwriter-stat-card:last .stat-content h3').text(data.stats.total);
            }
        }
    };
    
    /**
     * Initialize everything when document is ready
     */
    $(document).ready(function() {
        SmartWriterAdmin.init();
        Validation.init();
        Dashboard.init();
        
        // Restore active tab
        SmartWriterAdmin.restoreActiveTab();
        
        // Add fade-in animation to cards
        $('.smartwriter-card').addClass('smartwriter-fadeIn');
        
        // Initialize any existing toggle states
        if ($('#generate_images_toggle').is(':checked')) {
            $('#image_api_settings').show();
        }
        
        if ($('#enable_backdate_toggle').is(':checked')) {
            $('#backdate_settings').show();
        }
    });
    
})(jQuery);