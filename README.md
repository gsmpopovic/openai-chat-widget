/*
Plugin Name: OpenAI Chat Widget
Description: A mobile-responsive chat widget powered by OpenAI API.
Version: 1.0
Author: George Popovic
*/


Functionality Overview:

The OpenAI Chat Widget plugin adds a mobile-responsive chat widget to a WordPress website. Users can interact with the chat widget by typing messages. The plugin sends user messages to the OpenAI API and retrieves AI-generated responses. The AI responses are displayed in the chat widget in real-time. How it Works:

The plugin enqueues necessary scripts and stylesheets to render the chat widget. The chat widget's HTML is added to the footer of the website. When a user sends a message, an AJAX request is sent to the server. The server-side code retrieves the OpenAI API key from the plugin settings. The user message is sent to the OpenAI API using the API key for AI processing. The API returns an AI-generated response. The response is sent back to the chat widget and displayed to the user.