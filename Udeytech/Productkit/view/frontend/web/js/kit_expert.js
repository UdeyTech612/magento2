/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define(["jquery"], function ($) {
    "use strict";
    return function (data, node) {
        $(document).ready(function ($) {
            $('.selected-product').change(function (event) {
                var currentProducts = $('#selected_products').val().split(',');
                var selectedValue = $(this).val();
                if ($(this).prop('checked')) {
                    if ($.inArray(selectedValue, currentProducts) < 0) {
                        currentProducts.push(selectedValue);
                    }
                } else {
                    if ($.inArray(selectedValue, currentProducts) > -1) {
                        currentProducts.splice($.inArray(selectedValue, currentProducts), 1);
                    }
                }
                $('#selected_products').val(currentProducts);
            })
        });
    }
});
