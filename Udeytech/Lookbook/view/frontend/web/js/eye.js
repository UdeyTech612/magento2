/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 *
 * Zoomimage
 * Author: Stefan Petre www.eyecon.ro
 *
 */
;(function ($) {
    var EYE = window.EYE = function () {
        var _registered = {
            init: []
        };
        return {
            init: function () {
                $.each(_registered.init, function (nr, fn) {
                    fn.call();
                });
            },
            extend: function (prop) {
                for (var i in prop) {
                    if (prop[i] != undefined) {
                        this[i] = prop[i];
                    }
                }
            },
            register: function (fn, type) {
                if (!_registered[type]) {
                    _registered[type] = [];
                }
                _registered[type].push(fn);
            }
        };
    }();
    $(EYE.init);
})(jQuery);
