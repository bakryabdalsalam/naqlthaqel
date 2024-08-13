<?php

// Shortcode to display service form
function dsp_service_form_shortcode($atts) {
    global $post;
    
    $base_price = get_field('base_price', $post->ID);
    $wc_product_id = get_field('wc_product_id', $post->ID);
    $form_fields = get_field('form_fields', $post->ID);

    ob_start();
    ?>
    <div class="dsp-service-form">
        <h3><?php the_title(); ?></h3>
        <form id="dsp-service-form" data-product-id="<?php echo esc_attr($wc_product_id); ?>">
            <?php if ($form_fields) : ?>
                <?php foreach ($form_fields as $field) : ?>
                    <div class="form-field">
                        <label><?php echo esc_html($field['label']); ?></label>
                        <?php if ($field['type'] === 'text') : ?>
                            <input type="text" name="fields[<?php echo esc_attr($field['label']); ?>]" data-price="<?php echo esc_attr($field['price_increment']); ?>">
                        <?php elseif ($field['type'] === 'dropdown') : ?>
                            <select name="fields[<?php echo esc_attr($field['label']); ?>]" data-price="<?php echo esc_attr($field['price_increment']); ?>">
                                <?php foreach (explode(',', $field['options']) as $option) : ?>
                                    <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($field['type'] === 'checkbox') : ?>
                            <input type="checkbox" name="fields[<?php echo esc_attr($field['label']); ?>]" data-price="<?php echo esc_attr($field['price_increment']); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="service-price">
                <p>Total Price: <span id="dsp-service-price"><?php echo esc_html($base_price); ?></span></p>
            </div>
            <button type="submit">Proceed to Checkout</button>
        </form>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('dsp_service_form', 'dsp_service_form_shortcode');

// Handle AJAX request to add to WooCommerce cart
function dsp_add_to_cart() {
    $product_id = intval($_POST['product_id']);
    $price = floatval($_POST['price']);
    $form_data = sanitize_text_field($_POST['form_data']);

    // Add the product to the cart with custom price
    $cart_item_data = array(
        'custom_price' => $price,
        'form_data' => $form_data
    );

    $cart_item_key = WC()->cart->add_to_cart($product_id, 1, 0, array(), $cart_item_data);

    if ($cart_item_key) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_dsp_add_to_cart', 'dsp_add_to_cart');
add_action('wp_ajax_nopriv_dsp_add_to_cart', 'dsp_add_to_cart');

// Apply custom price to cart items
function dsp_apply_custom_price($cart_object) {
    foreach ($cart_object->get_cart() as $cart_item) {
        if (isset($cart_item['custom_price'])) {
            $cart_item['data']->set_price($cart_item['custom_price']);
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'dsp_apply_custom_price');

// Display form data in cart and checkout
function dsp_display_form_data_in_cart($item_data, $cart_item) {
    if (isset($cart_item['form_data'])) {
        $item_data[] = array(
            'name' => 'Service Details',
            'value' => sanitize_text_field($cart_item['form_data']),
        );
    }
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'dsp_display_form_data_in_cart', 10, 2);

// Save custom data to order
function dsp_save_custom_data_to_order($item, $cart_item_key, $values, $order) {
    if (isset($values['form_data'])) {
        $item->add_meta_data('Service Details', $values['form_data']);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'dsp_save_custom_data_to_order', 10, 4);

