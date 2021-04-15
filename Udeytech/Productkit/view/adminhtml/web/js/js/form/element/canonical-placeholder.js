/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                urlKey: '${ $.provider }:data.url_key'
            }
        },
        initialize: function () {
            this._super();
            this.placeholder = this.get('urlKey');
            return this;
        },
    });
});
