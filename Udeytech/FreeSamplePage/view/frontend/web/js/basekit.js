/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define(["jquery", "mage/url"], function ($, url) {
    "use strict";
    return function (data, node) {
        var WINDOW_SIZE_BREAKPOINT = 770;
        $(document).ready(function () {
            if (typeof (faceCarousels) !== 'undefined') {
                var faceCarouselGroup = new carouselsGroup(faceCarousels);
                if ($(window).width() < WINDOW_SIZE_BREAKPOINT) {
                    faceCarouselGroup.init();
                }
                $(window).resize(this, function () {
                    var checkWidth = $(this).width();
                    if (checkWidth < WINDOW_SIZE_BREAKPOINT) {
                        faceCarouselGroup.init();
                    } else {
                        faceCarouselGroup.destroy();
                    }
                });
            }
            $('.expertkit-item .empty').on('click', function () {
                var item = $(this).parents('.expertkit-item').get(0);
                BaseKit.setKitItem(item);
                if ($(window).width() < WINDOW_SIZE_BREAKPOINT) {
                    var goToCat = $('.expertkit-categories-box.selected');
                    if (goToCat.length) {
                        $('html,body').animate({scrollTop: goToCat.offset().top}, 'slow');
                    }
                }
            });
            $('.expertkit-item .validation-notice').on('click', function () {
                var item = $(this).parents('.expertkit-item').get(0);
                BaseKit.setKitItem(item);
            });
            $('.expertkit-category').on('click', function () {
                BaseKit.setKitCategory(this);

                if ($(window).width() < WINDOW_SIZE_BREAKPOINT) {
                    var goToProd = $('.expertkit-select-products-box.selected');
                    if (goToProd.length) {
                        $('html,body').animate({scrollTop: goToProd.offset().top}, 'slow');
                    }
                }
            });
            $('.expertkit-select-product').on('click', function () {
                BaseKit.addSelectedProduct(this);
            });
            $('.reset-item-button').on('click', function () {
                BaseKit.removeSelectedProduct(this);
            });
            $('.expertkit-addtocart-action').on('click', function () {
                if (!BaseKit.isCartValid()) {
                    return false;
                } else {
                    return true;
                }
            });
        });
        var BaseKit = {
            itemsAmount: 4,
            itemsAmounts: 7,
            currentItemId: '',
            currentCategoryId: '',
            selectedProducts: {},
            defaultCategory: {},
            removeSelectedProduct: function removeSelectedProduct(element) {
                var item = $(element).parents('.expertkit-item').get(0);
                var id = item.id.replace('expertkit_item_id_', '');
                delete this.selectedProducts[id];
                this.resetItemsContent(item);
                this.setKitItem(item);
                $('.expertkit-item').removeClass('selected');
            },
            resetItemsContent: function resetSelectedItems(element) {
                if (typeof element == "undefined") {
                    element = $('.expertkit-item');
                }
                $(element).find('.content').hide();
                $(element).find('.content .wrap').html('');
                $(element).find('.empty').show();
            },
            getProductContent: function getProductContent(element) {
                var content = {};
                content.name = $(element).find('.product-name').get(0).outerHTML;
                content.price = $(element).find('.product-price').get(0).outerHTML;
                content.image = $(element).find('img').get(0).outerHTML;
                content.option = $(element).find('input').get(0).outerHTML;
                return content;
            },
            prepareProductContent: function prepareProductContent(content) {
                var contentHtml = content.image + content.name + content.price + content.option;
                return contentHtml;
            },
            updateKitItemContent: function updateKitItemContent(content) {
                var contentHtml = this.prepareProductContent(content);
                var id = '#expertkit_item_id_' + this.currentItemId;
                if ($(id + ' .content .wrap').length == 0) {
                    //$(id).append(contentHtml);
                } else {
                    $(id + ' .content .wrap').html(contentHtml).find('input').removeAttr('disabled');
                    $(id + ' .content').show();
                    $(id + ' .empty').hide();
                    $(id + ' .validation-notice').hide();
                }
            },
            resetKitItem: function resetKitItem() {
                this.currentItemId = '';
                $('.expertkit-item').removeClass('selected');
                $('.expertkit-categories-box').removeClass('selected').hide();
                $('.customkit-page-section-item-box').hide();
                this.resetKitCategory();
                this.resetSelectedProduct();
                this.resetItemsContent();
            },
            setKitItem: function setKitItem(element) {
                this.resetKitCategory();
                var id = element.id.replace('expertkit_item_id_', ''); //* remove all before '-'
                this.currentItemId = id;
                $('.expertkit-item').removeClass('selected');
                $(element).addClass('selected');
                $(element).find('.validation-notice').hide();
                $(element).find('.empty').show();
                $('.customkit-page-section-item-box').addClass('visible').show();
                $('.expertkit-categories-box').removeClass('selected').hide();
                $('#expertkit_categories_box_id_' + id).addClass('selected').show();
                $('.customkit-page-section-item-box').show();
                $('#basekit_addtocart_action').show();
                this.setDefaultsCategory();
            },
            setDefaultsCategory: function setDefaultsCategory() {
                var category;
                if (typeof this.defaultCategory[this.currentItemId] == 'undefined') {
                    category = $('#expertkit_categories_box_id_' + this.currentItemId).find('.expertkit-category').removeClass('selected').get(0);
                    $(category).addClass('selected');
                    this.defaultCategory[this.currentItemId] = category;
                }
                this.setKitCategory(this.defaultCategory[this.currentItemId]);
            },
            resetKitCategory: function resetKitCategory() {
                this.currentCategoryId = '';
                $('.expertkit-category').removeClass('selected');
                $('.expertkit-select-products-box').removeClass('selected').hide();
            },
            setKitCategory: function setKitCategory(element) {
                var id = element.id.replace('expertkit_category_id_', ''); //* remove all before '-'
                this.currentCategoryId = id;
                this.defaultCategory[this.currentItemId] = element;
                $('.expertkit-category').removeClass('selected');
                $(element).addClass('selected');
                $('.expertkit-select-products-box').removeClass('selected').hide();
                if ($('#expertkit_select_products_box_id_' + id).length == 0) {
                    //this.loadProducts(id);
                } else {
                    $('#expertkit_select_products_box_id_' + id).addClass('selected').show();
                }
            },
            resetSelectedProduct: function resetSelectedProduct() {
                this.selectedProducts = {};
            },
            addSelectedProduct: function addSelectedProduct(element) {
                var id = element.id.replace('expertkit_select_product_id_', '');
                this.selectedProducts[this.currentItemId] = id;
                $('.expertkit-select-product').removeClass('selected');
                $(element).addClass('selected');
                this.updateKitItemContent(this.getProductContent(element));
            },
            hideItemBox: function () {
                this.currentCategoryId = '';
                $('.customkit-page-section-item-box').removeClass('visible').hide();
            },
            showValidNoticeForItems: function showValidNoticeForItems() {
                var selectedItemsIds = Object.keys(this.selectedProducts);
                $('.expertkit-item').each(function (idx, element) {
                    var id = element.id.replace('expertkit_item_id_', '');
                    if ($.inArray(id, selectedItemsIds) < 0) {
                        $(element).find('.empty').hide();
                        $(element).find('.content').hide();
                        $(element).find('.validation-notice').show();
                    }
                });
            },
            isCartValid: function isCartValid() {
                if ($('.expertkit-item').find('input').length != this.itemsAmount && $('.expertkit-item').find('input').length != this.itemsAmounts) {
                    this.showValidNoticeForItems();
                    return false;
                }
                return true;
            },
            addToCart: function addToCart(button) {
                if (!this.isCartValid()) {
                    return false;
                }
                this.kitCart = [];
                var params = {
                    isAjax: 'true',
                    type: this.kitType,
                    selected: JSON.stringify(this.kitCart),
                    qty: this.qty,
                    productId: $("#mainproduct").val(),
                };
                $.ajax({
                    type: "POST",
                    url: url.build('freesamplepage/ajaxpostb/ajpostb'),
                    data: params,
                    dataType: "json",
                    success: function (data) {
                        if (data) {
                            window.location.reload();
                        }
                    }.bind(this)
                });
                this.resetKitItem();
                return false;
            }
        };
        (function ($) {
            $.fn.categoriesPaginator = function () {
                return this.each(function () {
                    var paginator = $.extend({}, $.fn.categoriesPaginator.paginator);
                    paginator.create($(this));
                });
            };
            $.fn.categoriesPaginator.paginator = {
                create: function ($element) {
                    this.$element = $element;
                    this.$startIndex = 0;
                    this.$visibleItemsSum = 6;
                    this.$categoryItems = $element.find('.expertkit-category');
                    this.$itemsLength = this.$categoryItems.length;
                    this._updateItems(this.$startIndex, this.$visibleItemsSum - 1);
                    this._scrollItems(0);
                    this._init();
                },
                _showNavButton: function (button) {
                    $(this.$element).find(button).addClass('enable').removeClass('disable');
                },
                _hideNavButton: function (button) {
                    $(this.$element).find(button).addClass('disable').removeClass('enable');
                },
                _updateItems: function (start, finish) {
                    this.$categoryItems.each(function (idx, elm) {
                        if (idx >= start && idx <= finish) {
                            $(elm).addClass('visible').removeClass('invisible').show();
                        } else {
                            $(elm).addClass('invisible').removeClass('visible').hide();
                        }
                    });
                },
                _scrollItems: function (scroll) {
                    this.$startIndex = this.$startIndex + scroll;
                    if (this.$startIndex < 0) {
                        this.$startIndex = 0;
                        return;
                    }
                    if (this.$itemsLength <= this.$visibleItemsSum) {
                        this._hideNavButton('.prev');
                        this._hideNavButton('.next');
                        this._updateItems(0, this.$itemsLength - 1);
                        return;
                    }
                    var start = this.$startIndex;
                    var finish = this.$startIndex + this.$visibleItemsSum - 1;

                    if (finish > this.$itemsLength - 1) {
                        this.$startIndex--;
                        start = this.$startIndex;
                        finish--;
                        this._hideNavButton('.next');
                    }

                    if (start == 0) {
                        this._hideNavButton('.prev');
                    } else {
                        this._showNavButton('.prev');
                    }

                    if (finish == this.$itemsLength - 1) {
                        this._hideNavButton('.next');
                    } else {
                        this._showNavButton('.next');
                    }

                    this._updateItems(start, finish);
                },
                _init: function () {
                    var that = this;
                    this.$element
                        .on('click', '.prev', function () {
                            that._scrollItems(-1);
                        })
                        .on('click', '.next', function () {
                            that._scrollItems(1);
                        });
                }
            };
        }(jQuery));

        function carouselsGroup(carousels) {
            this.carousels = carousels;
            this.init = function init() {
                for (var key in this.carousels) {
                    if (this.carousels.hasOwnProperty(key)) {
                        this.initElement(this.carousels[key]);
                    }
                }
            };
            this.destroy = function destroy() {
                for (var key in this.carousels) {
                    if (this.carousels.hasOwnProperty(key)) {
                        this.destroyElement(this.carousels[key]);
                    }
                }
            };
            this.initElement = function initElement(element) {
                if (typeof element.data('owlCarousel') == 'undefined') {
                    element.owlCarousel({
                        rewindNav: false,
                        navigation: true,
                        navigationText: false,
                        pagination: false,
                        autoPlay: false,
                        singleItem: true
                    });
                    //$('.expertkit-items-box').hide();
                    //$('.expertkit-items-box.selected').show();

                    this.slideToCurrentItem(element);
                }
            };
            this.destroyElement = function destroyElement(element) {
                if (typeof element.data('owlCarousel') != 'undefined') {
                    if (element.hasClass('selected')) {
                        element.data('owlCarousel').destroy();
                        element.addClass('selected').show();
                    } else {
                        element.data('owlCarousel').destroy();
                    }
                }
            };
            this.slideToCurrentItem = function slideToCurrentItem(element) {
                if (typeof BaseKit.currentItemId != 'undefined') {
                    for (var step = 1; step < BaseKit.currentItemId; step++) {
                        element.data('owlCarousel').next();
                    }
                }
            };
        }
    }
});
