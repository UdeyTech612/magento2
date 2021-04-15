/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

require(['jquery'], function ($) {
    'use strict';
    $(document).ready(function () {
        alert('testmessage');
        console.log('hello');
        var lookbook = {
            group_products_qty: {},
            animation_speed: null,
            marker_width: null,
            marker_height: null,
            marker_type: 'dynamic',
            popup_close_image: null,
            popup_close_button: null,
            popup_duration: null,
            popup_overlay_opacity: null,
            mapster_enabled: null,
            mapster_fillOpacity: null,
            mapster_fillColor: null,
            mapster_strokeColor: null,
            mapster_strokeOpacity: null,
            mapster_strokeWidth: null,
            mapster_fade: null,
            mapster_fadeDuration: null,
            popup_content_width: null,
            popup_content_height: null,
            popup_content_percent: 0.4,
            refreshGroupQty: function (id) {
                qty = this.group_products_qty[id];
                if (!isNaN(parseInt(qty))) {
                    $('#qty_container_' + id).find('#qty').val(qty);
                    $("input[name='super_group[" + id + "]']").val(qty);
                    $('#group-item-plus-qty-' + id).text(qty);
                    if (qty == 0) {
                        $('#group-item-plus-qty-' + id).hide();
                    } else {
                        $('#group-item-plus-qty-' + id).show();
                    }
                }
            },
            initPopups: function () {
                $('.group-plus a').nm({
                    closeOnClick: true,
                    closeOnEscape: true,
                    closeButton: (this.popup_close_button) ? '<a href="#" class="nyroModalClose nyroModalCloseButton nmReposition" title="close">' + this.popup_close_image + '</a>' : '',
                    anim: {def: false},
                    anims: {
                        showBg: function (nm, callback) {
                            nm.elts.bg.css({'opacity': 0}).fadeTo(here.popup_duration, here.popup_overlay_opacity, callback);
                        },
                        showCont: function (nm, callback) {
                            nm.resize(true);
                            nm.elts.cont.css({'opacity': 0}).fadeTo(here.popup_duration, 1, callback);
                        },
                        hideCont: function (nm, callback) {
                            nm.elts.cont.css({'opacity': 1}).fadeTo(here.popup_duration, 0, callback);
                        },
                        hideBg: function (nm, callback) {
                            nm.elts.bg.css({'opacity': here.popup_overlay_opacity}).fadeTo(here.popup_duration, 0, callback);
                        },
                        resize: function (nm, callback) {
                            here.resizeContentWidth();
                            nm.resize(true);
                            nm.elts.cont.css({
                                width: nm.sizes.w,
                                height: nm.sizes.h,
                                top: (nm.getInternal().fullSize.viewH - nm.sizes.h - nm.sizes.hMargin / 2) / 2,
                                left: (nm.getInternal().fullSize.viewW - nm.sizes.w - nm.sizes.wMargin / 2) / 2
                            });
                            callback();
                        }
                    }
                });
            },
            slideUpName: function (element, dir) {
                id = element.attr('data-product-id');
                $('#group_item_container_' + id).find('.group-item-container-name').stop(true, true).animate({left: '0px'}, this.animation_speed);
            },
            slideDownName: function (element, dir) {
                if (this.marker_type == 'dynamic') {
                    tmp = (dir == 'left') ? '-' : '';
                    id = element.attr('data-product-id');
                    $('#group_item_container_' + id).find('.group-item-container-name').stop(false, false).delay(300).animate({left: tmp + $('#group_item_container_' + id).find('.group-item-container-name').outerWidth() + 'px'}, this.animation_speed, function () {
                        $('#group_item_wrap_' + id).removeClass('active');
                    });
                }
            },
            initMapster: function () {
                $('img.group-image').mapster({
                    fillOpacity: this.mapster_fillOpacity,
                    fillColor: this.mapster_fillColor,
                    strokeColor: this.mapster_strokeColor,
                    strokeOpacity: this.mapster_strokeOpacity,
                    strokeWidth: this.mapster_strokeWidth,
                    fade: this.mapster_fade,
                    fadeDuration: this.mapster_fadeDuration,
                    stroke: true,
                    isSelectable: false,
                    singleSelect: true,
                    mapKey: 'id',
                    listKey: 'id',
                    onClick: function () {
                        id = $(this).attr('data-product-id');
                        $('#group_item_plus_' + id + ' a').trigger('click');
                    }
                });
            },
            getImageSizeReal: function (imgLink) {
                var img = new Image();
                img.src = imgLink;
                return [img.width, img.height];
            },
            init: function () {
                here = this;
                this.initPopups();
                imgs = [];
                $('#group-image').find('img').each(function (index) {
                    imgs.push($(this).attr('src'));
                });
                if (imgs.length > 0) {
                    $.imgpreload(imgs, function () {
                        here.elementsReposition();
                        here.resizeContentWidth();
                        $('#group-image').animate({'opacity': 1}, here.animation_speed);
                        $(window).resize(function () {
                            here.elementsReposition();
                            here.resizeContentWidth();
                        });
                    })
                }
            },
            elementsReposition: function () {
                $('map area').each(function () {
                    tmp = $(this).attr('data-coords');
                    coords_array = tmp.split(',');
                    imageRealSize = here.getImageSizeReal($('#group-image img').attr('src'));
                    new_array = [];
                    for (var i = 0; i < coords_array.length; i++) {
                        new_array.push(parseInt(coords_array[i] * imageRealSize[0]));
                        i++;
                        new_array.push(parseInt(coords_array[i] * imageRealSize[1]));
                    }
                    $(this).attr('coords', new_array.join());
                });

                $('.group-item-plus').each(function () {
                    id = $(this).attr('data-product-id');
                    width = $('#group-image').width() * $(this).attr('data-pos-x') - here.marker_width / 2;
                    height = $('#group-image').height() * $(this).attr('data-pos-y') - here.marker_height / 2;
                    main_el = $(this);
                    $(this).css('left', width);
                    $(this).css('top', height);

                    if ($(this).position().left <= parseInt($('#group-image').width() / 2)) { /*left part*/
                        tmp_el = $('#group_item_container_' + id).find('.group-item-container-name');
                        tmp_el.css('padding-left', here.marker_width / 2 + 'px').css('left', '-' + $('#group_item_container_' + id).find('.group-item-container-name').outerWidth() + 'px');
                        tmp_el.css('width', tmp_el.outerWidth() + 'px').css('position', 'absolute');
                        tmp_el.parent().css('width', parseInt(tmp_el.outerWidth()) + 'px');
                        tmp_el.parent().css('height', parseInt(tmp_el.outerHeight()) + 'px');

                        $('#group_item_container_' + id).addClass('left').css('left', parseInt(main_el.position().left + main_el.outerWidth() / 2) + 'px').css('top', main_el.position().top + 'px');
                        tmp_el.css('left', '-' + $('#group_item_container_' + id).find('.group-item-container-name').outerWidth() + 'px');

                        $('#group_item_wrap_' + id).unbind('mouseenter mouseleave').hover(function () {
                            id = $(this).attr('data-product-id');
                            $('#area_' + id).trigger('mouseenter');
                        }, function () {
                            id = $(this).attr('data-product-id');
                            $('#area_' + id).trigger('mouseleave');
                        });

                        $('#area_' + id).unbind('mouseenter mouseleave').hover(function () {
                            id = $(this).attr('data-product-id');
                            $('#group_item_wrap_' + id).addClass('active');
                            here.slideUpName($(this), 'left');
                        }, function () {
                            id = $(this).attr('data-product-id');
                            here.slideDownName($(this), 'left');
                        });


                    } else { /*right part*/
                        dir = 'right';
                        tmp_el = $('#group_item_container_' + id).find('.group-item-container-name');
                        tmp_el.css('padding-right', here.marker_width / 2 + 'px').css('left', $('#group_item_container_' + id).find('.group-item-container-name').outerWidth() + 'px');
                        tmp_el.css('width', tmp_el.outerWidth() + 'px').css('position', 'absolute');
                        tmp_el.parent().css('width', parseInt(tmp_el.outerWidth()) + 'px');
                        tmp_el.parent().css('height', parseInt(tmp_el.outerHeight()) + 'px');

                        $('#group_item_container_' + id).addClass('right').css('left', parseInt(main_el.position().left + main_el.outerWidth() / 2 - $('#group_item_container_' + id).outerWidth()) + 'px').css('top', main_el.position().top + 'px');
                        tmp_el.css('left', $('#group_item_container_' + id).find('.group-item-container-name').outerWidth() + 'px');

                        $('#group_item_wrap_' + id).unbind('mouseenter mouseleave').hover(function () {
                            id = $(this).attr('data-product-id');
                            $('#area_' + id).trigger('mouseenter');
                        }, function () {
                            id = $(this).attr('data-product-id');
                            $('#area_' + id).trigger('mouseleave');
                        });

                        $('#area_' + id).unbind('mouseenter mouseleave').hover(function () {
                            id = $(this).attr('data-product-id');
                            $('#group_item_wrap_' + id).addClass('active');
                            here.slideUpName($(this), 'right');
                        }, function () {
                            id = $(this).attr('data-product-id');
                            here.slideDownName($(this), 'right');
                        });
                    }

                    if (here.marker_type == 'static') {
                        $('#group_item_wrap_' + id).addClass('active');
                        $('#group_item_container_' + id).find('.group-item-container-name').css('left', '0');
                    }
                });

                if (this.mapster_enabled) {
                    this.initMapster();
                }
            },
            resizeContentWidth: function () {
                if (this.popup_content_width / this.popup_content_percent > $(document).width()) {
                    $('.group-item-plus .item, .nyroModalDom .item').css('width', parseInt($(document).width() * this.popup_content_percent) + 'px');
                } else {
                    $('.group-item-plus .item, .nyroModalDom .item').css('width', this.popup_content_width + 'px');
                }
            }
        };
        (function ($) {
            $.imgpreload = function (imgs, settings) {
                settings = $.extend({}, $.fn.imgpreload.defaults, (settings instanceof Function) ? {all: settings} : settings);
                if ('string' == typeof imgs) {
                    imgs = new Array(imgs);
                }
                var loaded = [];
                $.each(imgs, function (i, elem) {
                    var img = new Image();
                    var url = elem;
                    var img_obj = img;
                    if ('string' != typeof elem) {
                        url = $(elem).attr('src');
                        img_obj = elem;
                    }
                    $(img).bind('load error', function (e) {
                        loaded.push(img_obj);
                        $.data(img_obj, 'loaded', ('error' == e.type) ? false : true);
                        if (settings.each instanceof Function) {
                            settings.each.call(img_obj);
                        }
                        if (loaded.length >= imgs.length && settings.all instanceof Function) {
                            settings.all.call(loaded);
                        }
                        $(this).unbind('load error');
                    });
                    img.src = url;
                });
            };
            $.fn.imgpreload = function (settings) {
                $.imgpreload(this, settings);
                return this;
            };
            $.fn.imgpreload.defaults = {
                each: null,
                all: null
            };
        })($);
    });
});
