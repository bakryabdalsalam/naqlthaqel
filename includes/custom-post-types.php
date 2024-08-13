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
    );

    register_post_type('dsp_service', $args);

}
add_action('init', 'dsp_register_services_post_type', 0);

