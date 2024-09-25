<?php

// Shortcode to display a specific service form based on the service ID
function dsp_service_form_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'service_id' => '', // Default service ID to empty
        ),
        $atts
    );

    // If no service ID is provided, return a message
    if (empty($atts['service_id'])) {
        return '<p>Please provide a service ID.</p>';
    }

    // Get the service post using the service ID
    $post = get_post($atts['service_id']);

    if (!$post || $post->post_type !== 'dsp_service') {
        return '<p>Service not found or invalid service ID.</p>';
    }

    // Retrieve ACF fields for the service (e.g., base price)
    $base_price = get_field('base_price', $post->ID);
    $wc_product_id = get_field('wc_product_id', $post->ID);
    $form_fields = get_field('form_fields', $post->ID);

    ob_start();
    ?>
    <div class="dsp-service-form">
        <h3><?php echo esc_html($post->post_title); ?></h3>
        <form id="dsp-service-form-<?php echo esc_attr($post->ID); ?>" data-product-id="<?php echo esc_attr($wc_product_id); ?>" enctype="multipart/form-data">
            <?php if ($form_fields) : ?>
                <?php foreach ($form_fields as $field) : ?>
                    <?php if (!empty($field['label'])) : ?>
                        <div class="form-field">
                            <label><?php echo esc_html($field['label']); ?></label>
                            <?php if ($field['type'] === 'text') : ?>
                                <input type="text" name="fields[<?php echo esc_attr($field['label']); ?>]" data-price="<?php echo esc_attr($field['price_increment']); ?>">
                            <?php elseif ($field['type'] === 'dropdown') : ?>
                                <select name="fields[<?php echo esc_attr($field['label']); ?>]">
                                    <?php if (!empty($field['options'])) : ?>
                                        <?php foreach ($field['options'] as $option) : ?>
                                            <option value="<?php echo esc_attr($option['option_label']); ?>" data-price="<?php echo esc_attr($option['option_price']); ?>">
                                                <?php echo esc_html($option['option_label']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="" disabled>No options available</option>
                                    <?php endif; ?>
                                </select>
                            <?php elseif ($field['type'] === 'checkbox') : ?>
                                <input type="checkbox" name="fields[<?php echo esc_attr($field['label']); ?>]" data-price="<?php echo esc_attr($field['price_increment']); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- User-provided fields -->
            <div class="form-field">
                <label for="notes">الملاحظات</label>
                <textarea id="notes" name="fields[الملاحظات]"></textarea>
            </div>

            <div class="form-field">
                <label for="image_upload">Upload Image</label>
                <input type="file" id="image_upload" name="fields[image_upload]" accept="image/*">
            </div>

            <div class="form-field">
                <label for="pickup_location">الإستلام من (إجباري) *</label>
                <input type="text" id="pickup_location" name="fields[الإستلام من]" required>
            </div>

            <div class="form-field">
                <label for="delivery_location">التوصيل (إجباري) *</label>
                <input type="text" id="delivery_location" name="fields[التوصيل]" required>
            </div>

            <div class="form-field">
                <label for="delivery_date">التاريخ (إجباري) *</label>
                <input type="date" id="delivery_date" name="fields[التاريخ]" required>
            </div>

            <div class="form-field">
                <label for="delivery_time">الوقت (إجباري) *</label>
                <input type="time" id="delivery_time" name="fields[الوقت]" required>
            </div>

            <div class="service-price">
                <p>Total Price: <span id="dsp-service-price" data-base-price="<?php echo esc_html($base_price); ?>"><?php echo esc_html($base_price); ?></span></p>
            </div>
            <button type="submit">Proceed to Checkout</button>
        </form>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('dsp_service_form', 'dsp_service_form_shortcode');








// Handle AJAX request to add to WooCommerce cart
function dsp_add_to_cart_ajax_handler() {
    // Check if required parameters are present
    if ( ! isset( $_POST['price'] ) || ! isset( $_POST['product_id'] ) ) {
        wp_send_json_error( array( 'message' => 'Missing required parameters.' ) );
        return;
    }

    // Get the product ID and price from the AJAX request
    $product_id = intval( $_POST['product_id'] );
    $price = floatval( $_POST['price'] );
    $form_data = isset( $_POST['fields'] ) ? $_POST['fields'] : array();

    // Validate the product ID
    if ( $product_id <= 0 ) {
        wp_send_json_error( array( 'message' => 'Invalid product ID.' ) );
        return;
    }

    // Handle image upload if it exists
    if ( ! empty( $_FILES['fields']['name']['image_upload'] ) ) {
        $file = array(
            'name'     => $_FILES['fields']['name']['image_upload'],
            'type'     => $_FILES['fields']['type']['image_upload'],
            'tmp_name' => $_FILES['fields']['tmp_name']['image_upload'],
            'error'    => $_FILES['fields']['error']['image_upload'],
            'size'     => $_FILES['fields']['size']['image_upload'],
        );

        $uploaded_file = wp_handle_upload( $file, array( 'test_form' => false ) );

        if ( isset( $uploaded_file['url'] ) ) {
            $form_data['image_upload'] = $uploaded_file['url'];
        } else {
            wp_send_json_error( array( 'message' => 'Image upload failed.' ) );
            return;
        }
    }

    // Add the product to the cart with custom price and form data
    $cart_item_data = array(
        'custom_price' => $price,
        'fields'       => $form_data,
    );

    // Generate a unique key to prevent merging similar products in the cart
    $unique_cart_item_key = md5( microtime() . rand() );
    $cart_item_data['unique_key'] = $unique_cart_item_key;

    $cart_item_key = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );

    if ( $cart_item_key ) {
        wp_send_json_success();
    } else {
        wp_send_json_error( array( 'message' => 'Failed to add product to cart.' ) );
    }
}
add_action( 'wp_ajax_dsp_add_to_cart', 'dsp_add_to_cart_ajax_handler' );
add_action( 'wp_ajax_nopriv_dsp_add_to_cart', 'dsp_add_to_cart_ajax_handler' );

// Apply custom price to cart items
function dsp_apply_custom_price( $cart_object ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['custom_price'] ) ) {
            $cart_item['data']->set_price( $cart_item['custom_price'] );
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'dsp_apply_custom_price' );

// Display form data in cart and checkout
function dsp_display_form_data_in_cart( $item_data, $cart_item ) {
    if ( isset( $cart_item['fields'] ) ) {
        foreach ( $cart_item['fields'] as $key => $value ) {
            if ( $key === 'image_upload' ) {
                $value = '<img src="' . esc_url( $value ) . '" style="max-width:100px;height:auto;">';
            } else {
                $value = wc_clean( $value );
            }
            $item_data[] = array(
                'key'   => wc_clean( $key ),
                'value' => $value,
            );
        }
    }
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'dsp_display_form_data_in_cart', 10, 2 );

// Save custom data to order
function dsp_save_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    if ( isset( $values['fields'] ) ) {
        foreach ( $values['fields'] as $key => $value ) {
            $item->add_meta_data( $key, $value, true );
        }
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'dsp_save_custom_data_to_order', 10, 4 );
/*
// Function to create pages for each service
function dsp_create_service_pages() {
    $args = array(
        'post_type'      => 'dsp_service',
        'posts_per_page' => -1,
    );
    $services = get_posts( $args );

    foreach ( $services as $service ) {
        $page_title   = $service->post_title;
        $page_content = '[dsp_service_form service_id="' . $service->ID . '"]';
        $page_check   = get_page_by_title( $page_title, 'OBJECT', 'page' );

        if ( ! isset( $page_check->ID ) ) {
            $new_page = array(
                'post_title'   => $page_title,
                'post_content' => $page_content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
                'post_name'    => sanitize_title( $page_title ),
            );
            wp_insert_post( $new_page );
        }
    }
}
add_action( 'init', 'dsp_create_service_pages' );
*/