<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catering Assistant - Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; }
        .catering-font { font-family: 'Playfair Display', serif; }
        .chat-container {
            height: calc(100vh - 200px);
        }
        .message-bubble {
            max-width: 80%;
            word-wrap: break-word;
        }
        .typing-indicator {
            display: none;
        }
        .typing-indicator.show {
            display: flex;
        }
        .typing-dot {
            animation: typing 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        .scroll-smooth {
            scroll-behavior: smooth;
        }
        .hover-scale {
            transition: all 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-white text-black">
    <!-- Header -->
    <?php include 'layouts/header.php'; ?>

    <!-- Yellow Header -->
    <section class="bg-yellow-400 text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font">Catering Assistant</h2>
            <p class="text-lg opacity-90">Get instant help with your catering questions and reservations</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Chat Interface -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover">
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-yellow-600 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-black font-semibold text-lg">Catering Assistant</h2>
                        <p class="text-black opacity-80 text-sm">Online • Ready to help</p>
                    </div>
                    <div class="ml-auto">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chat-messages" class="chat-container overflow-y-auto p-6 space-y-4 bg-gray-50">
                <!-- Welcome Message -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                    <div class="message-bubble bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <p class="text-gray-800 mb-2">👋 Hello! I'm your Catering Assistant. How can I help you today?</p>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600">I can help you with:</p>
                            <ul class="text-sm text-gray-600 space-y-1 ml-4">
                                <li>• <span class="font-medium">Menu planning</span> and customization</li>
                                <li>• <span class="font-medium">Pricing</span> and package information</li>
                                <li>• <span class="font-medium">Reservation</span> and booking assistance</li>
                                <li>• <span class="font-medium">Event coordination</span> and logistics</li>
                                <li>• <span class="font-medium">Dietary requirements</span> and special requests</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div id="typing-indicator" class="typing-indicator flex items-start space-x-3">
                    <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 flex items-center space-x-3">
                        <div class="flex space-x-1">
                            <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                            <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                            <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                        </div>
                        <span class="ml-3 text-gray-500 text-sm font-medium">AI is typing...</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-wrap gap-2 mb-4">
                    <button onclick="sendQuickMessage('What catering packages do you offer?')" 
                            class="quick-action-btn bg-white hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 px-3 py-2 rounded-full text-sm border border-gray-300 hover:border-yellow-300 transition duration-200 hover-scale">
                        <i class="fas fa-utensils mr-2"></i>Packages
                    </button>
                    <button onclick="sendQuickMessage('How much does catering cost?')" 
                            class="quick-action-btn bg-white hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 px-3 py-2 rounded-full text-sm border border-gray-300 hover:border-yellow-300 transition duration-200 hover-scale">
                        <i class="fas fa-dollar-sign mr-2"></i>Pricing
                    </button>
                    <button onclick="sendQuickMessage('How do I make a reservation?')" 
                            class="quick-action-btn bg-white hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 px-3 py-2 rounded-full text-sm border border-gray-300 hover:border-yellow-300 transition duration-200 hover-scale">
                        <i class="fas fa-calendar-plus mr-2"></i>Book Now
                    </button>
                    <button onclick="sendQuickMessage('Do you accommodate dietary restrictions?')" 
                            class="quick-action-btn bg-white hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 px-3 py-2 rounded-full text-sm border border-gray-300 hover:border-yellow-300 transition duration-200 hover-scale">
                        <i class="fas fa-heart mr-2"></i>Dietary Needs
                    </button>
                </div>

                <!-- Message Input -->
                <div class="flex space-x-3">
                    <div class="flex-1 relative">
                        <input type="text" 
                               id="message-input" 
                               placeholder="Type your message here..." 
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-none"
                               onkeypress="handleKeyPress(event)">
                        <button onclick="sendMessage()" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-md transition duration-200">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 card-hover">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">Response Time</h3>
                </div>
                <p class="text-gray-600 text-sm">Get instant responses to your catering questions 24/7</p>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 card-hover">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-shield-alt text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">Secure & Private</h3>
                </div>
                <p class="text-gray-600 text-sm">Your conversations are secure and your data is protected</p>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 card-hover">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-lightbulb text-yellow-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">Smart Suggestions</h3>
                </div>
                <p class="text-gray-600 text-sm">Get personalized recommendations for your events</p>
            </div>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const typingIndicator = document.getElementById('typing-indicator');

        // Remove static responses and use API
        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;

            // Add user message
            addMessage(message, 'user');
            messageInput.value = '';

            // Show typing indicator (3 dots)
            showTypingIndicator();

            // Call backend API
            try {
                const response = await fetch('/public/chatbot_api.php?type=ask', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'message=' + encodeURIComponent(message)
                });
                const data = await response.json();
                hideTypingIndicator();
                if (data.reply) {
                    addMessage(data.reply, 'bot');
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    addMessage('Sorry, I could not get a response. Please try again later.', 'bot');
                }
            } catch (e) {
                hideTypingIndicator();
                addMessage('Sorry, there was an error connecting to the server.', 'bot');
            }
        }

        function sendQuickMessage(message) {
            messageInput.value = message;
            sendMessage();
        }

        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex items-start space-x-3';
            
            const icon = sender === 'user' ? 'fas fa-user' : 'fas fa-robot';
            const bgColor = sender === 'user' ? 'bg-yellow-500' : 'bg-gray-400';
            const messageBg = sender === 'user' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-800';
            const justifyClass = sender === 'user' ? 'justify-end' : 'justify-start';

            messageDiv.innerHTML = `
                <div class="flex items-start space-x-3 ${justifyClass} w-full">
                    ${sender === 'bot' ? `<div class="w-8 h-8 ${bgColor} rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="${icon} text-white text-sm"></i>
                    </div>` : ''}
                    <div class="message-bubble ${messageBg} rounded-lg p-4 shadow-sm border border-gray-200">
                        <p class="whitespace-pre-line">${text}</p>
                    </div>
                    ${sender === 'user' ? `<div class="w-8 h-8 ${bgColor} rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="${icon} text-white text-sm"></i>
                    </div>` : ''}
                </div>
            `;

            chatMessages.appendChild(messageDiv);
            scrollToBottom();
        }

        function showTypingIndicator() {
            typingIndicator.classList.add('show');
            scrollToBottom();
        }

        function hideTypingIndicator() {
            typingIndicator.classList.remove('show');
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        // Auto-scroll to bottom on page load
        window.addEventListener('load', scrollToBottom);
    </script>
</body>
</html>
