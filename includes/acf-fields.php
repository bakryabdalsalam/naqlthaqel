<?php

// Check if ACF is active
if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
        'key' => 'group_1',
        'title' => 'Service Fields',
        'fields' => array(
            array(
                'key' => 'field_base_price',
                'label' => 'Base Price',
                'name' => 'base_price',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_wc_product_id',
                'label' => 'WooCommerce Product ID',
                'name' => 'wc_product_id',
                'type' => 'number',
                'required' => 1,
                'instructions' => 'Enter the WooCommerce product ID that this service is associated with.',
            ),
            array(
                'key' => 'field_form_fields',
                'label' => 'Form Fields',
                'name' => 'form_fields',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_label',
                        'label' => 'Label',
                        'name' => 'label',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_type',
                        'label' => 'Type',
                        'name' => 'type',
                        'type' => 'select',
                        'choices' => array(
                            'text' => 'Text',
                            'dropdown' => 'Dropdown',
                            'checkbox' => 'Checkbox',
                        ),
                    ),
                    array(
                        'key' => 'field_options',
                        'label' => 'Options',
                        'name' => 'options',
                        'type' => 'text',
                        'instructions' => 'Comma separated values (for dropdowns and checkboxes)',
                    ),
                    array(
                        'key' => 'field_price_increment',
                        'label' => 'Price Increment',
                        'name' => 'price_increment',
                        'type' => 'number',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'dsp_service',
                ),
            ),
        ),
    ));
}

