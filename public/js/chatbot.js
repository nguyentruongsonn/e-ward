// jQuery-based chatbot script
// Requires jQuery to be loaded before this script
;(function ($) {
    $(function () {
        var $chatWindow = $('.chatwindow');
        var $chatToggle = $('.chat-toggle-button');
        var $closeBtn = $chatWindow.find('.close');
        var $sendBtn = $('.inputarea button');
        var $input = $('.inputarea input');
        var $chatArea = $('.chat');

        function toggleChat() {
            var isHidden = $chatWindow.is(':hidden') || $chatWindow.css('display') === 'none';
            if (isHidden) {
                $chatWindow.css('display', 'flex');
                $chatToggle.hide();
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

            // placeholder for model reply
            $chatArea.append('<div class="model"><p>Đang trả lời...</p></div>');
            var $lastModel = $chatArea.find('.model').last();

            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/chat/send',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ message: message }),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (data) {
                    try {
                        var reply = data && data.reply ? data.reply : 'Không có phản hồi từ server.';
                        $lastModel.find('p').text(reply);
                    } catch (err) {
                        console.error(err);
                        $lastModel.find('p').text('⚠️ Lỗi xử lý phản hồi.');
                    }
                },
                error: function (xhr) {
                    console.error('Chat request failed:', xhr);
                    $lastModel.find('p').text('⚠️ Lỗi kết nối tới máy chủ.');
                }
            });
        }
    });
})(window.jQuery || window.$);
