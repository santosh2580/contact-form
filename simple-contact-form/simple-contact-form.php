<?php
/*
Plugin Name: Simple Contact Form
Description: Simple contact form
Version: 1.0
Text Domain: simple-contact-form
Author: Santosh Hacker
Plugin URI: https://www.example.com/simple-contact-form
*/

class SimpleContactForm
{
    public function __construct()
    {
        add_action('init', array($this, 'create_custom_post_type'));
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));

        // Add shortcode for contact form
        add_shortcode('contact_form', array($this, 'contact_form_shortcode'));

        // Handle AJAX requests
        add_action('wp_ajax_submit_contact_form', array($this, 'handle_contact_form'));
        add_action('wp_ajax_nopriv_submit_contact_form', array($this, 'handle_contact_form'));
    }

    // Create custom post type for contact form entries
    public function create_custom_post_type()
    {
        $args = array(
            'public' => true,
            'has_archive' => true,
            'supports' => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'labels' => array(
                'name' => 'Contact Form',
                'singular_name' => 'Contact Form Entry'
            ),
            'menu_icon' => 'dashicons-media-text',
        );

        register_post_type('simple_contact_form', $args);
    }

    // Enqueue CSS and JS files including Tailwind CSS
    public function load_assets()
    {
        // Enqueue Tailwind CSS
        wp_enqueue_style(
            'tailwind-css',
            'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            array(),
            '2.2.19'
        );

        // Enqueue your plugin's custom CSS
        wp_enqueue_style(
            'simple-contact-form',
            plugin_dir_url(__FILE__) . 'css/simple-contact-form.css',
            array(),
            1,
            'all'
        );

        // Enqueue the plugin's JS file for AJAX
        wp_enqueue_script(
            'simple-contact-form-js',
            plugin_dir_url(__FILE__) . 'js/simple-contact-form.js',
            array('jquery'),
            1,
            true
        );

        // Pass admin-ajax URL to JS
        wp_localize_script('simple-contact-form-js', 'simpleContactForm', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contact_form_nonce')
        ));
    }

    // Contact form shortcode
    public function contact_form_shortcode()
    {
        ob_start(); ?>
        <div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">Send Us a Message</h1>
            <h2 class="text-lg text-center text-gray-600 mb-8">Please fill out the form below</h2>
            
            <form method="post" action="" class="space-y-6" id="simple-contact-form">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="John Doe" 
                        required>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="john@example.com" 
                        required>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Message:</label>
                    <textarea 
                        name="message" 
                        id="message"
                        rows="4" 
                        class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Write your message here..." 
                        required></textarea>
                </div>
                
                <div class="flex justify-center">
                    <button 
                        type="submit" 
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
                        Submit
                    </button>
                </div>
                <p id="form-response" class="mt-4 text-center"></p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    // Handle AJAX form submission
    public function handle_contact_form()
    {
        check_ajax_referer('contact_form_nonce', 'nonce');

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error('All fields are required.');
        }

        // Insert data as a post
        wp_insert_post(array(
            'post_type' => 'simple_contact_form',
            'post_title' => 'Message from ' . $name,
            'post_content' => $message,
            'post_status' => 'publish'
        ));

        wp_send_json_success('Thank you for your message!');
    }

    
}

// Initialize the class
new SimpleContactForm();
