jQuery(document).ready(function($) {
    var basePrice = parseFloat($('#dsp-service-price').text());

    $('#dsp-service-form input, #dsp-service-form select').on('change', function() {
        var totalPrice = basePrice;

        $('#dsp-service-form input, #dsp-service-form select').each(function() {
            if ($(this).is(':checked') || $(this).is('select')) {
                var priceIncrement = parseFloat($(this).data('price')) || 0;
                totalPrice += priceIncrement;
            }
        });

        $('#dsp-service-price').text(totalPrice.toFixed(2));
    });

    $('#dsp-service-form').on('submit', function(e) {
        e.preventDefault();

        var productId = $(this).data('product-id');
        var price = $('#dsp-service-price').text();
        var formData = $(this).serialize();

        // AJAX request to add to cart
        $.ajax({
            url: dsp_params.ajax_url,
            type: 'POST',
            data: {
                action: 'dsp_add_to_cart',
                product_id: productId,
                price: price,
                form_data: formData
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = dsp_params.cart_url;
                } else {
                    alert('Failed to add the service to the cart. Please try again.');
                }
            }
        });
    });
});
