jQuery(document).ready(function($) {
    var chatWidget = $('#openai-chat-widget');
    var chatContainer = $('#openai-chat-container');
    var chatMessages = $('#openai-chat-messages');
    var chatText = $('#openai-chat-text');
    var chatSend = $('#openai-chat-send');

    // Toggle chat widget visibility
    $('#openai-chat-title, #openai-chat-close').click(function() {
        chatContainer.slideToggle();
    });

    // Send user message and get AI response
    chatSend.click(function() {
        var message = chatText.val().trim();

        if (message !== '') {
            sendMessage(message);
            chatText.val('');
        }
    });

    // Handle Enter key press in the input field
    chatText.keyup(function(event) {
        if (event.keyCode === 13) {
            chatSend.click();
        }
    });

    function sendMessage(message) {
        chatMessages.append('<div class="chat-message user">' + message + '</div>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'openai_chat_widget_send_message',
                message: message
            },
            success: function(response) {
                if (response.success) {
                    var reply = response.data;
                    chatMessages.append('<div class="chat-message ai">' + reply + '</div>');
                    chatMessages.scrollTop(chatMessages[0].scrollHeight);
                } else {
                    console.log('Error:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('AJAX Error:', errorThrown);
            }
        });
    }
});
