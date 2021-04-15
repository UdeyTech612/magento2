/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
require(['jquery','mage/url'], function ($,url) {
    'use strict';
    jQuery(document).ready(function () {
        jQuery(document).ajaxStop(function () {
            $("#tetnew").click(function () {
                var datastring = $("#preview_form").serialize();
                $.ajax({
                    type: "POST",
                    url: url.build('admin/adminhtml/closeups/save'),
                    data: datastring,
                    success: function (data) {
                        alert('Data send');
                    }
                });
            });
        });
    });
});
