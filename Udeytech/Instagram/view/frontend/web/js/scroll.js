/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    "jquery/ui"
], function ($) {
    'use strict';
//    if (typeof UdeytechInstagram !== 'function') {
    $.widget('udeytech.scroller', {
        options: {
            handleSelector: '.udeytech-instagram-list',
            listSelector: '.udeytech-instagram-list',
            listItemSelector: '.udeytech-instagram-list-item',
            tickContainerSelector: '.ticks',
            tickTemplateSelector: '.template',
            initListener: 'udeytech-loaded'
        },
        _create: function () {
            this.moves = [];
            this.handles = $(this.options.handleSelector);
            this.configs = [];
            this.items = [];
            this.active = [];
            this.tickTpls = [];
        },

        getTimeout: function (timeoutCode) {
            var timeout = 5000;
            switch (timeoutCode) {
                case 'slow'   :
                    timeout = 10000;
                    break;
                case 'normal' :
                    timeout = 8000;
                    break;
                case 'fast'   :
                    timeout = 4000;
                    break;
            }
            return timeout;
        },

        _init: function () {
            var that = this;
            if (this.handles) {
                for (var i = 0; i < this.handles.length; i++) {
                    var slider = $(this.handles[i]);
                    slider.attr('data-index', i);
                    this.active[i] = 0;
                    this.handles[i] = slider;
                    this.moves[i] = '-';
                    this.configs[i] = $.parseJSON(slider.attr('data-options'));

                    var tickContainer = slider.find(this.options.tickContainerSelector);
                    var tpl = tickContainer.find(this.options.tickTemplateSelector);
                    if (tpl.length) {
                        this.tickTpls[i] = tpl.clone();
                        tpl.remove();
                    }
                    if (this.options.initListener) {
                        $(this.handles[i]).find(this.options.listSelector).on(this.options.initListener, function () {
                            that.start(this);
                        });
                    }

                }
            }
        },

        start: function (listElement) {
            listElement = $(listElement);
            var items = listElement.find(this.options.listItemSelector);
            var index = listElement.parent().attr('data-index') * 1;
            this.initTicks(index);
            listElement.width((items.length) * ($(items[0]).outerWidth(true)));
            this.items[index] = items;
            if (this.configs[index].autostart * 1
                && listElement.width() > listElement.parent().width()
            ) {
                this.move(index);
            }
        },

        initTicks: function (index) {
            var that = this;
            var template = this.tickTpls[index];
            var tickContainer = $(this.handles[index]).find(this.options.tickContainerSelector);
            if (template) {
                var items = tickContainer.parent().find(this.options.listItemSelector);
                for (var i = 0; i < items.length; i++) {
                    var tpl = template.clone();
                    if (i !== 0) {
                        tpl.removeClass('active');
                    }
                    tpl.attr('data-index', i);
                    tickContainer.append(tpl);
                    tpl.click(function () {
                        that.moveTo($(this).attr('data-index') * 1, tickContainer.parent().attr('data-index') * 1);
                    })
                }
            }
        },

        moveTo: function (itemIndex, handleIndex) {
            var that = this;
            var item = $(this.items[handleIndex][itemIndex]);
            if (item.hasClass('active')) {
                return;
            }
            var itemWidth = item.outerWidth(true);
            var list = item.parent();
            this.active[handleIndex] = itemIndex;
            that.setActiveTick(handleIndex);
            var pos = item.parent().width() - item.position().left;
            var left = pos >= item.parent().parent().width() ?
                item.position().left :
                item.parent().width() - item.parent().parent().width();
            list.stop(true).animate({
                    left: -1 * left
                },
                this.configs[handleIndex].timeout ? this.getTimeout(this.configs[handleIndex].timeout) : 2000,
                this.configs[handleIndex].easing ? this.configs[handleIndex].easing : 'linear',
                function () {
                    if (that.configs[handleIndex].autostart * 1) {
                        //schedule next animation
                        setTimeout(function () {
                            that.move(handleIndex);
                        }, 5000);
                    }
                }
            );
        },

        move: function (handleIndex) {
            var that = this;
            var parent = this.items[handleIndex].parent();
            var itemWidth = $(this.items[handleIndex][0]).outerWidth(true);

            if (parent.position().left >= 0) {
                this.moves[handleIndex] = '-';
            } else if (-1 * parent.position().left > (parent.width() - itemWidth - parent.parent().width())) {
                this.moves[handleIndex] = '+';
            }
            if (this.moves[handleIndex] == '-') {
                this.active[handleIndex]++;
            } else {
                this.active[handleIndex]--;
            }
            that.setActiveTick(handleIndex);
            //move container
            parent.animate({
                    left: this.moves[handleIndex] + "=" + itemWidth
                },
                this.configs[handleIndex].timeout ? this.getTimeout(this.configs[handleIndex].timeout) : 2000,
                this.configs[handleIndex].easing ? this.configs[handleIndex].easing : 'linear',
                function () {
                    //schedule next animation
                    that.move(handleIndex);
                }
            );

        },

        setActiveTick: function (index) {
            var tmp = this.items[index].parent().parent();
            var ticks = tmp.find(this.options.tickContainerSelector + ' .tick');
            if (ticks.length) {
                ticks.removeClass('active');
                tmp.find('.tick[data-index="' + this.active[index] + '"]').addClass('active');
            }
        }
    });


    return $.udeytech.instagram;
});
