/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/ui-select',
    'underscore'
], function (Select, _) {
    'use strict';

    return Select.extend({
        initialize: function () {
            this._super();
            var elementValue = this.value();

            elementValue = _.isString(elementValue) ? elementValue.split(',') : elementValue;
            this.value(elementValue);

            return this;
        }
    });
});
