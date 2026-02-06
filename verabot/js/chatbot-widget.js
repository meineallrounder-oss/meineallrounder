// Chatbot Widget JavaScript - Standalone Version
(function() {
    'use strict';
    
    console.log('Chatbot widget script loaded');
    
    // API URL - Dinamički određuje putanju na osnovu trenutne lokacije
    function getApiUrl() {
        const path = window.location.pathname;
        const scripts = document.getElementsByTagName('script');
        let scriptPath = '';
        
        // Pronađi putanju trenutnog script-a
        for (let i = 0; i < scripts.length; i++) {
            if (scripts[i].src && scripts[i].src.includes('chatbot-widget.js')) {
                scriptPath = scripts[i].src;
                break;
            }
        }
        
        console.log('Script path found:', scriptPath);
        console.log('Current pathname:', path);
        
        // Ekstraktuj folder putanju iz script src
        if (scriptPath) {
            // Remove origin if present
            let relativePath = scriptPath;
            if (scriptPath.startsWith('http://') || scriptPath.startsWith('https://')) {
                relativePath = scriptPath.replace(window.location.origin, '');
            }
            
            // Extract base path (everything before /js/chatbot-widget.js)
            const match = relativePath.match(/(.*)\/js\/chatbot-widget\.js/);
            if (match) {
                const basePath = match[1];
                const apiPath = basePath + '/api.php';
                console.log('API URL from script path:', apiPath);
                return apiPath;
            }
            
            // Try alternative pattern (for different folder structures)
            const altMatch = relativePath.match(/(.*)\/verabot\/js\/chatbot-widget\.js/);
            if (altMatch) {
                const basePath = altMatch[1] || '';
                const apiPath = basePath + '/verabot/api.php';
                console.log('API URL from alternative pattern:', apiPath);
                return apiPath;
            }
        }
        
        // Fallback - calculate based on current page path
        // Count directory depth (excluding root)
        const pathParts = path.split('/').filter(p => p && p !== 'index.html' && !p.includes('.html'));
        const depth = pathParts.length;
        
        console.log('Path depth:', depth, 'Path parts:', pathParts);
        
        // Build relative path to verabot folder
        if (depth === 0) {
            // Root level
            return 'verabot/api.php';
        } else {
            // In subfolder - go up N levels
            const upLevels = '../'.repeat(depth);
            return upLevels + 'verabot/api.php';
        }
    }
    
    // Check if we're on Vercel - use Node.js API endpoint
    let API_URL = getApiUrl();
    
    // If on Vercel (check for vercel.app domain or custom domain), try Node.js endpoint first
    if (window.location.hostname.includes('vercel.app') || window.location.hostname.includes('meineallrounder.de')) {
        // Try Node.js API endpoint first (for Vercel)
        const nodeApiUrl = '/api/chatbot';
        console.log('Detected Vercel deployment, trying Node.js API:', nodeApiUrl);
        // We'll test this URL first, but keep the PHP URL as fallback
        API_URL = nodeApiUrl;
    }
    
    console.log('Chatbot API URL:', API_URL);
    
    // Load settings from server or localStorage
    let chatbotSettings = null;
    
    async function loadChatbotSettings() {
        // First, try to load from localStorage (set by admin panel)
        try {
            const localSettings = localStorage.getItem('chatbotSettings');
            if (localSettings) {
                chatbotSettings = JSON.parse(localSettings);
                console.log('Loaded chatbot settings from localStorage');
                applyChatbotSettings();
                return true;
            }
        } catch (e) {
            console.warn('Could not load settings from localStorage:', e);
        }
        
        // Fallback: try to load from server API
        try {
            const settingsUrl = API_URL.replace('/api.php', '/api-settings.php');
            const response = await fetch(settingsUrl);
            if (response.ok) {
                chatbotSettings = await response.json();
                applyChatbotSettings();
            } else {
                console.warn('Could not load chatbot settings from server, using defaults');
            }
        } catch (e) {
            console.warn('Could not load chatbot settings from server:', e);
            // Continue without settings - use defaults
        }
        return true; // Always return true so initialization continues
    }
    
    // Apply settings to CSS
    function applyChatbotSettings(customSettings = null) {
        // Use custom settings if provided, otherwise use loaded settings, otherwise defaults
        let settings = customSettings;
        
        if (!settings) {
            // Try localStorage first
            try {
                const localSettings = localStorage.getItem('chatbotSettings');
                if (localSettings) {
                    settings = JSON.parse(localSettings);
                }
            } catch (e) {
                console.warn('Could not load from localStorage:', e);
            }
        }
        
        if (!settings) {
            settings = chatbotSettings;
        }
        
        // Use default settings if not loaded yet
        if (!settings) {
            settings = {
                headerColor: '#ea580c',
                headerColorSecondary: '#fb923c',
                userMessageColor: '#ea580c',
                userMessageColorSecondary: '#fb923c',
                toggleButtonColor: '#ea580c',
                backgroundColor: '#ffffff',
                universalColor: '#ea580c',
                iconEmoji: '💬',
                logoUrl: ''
            };
        }
        
        // Map settings to expected format (support both camelCase and snake_case)
        const mappedSettings = {
            header_color: settings.headerColor || settings.header_color || '#ea580c',
            header_color_secondary: settings.headerColorSecondary || settings.header_color_secondary || '#fb923c',
            user_message_color: settings.userMessageColor || settings.user_message_color || '#ea580c',
            user_message_color_secondary: settings.userMessageColorSecondary || settings.user_message_color_secondary || '#fb923c',
            toggle_button_color: settings.toggleButtonColor || settings.toggle_button_color || '#ea580c',
            background_color: settings.backgroundColor || settings.background_color || '#ffffff',
            universal_color: settings.universalColor || settings.universal_color || '#ea580c',
            icon_emoji: settings.iconEmoji || settings.icon_emoji || '💬',
            logo_url: settings.logoUrl || settings.logo_url || ''
        };
        
        settings = mappedSettings;
        
        const root = document.documentElement;
        
        // Set CSS variables
        root.style.setProperty('--chatbot-header-color', settings.header_color || '#ea580c');
        root.style.setProperty('--chatbot-header-color-secondary', settings.header_color_secondary || '#fb923c');
        root.style.setProperty('--chatbot-user-message-color', settings.user_message_color || '#ea580c');
        root.style.setProperty('--chatbot-user-message-color-secondary', settings.user_message_color_secondary || '#fb923c');
        root.style.setProperty('--chatbot-toggle-color', settings.toggle_button_color || '#ea580c');
        root.style.setProperty('--chatbot-bg-color', settings.background_color || '#ffffff');
        root.style.setProperty('--chatbot-universal-color', settings.universal_color || '#ea580c');
        
        // Update toggle button emoji
        const toggleEmoji = document.querySelector('.chatbot-toggle-emoji');
        if (toggleEmoji && settings.icon_emoji) {
            toggleEmoji.textContent = settings.icon_emoji;
        }
        
        // Update logo if set
        if (settings.logo_url) {
            const logoImg = document.querySelector('.chatbot-logo');
            if (logoImg) {
                logoImg.src = settings.logo_url;
                logoImg.style.display = 'block';
            }
        }
        
        // Store settings for external access
        chatbotSettings = settings;
    }
    
    // Export function for admin panel
    window.applyChatbotSettings = function(customSettings) {
        applyChatbotSettings(customSettings);
    };
    }
    
    // Function to create chatbot HTML structure if it doesn't exist
    function createChatbotHTML() {
        // Check if chatbot widget already exists
        if (document.getElementById('chatbot-widget')) {
            return;
        }
        
        // Get configuration from data attributes or use defaults
        const configScript = document.querySelector('script[data-chatbot-config]');
        let config = {
            companyName: 'Meine Allrounder',
            welcomeMessage: 'Hallo! Willkommen bei Meine Allrounder. Wie kann ich Ihnen heute helfen?',
            toggleText: 'KI Asistent',
            logoUrl: null
        };
        
        if (configScript) {
            try {
                let configText = configScript.textContent || configScript.innerHTML;
                configText = configText.trim();
                const customConfig = JSON.parse(configText);
                config = { ...config, ...customConfig };
            } catch (e) {
                console.warn('Error parsing chatbot config:', e);
            }
        }
        
        // Use icon from settings if available (with fallback)
        const iconEmoji = (chatbotSettings && chatbotSettings.icon_emoji) ? chatbotSettings.icon_emoji : '💬';
        const logoUrl = (chatbotSettings && chatbotSettings.logo_url) ? chatbotSettings.logo_url : (config.logoUrl || null);
        
        // Create widget HTML
        const widgetHTML = `
            <div id="chatbot-widget">
                <div id="chatbot-container">
                    <div id="chatbot-header">
                        <div class="chatbot-header-content">
                            <div class="chatbot-header-brand">
                                ${logoUrl ? 
                                    `<img src="${logoUrl}" alt="${config.companyName}" class="chatbot-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                     <div class="chatbot-logo-fallback" style="display: none;">
                                         <span class="logo-main">${config.companyName.split(' ')[0]}</span>
                                         <span class="logo-accent">${config.companyName.split(' ').slice(1).join(' ')}</span>
                                     </div>` :
                                    `<div class="chatbot-logo-fallback" style="display: flex;">
                                         <span class="logo-main">${config.companyName.split(' ')[0]}</span>
                                         <span class="logo-accent">${config.companyName.split(' ').slice(1).join(' ')}</span>
                                     </div>`
                                }
                                <div class="chatbot-header-text">
                                    <p>KI Asistent!</p>
                                </div>
                            </div>
                            <div class="chatbot-header-actions">
                                <button id="chatbot-minimize" class="chatbot-header-btn" title="Minimieren">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                </button>
                                <button id="chatbot-close" class="chatbot-header-btn" title="Schließen">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="chatbot-messages">
                        <div class="chatbot-message bot">
                            <div class="chatbot-message-content">
                                ${config.welcomeMessage}
                            </div>
                        </div>
                    </div>
                    <div id="chatbot-input-container">
                        <input type="text" id="chatbot-input" placeholder="Nachricht eingeben...">
                        <button id="chatbot-send">Senden</button>
                    </div>
                </div>
                <button id="chatbot-toggle">
                    <span class="chatbot-toggle-emoji">${iconEmoji}</span>
                    <span class="chatbot-toggle-text">${config.toggleText}</span>
                </button>
            </div>
        `;
        
        // Insert HTML before closing body tag
        if (document.body) {
            document.body.insertAdjacentHTML('beforeend', widgetHTML);
            console.log('Chatbot HTML inserted');
            
            // Verify widget was created
            const widget = document.getElementById('chatbot-widget');
            const toggle = document.getElementById('chatbot-toggle');
            console.log('Widget created:', !!widget);
            console.log('Toggle button created:', !!toggle);
            
            // Apply settings after HTML is inserted
            setTimeout(applyChatbotSettings, 100);
        } else {
            console.error('Cannot insert chatbot HTML: document.body not found');
        }
    }
    
    // States: 'closed', 'open', 'minimized'
    let state = 'closed';
    
    function initChatbot() {
        console.log('Initializing chatbot...');
        console.log('Document ready state:', document.readyState);
        console.log('Document body exists:', !!document.body);
        
        // Check if document.body exists
        if (!document.body) {
            console.error('document.body not found, waiting...');
            setTimeout(initChatbot, 100);
            return;
        }
        
        // Check if widget already exists
        const existingWidget = document.getElementById('chatbot-widget');
        if (existingWidget) {
            console.log('Widget already exists, skipping creation');
            return;
        }
        
        // Create chatbot HTML if it doesn't exist
        console.log('Creating chatbot HTML...');
        createChatbotHTML();
        
        const container = document.getElementById('chatbot-container');
        const toggle = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close');
        const minimizeBtn = document.getElementById('chatbot-minimize');
        const messages = document.getElementById('chatbot-messages');
        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        
        if (!container || !toggle || !closeBtn || !minimizeBtn || !messages || !input || !sendBtn) {
            console.error('Chatbot elements not found after creation');
            console.log('container:', container, 'toggle:', toggle);
            return;
        }
        
        console.log('Chatbot elements found, continuing initialization...');
        
        // Initialize toggle button as visible
        toggle.classList.add('visible');
        
        // Update UI based on state
        function updateUI() {
            if (state === 'open') {
                container.classList.remove('minimized');
                container.classList.add('show');
                toggle.classList.remove('visible');
                toggle.classList.add('hidden');
            } else if (state === 'minimized') {
                container.classList.remove('show');
                container.classList.add('minimized');
                toggle.classList.remove('hidden');
                toggle.classList.add('visible');
                input.blur();
            } else {
                container.classList.remove('show');
                container.classList.add('minimized');
                toggle.classList.remove('hidden');
                toggle.classList.add('visible');
                input.blur();
            }
        }
        
        // Open chatbot
        function openChat() {
            state = 'open';
            updateUI();
            if (window.innerWidth <= 768) {
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
            }
            setTimeout(function() {
                input.focus();
            }, 300);
        }
        
        // Close chatbot
        function closeChat() {
            state = 'closed';
            updateUI();
            if (window.innerWidth <= 768) {
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.width = '';
            }
        }
        
        // Minimize chatbot
        function minimizeChat() {
            state = 'minimized';
            updateUI();
            if (window.innerWidth <= 768) {
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.width = '';
            }
        }
        
        toggle.addEventListener('click', function() {
            if (state === 'closed' || state === 'minimized') {
                openChat();
            } else {
                minimizeChat();
            }
        });
        
        closeBtn.addEventListener('click', closeChat);
        minimizeBtn.addEventListener('click', minimizeChat);
        
        // Add message to chat
        function addMessage(text, isUser) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chatbot-message ' + (isUser ? 'user' : 'bot');
            messageDiv.innerHTML = '<div class="chatbot-message-content">' + text + '</div>';
            messages.appendChild(messageDiv);
            messages.scrollTop = messages.scrollHeight;
        }
        
        // Show typing indicator
        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chatbot-message bot typing';
            typingDiv.id = 'chatbot-typing';
            typingDiv.innerHTML = '<div class="chatbot-message-content"><span class="typing-dots"><span></span><span></span><span></span></span></div>';
            messages.appendChild(typingDiv);
            messages.scrollTop = messages.scrollHeight;
            return typingDiv;
        }
        
        // Hide typing indicator
        function hideTyping(typingElement) {
            if (typingElement && typingElement.parentNode) {
                typingElement.parentNode.removeChild(typingElement);
            }
        }
        
        // Send message
        async function sendMessage() {
            const message = input.value.trim();
            if (!message) return;
            
            addMessage(message, true);
            input.value = '';
            sendBtn.disabled = true;
            
            const typing = showTyping();
            
            try {
                // Try Node.js API first (for Vercel), fallback to PHP
                let currentApiUrl = API_URL;
                
                // Check if we should use Node.js API (Vercel)
                if (window.location.hostname.includes('vercel.app') || window.location.hostname.includes('meineallrounder.de')) {
                    currentApiUrl = '/api/chatbot';
                    console.log('Using Node.js API for Vercel:', currentApiUrl);
                } else {
                    console.log('Using PHP API:', currentApiUrl);
                }
                
                console.log('Sending message to:', currentApiUrl);
                const response = await fetch(currentApiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    // If Node.js API fails, try PHP fallback
                    if (currentApiUrl === '/api/chatbot' && response.status === 404) {
                        console.log('Node.js API not found, trying PHP fallback...');
                        const phpUrl = getApiUrl();
                        const fallbackResponse = await fetch(phpUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: message })
                        });
                        
                        if (fallbackResponse.ok) {
                            const fallbackData = await fallbackResponse.json();
                            hideTyping(typing);
                            setTimeout(function() {
                                addMessage(fallbackData.response || fallbackData.message || 'Entschuldigung, es ist ein Fehler aufgetreten.', false);
                            }, 100);
                            sendBtn.disabled = false;
                            input.focus();
                            return;
                        }
                    }
                    
                    const errorText = await response.text();
                    console.error('API Error:', response.status, errorText);
                    
                    let errorMessage = 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
                    
                    try {
                        const errorData = JSON.parse(errorText);
                        if (errorData.response) {
                            errorMessage = errorData.response;
                        } else if (errorData.error) {
                            console.error('API Error details:', errorData.error);
                            if (errorData.error.includes('API Key') || errorData.error.includes('API-Schlüssel')) {
                                errorMessage = 'Entschuldigung, der Chatbot ist momentan nicht verfügbar. Bitte kontaktieren Sie uns direkt.';
                            }
                        }
                    } catch (e) {
                        // If JSON parsing fails, use default message
                    }
                    
                    hideTyping(typing);
                    setTimeout(function() {
                        addMessage(errorMessage, false);
                    }, 100);
                    sendBtn.disabled = false;
                    input.focus();
                    return;
                }
                
                const data = await response.json();
                
                let responseText = '';
                if (data.response) {
                    responseText = data.response;
                } else if (data.status === 'success' && data.response) {
                    responseText = data.response;
                } else if (data.error) {
                    console.error('API Error:', data.error);
                    responseText = 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
                } else {
                    console.error('Unexpected API response:', data);
                    responseText = 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
                }
                
                hideTyping(typing);
                
                setTimeout(function() {
                    addMessage(responseText, false);
                }, 250);
                
            } catch (error) {
                hideTyping(typing);
                console.error('Chatbot error:', error);
                setTimeout(function() {
                    addMessage('Entschuldigung, ich konnte keine Verbindung herstellen. Bitte versuchen Sie es erneut.', false);
                }, 250);
            } finally {
                sendBtn.disabled = false;
                input.focus();
            }
        }
        
        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !sendBtn.disabled) {
                sendMessage();
            }
        });
    }
    
    // Initialize chatbot - load settings in background
    function initializeChatbot() {
        console.log('Starting chatbot initialization');
        
        // Load settings (non-blocking)
        loadChatbotSettings();
        
        // Initialize chatbot immediately
        setTimeout(function() {
            initChatbot();
        }, 100);
    }
    
    // Start initialization when script loads
    console.log('=== Chatbot Widget Script Loading ===');
    console.log('Current URL:', window.location.href);
    console.log('Current pathname:', window.location.pathname);
    console.log('Document ready state:', document.readyState);
    
    if (document.readyState === 'loading') {
        console.log('Waiting for DOMContentLoaded...');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired, initializing chatbot');
            initializeChatbot();
        });
    } else {
        console.log('DOM already ready, initializing chatbot');
        // Small delay to ensure everything is loaded
        setTimeout(function() {
            initializeChatbot();
        }, 50);
    }
})();
