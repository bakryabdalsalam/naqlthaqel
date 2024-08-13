<?php
/**
 * Plugin Name: Dynamic Service Plugin
 * Plugin URI: https://bakry2.vercel.app/
 * Description: A plugin to create services with dynamic pricing and integration with WooCommerce.
 * Author URI: https://bakry2.vercel.app/
 * Version: 1.0
 * Author: Bakry Abdelsalam
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define paths
define('DYNAMIC_SERVICE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DYNAMIC_SERVICE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once DYNAMIC_SERVICE_PLUGIN_PATH . 'includes/custom-post-types.php';
require_once DYNAMIC_SERVICE_PLUGIN_PATH . 'includes/acf-fields.php';
require_once DYNAMIC_SERVICE_PLUGIN_PATH . 'includes/shortcodes.php';

// Enqueue scripts and styles
function dsp_enqueue_scripts() {
    wp_enqueue_script('dsp-dynamic-pricing', DYNAMIC_SERVICE_PLUGIN_URL . 'assets/js/dynamic-pricing.js', array('jquery'), '1.0', true);
    wp_localize_script('dsp-dynamic-pricing', 'dsp_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'cart_url' => wc_get_cart_url(),
    ));
}
add_action('wp_enqueue_scripts', 'dsp_enqueue_scripts');





