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
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_option_label',
                                'label' => 'Option Label',
                                'name' => 'option_label',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_option_price',
                                'label' => 'Option Price',
                                'name' => 'option_price',
                                'type' => 'number',
                                'instructions' => 'Enter the price for this option.',
                            ),
                        ),
                        'min' => 0,
                        'layout' => 'table',
                        'button_label' => 'Add Option',
                        'instructions' => 'Define the options and their respective prices (for dropdowns and checkboxes).',
                    ),
                ),
            ),
            // Additional Fields (Not Required)
            array(
                'key' => 'field_notes',
                'label' => 'الملاحظات',
                'name' => 'notes',
                'type' => 'textarea',
                'required' => 0,
            ),
            array(
                'key' => 'field_image_upload',
                'label' => 'Upload Image',
                'name' => 'image_upload',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'instructions' => 'Upload an image.',
                'required' => 0,
            ),
            array(
                'key' => 'field_pickup_location',
                'label' => 'الإستلام من',
                'name' => 'pickup_location',
                'type' => 'text',
                'required' => 0,
            ),
            array(
                'key' => 'field_delivery_location',
                'label' => 'التوصيل',
                'name' => 'delivery_location',
                'type' => 'text',
                'required' => 0,
            ),
            array(
                'key' => 'field_delivery_date',
                'label' => 'التاريخ',
                'name' => 'delivery_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'd/m/Y',
                'return_format' => 'd/m/Y',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_delivery_time',
                'label' => 'الوقت',
                'name' => 'delivery_time',
                'type' => 'time_picker',
                'required' => 0,
                'display_format' => 'H:i',
                'return_format' => 'H:i',
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
