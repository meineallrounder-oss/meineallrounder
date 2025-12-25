// Chatbot Widget JavaScript - Standalone Version
(function() {
    'use strict';
    
    // API URL - Dinamički određuje putanju na osnovu trenutne lokacije
    // Ako je widget u "sistem bua ai bot" folderu, koristi sistem bua ai bot/api.php
    function getApiUrl() {
        const path = window.location.pathname;
        
        // Ako smo u "sistem bua ai bot" folderu
        if (path.includes('sistem bua ai bot')) {
            // Ako smo u podfolderu (npr. sistem bua ai bot/subfolder/)
            if (path.match(/sistem bua ai bot\/[^\/]+\//)) {
                return '../api.php';
            }
            // Ako smo direktno u folderu
            return 'api.php';
        }
        
        // Ako je "sistem bua ai bot" folder u root-u, ali widget je na drugom mestu
        // Pokušaj da nađeš sistem bua ai bot/api.php relativno
        const depth = (path.match(/\//g) || []).length - 1;
        if (depth === 0) {
            return 'sistem bua ai bot/api.php';
        } else if (depth === 1) {
            return '../sistem bua ai bot/api.php';
        } else {
            // Za dublje strukture, koristi absolutnu putanju
            return '/sistem bua ai bot/api.php';
        }
    }
    
    const API_URL = getApiUrl();
    
    // States: 'closed', 'open', 'minimized'
    let state = 'closed';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chatbot-container');
        const toggle = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close');
        const minimizeBtn = document.getElementById('chatbot-minimize');
        const messages = document.getElementById('chatbot-messages');
        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        
        if (!container || !toggle || !closeBtn || !minimizeBtn || !messages || !input || !sendBtn) {
            console.error('Chatbot elements not found');
            return;
        }
        
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
            input.blur();
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
            input.blur();
        }
        
        // Toggle chatbot (open/close)
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (state === 'closed' || state === 'minimized') {
                openChat();
            } else {
                closeChat();
            }
        });
        
        // Close button
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeChat();
        });
        
        // Minimize button
        minimizeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            minimizeChat();
        });
        
        // Close chatbot when clicking outside on mobile
        if (window.innerWidth <= 768) {
            document.addEventListener('click', function(e) {
                if (state === 'open' && 
                    !container.contains(e.target) && 
                    !toggle.contains(e.target)) {
                    closeChat();
                }
            });
        }
        
        // Prevent clicks inside container from closing
        container.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Add message with animation
        function addMessage(content, isUser) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${isUser ? 'user' : 'bot'}`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'chatbot-message-content';
            contentDiv.textContent = content;
            
            messageDiv.appendChild(contentDiv);
            messages.appendChild(messageDiv);
            
            setTimeout(function() {
                messages.scrollTo({
                    top: messages.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
        }
        
        // Show typing indicator with animation
        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typing-indicator';
            typingDiv.className = 'chatbot-message bot';
            typingDiv.innerHTML = '<div class="chatbot-message-content" style="padding: 12px 16px;"><span></span><span></span><span></span></div>';
            messages.appendChild(typingDiv);
            
            setTimeout(function() {
                messages.scrollTo({
                    top: messages.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
            
            return typingDiv;
        }
        
        // Hide typing indicator with smooth fade out
        function hideTyping(typingElement) {
            if (typingElement) {
                typingElement.style.opacity = '0';
                typingElement.style.transition = 'opacity 0.2s ease';
                setTimeout(function() {
                    if (typingElement && typingElement.parentNode) {
                        typingElement.remove();
                    }
                }, 200);
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
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error:', response.status, errorText);
                    hideTyping(typing);
                    setTimeout(function() {
                        addMessage('Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.', false);
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
    });
})();

