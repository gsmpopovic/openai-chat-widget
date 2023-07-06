<?php
/*
Plugin Name: OpenAI Chat Widget
Description: A mobile-responsive chat widget powered by OpenAI API.
Version: 1.0
Author: George Popovic
*/

// Enqueue necessary scripts and stylesheets
function openai_chat_widget_enqueue_scripts()
{
    wp_enqueue_style('openai-chat-widget', plugins_url('css/chat-widget.css', __FILE__));
    wp_enqueue_script('openai-chat-widget', plugins_url('js/chat-widget.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'openai_chat_widget_enqueue_scripts');

// Add chat widget HTML to the footer
function openai_chat_widget_add_html()
{
?>
    <div id="openai-chat-widget">
        <div id="openai-chat-container">
            <div id="openai-chat-header">
                <span id="openai-chat-title">Chat Widget</span>
                <span id="openai-chat-close">&times;</span>
            </div>
            <div id="openai-chat-body">
                <div id="openai-chat-messages"></div>
                <div id="openai-chat-input">
                    <input type="text" id="openai-chat-text" placeholder="Type your message...">
                    <button id="openai-chat-send">Send</button>
                </div>
            </div>
        </div>
    </div>
<?php
}
add_action('wp_footer', 'openai_chat_widget_add_html');

// Configure OpenAI API credentials
$openai_api_key = get_option('openai_api_key');

// Handle AJAX request to send user message and get AI response
function openai_chat_widget_send_message()
{
    global $openai_api_key;

    $message = sanitize_text_field($_POST['message']);

    $headers = array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $openai_api_key,
    );

    $data = array(
        'messages' => array(
            array('role' => 'user', 'content' => $message),
        ),
    );

    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => $headers,
        'body'    => json_encode($data),
    ));

    if (is_wp_error($response)) {
        wp_send_json_error('Error sending message.');
    }

    $response_body = wp_remote_retrieve_body($response);
    $response_data = json_decode($response_body, true);

    if (isset($response_data['choices'][0]['text'])) {
        $reply = $response_data['choices'][0]['text'];
        wp_send_json_success($reply);
    } else {
        wp_send_json_error('Invalid response from AI.');
    }
}
add_action('wp_ajax_openai_chat_widget_send_message', 'openai_chat_widget_send_message');
add_action('wp_ajax_nopriv_openai_chat_widget_send_message', 'openai_chat_widget_send_message');

// Register plugin settings page
function openai_chat_widget_register_settings_page() {
    add_options_page(
        'OpenAI Chat Widget Settings',
        'OpenAI Chat Widget',
        'manage_options',
        'openai-chat-widget-settings',
        'openai_chat_widget_render_settings_page'
    );
}
add_action('admin_menu', 'openai_chat_widget_register_settings_page');

// Render plugin settings page
function openai_chat_widget_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>OpenAI Chat Widget Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('openai-chat-widget-settings');
            do_settings_sections('openai-chat-widget-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and initialize plugin settings
function openai_chat_widget_register_settings() {
    register_setting('openai-chat-widget-settings', 'openai_api_key');
    add_settings_section(
        'openai-chat-widget-api-section',
        'API Settings',
        'openai_chat_widget_api_section_callback',
        'openai-chat-widget-settings'
    );
    add_settings_field(
        'openai-chat-widget-api-key',
        'API Key',
        'openai_chat_widget_api_key_callback',
        'openai-chat-widget-settings',
        'openai-chat-widget-api-section'
    );
}

add_action('admin_init', 'openai_chat_widget_register_settings');

// Callback function for API section
function openai_chat_widget_api_section_callback() {
    echo '<p>Enter your OpenAI API key:</p>';
}

// Callback function for API key field
function openai_chat_widget_api_key_callback() {
    $api_key = get_option('openai_api_key');
    echo '<input type="text" name="openai_api_key" value="' . esc_attr($api_key) . '" />';
}
