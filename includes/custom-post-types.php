<?php

// Register Custom Post Type
function dsp_register_services_post_type() {

    $labels = array(
        'name'                  => _x('Services', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Service', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Services', 'text_domain'),
        'name_admin_bar'        => __('Service', 'text_domain'),
    );

    $args = array(
        'label'                 => __('Service', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'public'                => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-admin-tools',
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable REST API support
        'rest_base'             => 'services', // Optional: Customize the REST API base path
        'rest_controller_class' => 'WP_REST_Posts_Controller', // Use default controller
    );

    register_post_type('dsp_service', $args);
}
add_action('init', 'dsp_register_services_post_type', 0);


// Add a new column for the shortcode
function dsp_add_shortcode_column($columns) {
    $columns['dsp_shortcode'] = __('Shortcode', 'text_domain');
    return $columns;
}
add_filter('manage_dsp_service_posts_columns', 'dsp_add_shortcode_column');

// Populate the shortcode column
function dsp_display_shortcode_column($column, $post_id) {
    if ($column == 'dsp_shortcode') {
        echo '[dsp_service_form service_id="' . $post_id . '"]';
    }
}
add_action('manage_dsp_service_posts_custom_column', 'dsp_display_shortcode_column', 10, 2);



/*
 * API
 * API to get the product
 */

// Register custom REST routes during the rest_api_init action
add_action( 'rest_api_init', 'dsp_register_custom_rest_routes' );

function dsp_register_custom_rest_routes() {
    // Register the '/services' route
    register_rest_route( 'dsp/v1', '/services', array(
        'methods'             => 'GET',
        'callback'            => 'dsp_get_services_with_custom_data',
        'permission_callback' => '__return_true', // Adjust permissions as needed
    ) );

    // Register the '/add-to-cart' route
    register_rest_route( 'dsp/v1', '/add-to-cart', array(
        'methods'             => 'POST',
        'callback'            => 'dsp_rest_add_to_cart',
        'permission_callback' => 'dsp_rest_permission_check', // Define permission callback
    ) );
}

// The callback function for '/services'
function dsp_get_services_with_custom_data( WP_REST_Request $request ) {
    $args = array(
        'post_type'      => 'dsp_service',
        'posts_per_page' => -1,
    );

    $services = get_posts( $args );
    $data = array();

    foreach ( $services as $service ) {
        // Get custom fields using ACF functions
        $base_price     = get_field( 'base_price', $service->ID );
        $wc_product_id  = get_field( 'wc_product_id', $service->ID );
        $form_fields    = get_field( 'form_fields', $service->ID );

        $data[] = array(
            'id'            => $service->ID,
            'title'         => $service->post_title,
            'content'       => $service->post_content,
            'base_price'    => $base_price,
            'wc_product_id' => $wc_product_id,
            'form_fields'   => $form_fields,
        );
    }

    return rest_ensure_response( $data );
}

// Define the permission callback function for '/add-to-cart'
function dsp_rest_permission_check( WP_REST_Request $request ) {
    // For example, allow only authenticated users
    return is_user_logged_in();

    // If you want to allow unauthenticated access (not recommended for modifying data), use:
    // return true;
}

// The callback function for '/add-to-cart'
function dsp_rest_add_to_cart( WP_REST_Request $request ) {
    // Get parameters from the request
    $product_id = $request->get_param( 'product_id' );
    $price      = $request->get_param( 'price' );
    $fields     = $request->get_param( 'fields' );

    // Validate and sanitize inputs
    $product_id = intval( $product_id );
    $price      = floatval( $price );

    // Implement the same logic as in your AJAX handler
    // Add the product to the cart with custom data
    $cart_item_data = array(
        'custom_price' => $price,
        'fields'       => $fields,
    );

    // Generate a unique key
    $unique_cart_item_key           = md5( microtime() . rand() );
    $cart_item_data['unique_key']   = $unique_cart_item_key;

    $cart_item_key = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );

    if ( $cart_item_key ) {
        return rest_ensure_response( array( 'success' => true ) );
    } else {
        // Capture WooCommerce error messages
        $errors         = wc_get_notices( 'error' );
        $error_messages = array();

        if ( ! empty( $errors ) ) {
            foreach ( $errors as $error ) {
                $error_messages[] = $error;
            }
            wc_clear_notices();
        } else {
            $error_messages[] = 'Failed to add product to cart.';
        }

        return new WP_Error( 'add_to_cart_failed', implode( ' ', $error_messages ), array( 'status' => 400 ) );
    }
}