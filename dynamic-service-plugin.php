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

// Define paths (you can remove these if not used elsewhere)
define('DYNAMIC_SERVICE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DYNAMIC_SERVICE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once DYNAMIC_SERVICE_PLUGIN_PATH . 'includes/custom-post-types.php';
require_once DYNAMIC_SERVICE_PLUGIN_PATH . 'includes/acf-fields.php';
require_once DYNAMIC_SERVICE_PLUGIN_PATH . 'includes/shortcodes.php';

// Enqueue styles
function dsp_enqueue_styles() {
    // Enqueue the custom CSS for the service form
    wp_enqueue_style(
        'dsp-service-form',
        plugins_url('assets/css/dsp-service-form.css', __FILE__),
        array(),
        '1.0.0',
        'all'
    );
}
add_action('wp_enqueue_scripts', 'dsp_enqueue_styles');

// Add inline JavaScript code
function dsp_add_inline_script() {
    // Prepare parameters for JavaScript
    $dsp_params = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'cart_url' => wc_get_cart_url(),
    );
    ?>
    <script type="text/javascript">
    var dsp_params = <?php echo json_encode($dsp_params); ?>;
    (function($) {
        $(document).ready(function() {
            // Function to calculate the total price based on selected options
            function calculateTotalPrice() {
                // Retrieve the base price from the element's data attribute
                var basePriceAttr = $('#dsp-service-price').attr('data-base-price');
                console.log("Raw base price attribute:", basePriceAttr);

                var basePrice = parseFloat(basePriceAttr) || 0;
                var totalPrice = basePrice;

                console.log("Parsed base price:", basePrice);

                // Loop through all select fields in the form and calculate the price based on selected options
                $('#dsp-service-form-446 select').each(function() {
                    var selectedOption = $(this).find('option:selected');
                    var selectedOptionPriceAttr = selectedOption.attr('data-price');
                    console.log("Raw selected option price attribute:", selectedOptionPriceAttr);

                    // Trim whitespace and parse the price
                    var selectedOptionPrice = parseFloat($.trim(selectedOptionPriceAttr));

                    // Log for debugging purposes
                    console.log("Selected option:", selectedOption.val(), "Parsed Price:", selectedOptionPrice);

                    if (!isNaN(selectedOptionPrice)) {
                        totalPrice += selectedOptionPrice;
                    } else {
                        console.warn("No valid price found for selected option:", selectedOption.val());
                    }
                });

                // Update the displayed total price
                console.log("Total price calculated:", totalPrice);
                $('#dsp-service-price').text(totalPrice.toFixed(2));
            }

            // Recalculate price when any select field changes
            $(document).on('change', '#dsp-service-form-446 select', function() {
                console.log("Selection changed in field:", $(this).attr('name'));
                calculateTotalPrice();
            });

            // Form submission
            $('#dsp-service-form-446').on('submit', function(e) {
                e.preventDefault();

                var productId = $(this).data('product-id');
                var price = $('#dsp-service-price').text();
                var formData = new FormData(this);  // Use FormData to include file uploads

                // Append the calculated price to the FormData object
                formData.append('price', price);
                formData.append('action', 'dsp_add_to_cart');
                formData.append('product_id', productId);

                // AJAX request to add to cart
                $.ajax({
                    url: dsp_params.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,  // Important: Do not process data, because we are using FormData
                    contentType: false,  // Important: Do not set content type header, let jQuery do it for us
                    success: function(response) {
                        console.log('AJAX response:', response);
                        if (response.success) {
                            window.location.href = dsp_params.cart_url;
                        } else {
                            alert('Failed to add the service to the cart. Please try again.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('AJAX error:', textStatus, errorThrown);
                        alert('An error occurred while processing the request.');
                    }
                });
            });

            // Initial calculation on page load
            calculateTotalPrice();
        });
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'dsp_add_inline_script');

