/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Dotsquares_Faq/js/tag-it'
], function (jQuery, Abstract) {
    'use strict';

    return Abstract.extend({
        initialize: function () {
            this._super();
            var allTags = this.tags, input = "input[name='" + this.inputName + "']";
            jQuery.async(input, (function () {
                jQuery(input).tagit({
                    availableTags: allTags
                });
            }));

            return this;
        }
    });
});
