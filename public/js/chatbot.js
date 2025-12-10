// jQuery-based chatbot script with localStorage persistence
// Requires jQuery to be loaded before this script
; (function ($) {
    $(function () {
        var $chatWindow = $('.chatwindow');
        var $chatToggle = $('.chat-toggle-button');
        var $closeBtn = $chatWindow.find('.close');
        var $sendBtn = $('.inputarea button');
        var $input = $('.inputarea input');
        var $chatArea = $('.chat');

        var STORAGE_KEY = 'eward_chatbot_messages';
        var WELCOME_SHOWN_KEY = 'eward_chatbot_welcome_shown';

        // Load messages from localStorage on page load
        function loadMessages() {
            var messages = localStorage.getItem(STORAGE_KEY);
            var welcomeShown = localStorage.getItem(WELCOME_SHOWN_KEY);

            if (messages) {
                try {
                    var parsedMessages = JSON.parse(messages);
                    $chatArea.empty(); // Clear default content

                    parsedMessages.forEach(function (msg) {
                        var messageHtml = '<div class="' + msg.type + '"><p>' + escapeHtml(msg.text) + '</p></div>';
                        $chatArea.append(messageHtml);
                    });

                    scrollToBottom();
                } catch (e) {
                    console.error('Error loading messages:', e);
                }
            } else if (!welcomeShown) {
                // Show welcome message for first-time visitors
                localStorage.setItem(WELCOME_SHOWN_KEY, 'true');
            }
        }

        // Save messages to localStorage
        function saveMessages() {
            var messages = [];
            $chatArea.find('.model, .user').each(function () {
                var $msg = $(this);
                var type = $msg.hasClass('model') ? 'model' : 'user';
                var text = $msg.find('p').text();
                messages.push({ type: type, text: text });
            });

            localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
        }

        // Scroll to bottom of chat
        function scrollToBottom() {
            $chatArea.animate({ scrollTop: $chatArea.prop('scrollHeight') }, 300);
        }

        function toggleChat() {
            var isHidden = $chatWindow.is(':hidden') || $chatWindow.css('display') === 'none';
            if (isHidden) {
                $chatWindow.css('display', 'flex');
                $chatToggle.hide();
                scrollToBottom();
            } else {
                $chatWindow.hide();
                $chatToggle.show();
            }
        }

        $closeBtn.on('click', function (e) {
            e.preventDefault();
            toggleChat();
        });

        $chatToggle.on('click', function (e) {
            e.preventDefault();
            toggleChat();
        });

        $sendBtn.on('click', sendMessage);
        $input.on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                sendMessage();
            }
        });

        function escapeHtml(text) {
            return $('<div/>').text(text).html();
        }

        function sendMessage() {
            var message = $.trim($input.val());
            if (!message) return;

            // append user message (escaped)
            $chatArea.append('<div class="user"><p>' + escapeHtml(message) + '</p></div>');
            $input.val('');
            scrollToBottom();

            // Save user message
            saveMessages();

            // placeholder for model reply with typing indicator
            $chatArea.append('<div class="model"><div class="typing-indicator"><span></span><span></span><span></span></div></div>');
            var $lastModel = $chatArea.find('.model').last();
            scrollToBottom();

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var baseUrl = $('meta[name="base-url"]').attr('content') || window.location.origin;

            $.ajax({
                url: baseUrl + '/chat/send',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ message: message }),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (data) {
                    try {
                        var reply = data && data.reply ? data.reply : 'Không có phản hồi từ server.';

                        // Replace typing indicator with actual reply
                        $lastModel.html('<p>' + escapeHtml(reply) + '</p>');
                        scrollToBottom();

                        // Save bot message
                        saveMessages();
                    } catch (err) {
                        console.error(err);
                        $lastModel.html('<p>⚠️ Lỗi xử lý phản hồi.</p>');
                        saveMessages();
                    }
                },
                error: function (xhr) {
                    console.error('Chat request failed:', xhr);
                    $lastModel.html('<p>⚠️ Lỗi kết nối tới máy chủ.</p>');
                    saveMessages();
                }
            });
        }

        // Load messages when page loads
        loadMessages();

        // Optional: Add clear chat functionality (can be triggered via console or button)
        window.clearChatHistory = function () {
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(WELCOME_SHOWN_KEY);
            $chatArea.empty();
            $chatArea.append('<div class="model"><p>Xin chào bạn 👋, mình có thể giúp gì cho bạn hôm nay?</p></div>');
            console.log('Chat history cleared!');
        };
    });
})(window.jQuery || window.$);
