<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .chat-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .chat-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .chat-header .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        
        .chat-header .close-btn:hover {
            background-color: rgba(255,255,255,0.2);
        }
        
        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f8f9fa;
        }
        
        .message {
            margin-bottom: 15px;
            max-width: 80%;
            clear: both;
        }
        
        .user-message {
            float: right;
            background-color: #4e54c8;
            color: white;
            padding: 10px 15px;
            border-radius: 18px 18px 0 18px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .bot-message {
            float: left;
            background-color: white;
            color: #333;
            padding: 10px 15px;
            border-radius: 18px 18px 18px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .chat-input {
            padding: 15px;
            background-color: white;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .chat-input input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .chat-input input:focus {
            border-color: #4e54c8;
        }
        
        .chat-input button {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        
        .chat-input button:hover {
            transform: scale(1.05);
        }
        
        .chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1001;
            transition: transform 0.3s;
        }
        
        .chat-toggle:hover {
            transform: scale(1.1);
        }
        
        .chat-toggle i {
            font-size: 24px;
        }
        
        .quick-questions {
            padding: 10px 15px;
            background-color: #f0f2ff;
            border-top: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .quick-questions button {
            background-color: white;
            color: #4e54c8;
            border: 1px solid #4e54c8;
            padding: 8px 12px;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .quick-questions button:hover {
            background-color: #4e54c8;
            color: white;
        }
        
        .bot-message pre {
            white-space: pre-wrap;
            font-family: inherit;
            margin: 0;
        }
        
        .bot-message .emoji {
            font-size: 1.2em;
            margin-right: 5px;
        }
        
        .bot-message .section-header {
            font-weight: bold;
            margin-top: 10px;
            color: #4e54c8;
        }
        
        .bot-message .review {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 5px;
        }
        
        .bot-message .rating {
            color: #ffc107;
        }
        
        .bot-message .separator {
            border-top: 1px dashed #ddd;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="chat-toggle" onclick="toggleChat()">
        <i class="fa fa-comments"></i>
    </div>
    
    <div class="chat-container" id="chatContainer" style="display: none;">
        <div class="chat-header">
            <h3><i class="fa fa-robot"></i> Travel Assistant</h3>
            <button class="close-btn" onclick="toggleChat()">&times;</button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                <pre>👋 Hello! I'm your travel assistant. How can I help you today?</pre>
            </div>
        </div>
        <div class="quick-questions">
            <button onclick="quickAsk('Show me available tour packages.')">Tour Packages</button>
            <button onclick="quickAsk('What is my booking status? My Booking ID is 0.')">Check Booking Status</button>
            <button onclick="quickAsk('What is my inquiry? My Inquiry ID is 1.')">Check Inquiry</button>
            <button onclick="quickAsk('What issue did I report? My Issue ID is 1.')">Check Issue</button>
        </div>
        <div class="chat-input">
            <input type="text" id="userInput" placeholder="Type your message here..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        function toggleChat() {
            const chatContainer = document.getElementById('chatContainer');
            if (chatContainer.style.display === 'none') {
                chatContainer.style.display = 'flex';
            } else {
                chatContainer.style.display = 'none';
            }
        }
        
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }
        
        function sendMessage() {
            const userInput = document.getElementById('userInput');
            const message = userInput.value.trim();
            
            if (message) {
                addMessage(message, 'user');
                userInput.value = '';
                
                // Show typing indicator
                const typingIndicator = document.createElement('div');
                typingIndicator.className = 'message bot-message';
                typingIndicator.innerHTML = '<pre>Typing...</pre>';
                document.getElementById('chatMessages').appendChild(typingIndicator);
                
                // Scroll to bottom
                const chatMessages = document.getElementById('chatMessages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Send message to server
                fetch('chatbot.php?message=' + encodeURIComponent(message))
                    .then(response => response.json())
                    .then(data => {
                        // Remove typing indicator
                        chatMessages.removeChild(typingIndicator);
                        
                        // Add bot response
                        addMessage(data.response, 'bot');
                        
                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Remove typing indicator
                        chatMessages.removeChild(typingIndicator);
                        
                        // Add error message
                        addMessage('Sorry, there was an error processing your request.', 'bot');
                    });
            }
        }
        
        function addMessage(message, sender) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message ' + sender + '-message';
            
            // Format the message with proper line breaks and styling
            const formattedMessage = formatMessage(message);
            messageDiv.innerHTML = formattedMessage;
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function formatMessage(message) {
            // Replace line breaks with <br> tags
            let formatted = message.replace(/\n/g, '<br>');
            
            // Add styling for emojis
            formatted = formatted.replace(/([📦📍🗺️💰✨📝📊👤🍽️📅📋⏳✅❌❓💡])/g, '<span class="emoji">$1</span>');
            
            // Add styling for section headers
            formatted = formatted.replace(/([A-Z\s]+):/g, '<span class="section-header">$1:</span>');
            
            // Add styling for ratings
            formatted = formatted.replace(/(★+☆*)/g, '<span class="rating">$1</span>');
            
            // Add styling for separators
            formatted = formatted.replace(/(━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━)/g, '<div class="separator">$1</div>');
            
            return '<pre>' + formatted + '</pre>';
        }
        
        function quickAsk(question) {
            document.getElementById('userInput').value = question;
            sendMessage();
        }
    </script>
</body>
</html>
