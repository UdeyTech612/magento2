/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * This script is a simple wrapper that allows you to use OwlCarousel with Magento 2
 */
define([
    "jquery",
    "Udeytech_Totalcustomization/js/owl.carousel.min"
], function ($) {
    return function (config, element) {
        return $(element).owlCarousel(config);
    }
});
