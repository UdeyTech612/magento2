/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'domReady!'
], function ($, _) {
    'use strict';

    $.widget('mage.faqQuestionsGrid', {
        options: {
            uniqId: ''
        },

        /**
         *
         * @constructor
         */
        _create: function () {
            this.initListeners();
        },

        initListeners: function () {
            $(document).on('dsDynamicRowsUpdated' + this.options.uniqId, this.processDynamicRowsUpdate.bind(this));
            $(document).on('click', '#addSelectedFiles', this.processAddSelectedQuestions.bind(this));
        },

        /**
         * @listens dsDynamicRowsUpdated
         */
        processDynamicRowsUpdate: function (event, eventData) {
            eventData = eventData || {};
            $('#' + this.options.uniqId + 'value').val(btoa(JSON.stringify(eventData.elements)));
        },

        /**
         * @fires dsDynamicRowsAdd
         * @listens onclick
         */
        processAddSelectedQuestions: function () {
            var checkedElements = {}, gridModal = window[this.options.uniqId];
            $('.col-question_ids input[type="checkbox"]:checked').each(function (i, elem) {
                checkedElements[$(elem).val()] = {
                    'data': {
                        question_id: $(elem).val(),
                        title: $(elem).closest('tr').find('td.col-title').text().trim()
                    }
                };
            });

            if (!_.isEmpty(checkedElements)) {
                $(document).trigger(
                    'dsDynamicRowsAdd' + this.options.uniqId,
                    {elements: checkedElements}
                );
            }

            if (gridModal) {
                gridModal.close();
            }
        }
    });

    return $.mage.faqQuestionsGrid;
});
