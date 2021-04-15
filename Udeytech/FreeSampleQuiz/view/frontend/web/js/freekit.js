/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
define(["jquery", "mage/url"], function ($, url) {
    "use strict";
    return function (data, node) {
        var WINDOW_SIZE_BREAKPOINT_SMALL = 769;
        $(document).ready(function () {
            $(window).resize(function () {
                FreeKit.update();
            });
        });
        var FreeKit = {
            ITEMS_FOR_DELIMITER: 0,
            addToCartUrl: '',
            qty: 1,
            kitType: 'free',
            productsConteiner: {},
            categoriesConteiner: {},
            itemsBox: {},
            carousel: {},
            items: [],
            itemIdPrefix: '',
            samplesBox: [],
            sampleBoxIdPrefix: '',
            categoriesBoxIdPrefix: '',
            categoriesBox: {},
            products: [],
            removeBtn: [],
            itemsAmount: 5,
            currentItemId: '',
            currentCategoryId: '',
            selectedProducts: {},
            sampleProducts: [],
            sampleProductIdPrefix: '',
            selectCurrentState: '',
            categories: '',
            defaultCategory: {},
            slidersPosition: {},
            kitCart: [],
            addToCartItem: {},
            init: function () {
                this.itemsBox = data.itemsBox;
                this.items = data.items;
                this.products = data.products;
                this.removeBtn = data.removeBtn;
                this.itemIdPrefix = data.itemIdPrefix;
                this.samplesBox = data.samplesBox;
                this.sampleBoxIdPrefix = data.sampleBoxIdPrefix;
                this.sampleProducts = data.sampleProducts;
                this.sampleProductIdPrefix = data.sampleProductIdPrefix;
                this.categoriesBoxIdPrefix = data.categoriesBoxIdPrefix;
                this.categoryIdPrefix = data.categoryIdPrefix;
                this.categoriesBox = data.categoriesBox;
                this.categories = data.categories;
                this.productsConteiner = data.productsConteiner;
                this.categoriesConteiner = data.categoriesConteiner;
                this.ITEMS_FOR_DELIMITER = data.ITEMS_FOR_DELIMITER;
                this.carousel = new carouselsGroup([data.itemsBox], this);
                this.update();
                this.resetAllSelectedProduct();
            },
            funTest: function () {
                $('#freekit_addtocart_action').on('click', '#freekit_add_to_cart', this, function (e) {
                    FreeKit.addToCart(this);
                });
                $(".freekit-category").on('click', this, function (e) {
                    FreeKit.setKitCategory(this);
                    if ($(window).width() < WINDOW_SIZE_BREAKPOINT_SMALL) {
                        var goToProd = $('.freekit-select-products-box.selected');
                        if (goToProd.length) {
                            $('html,body').animate({scrollTop: goToProd.offset().top}, 'slow');
                        }
                    }
                });
                $('.freekit-item').on('click', '.empty', this, function (e) {
                    var item = $(this).parents('li').get(0);
                    FreeKit.setKitItem(item);
                    if ($(window).width() < WINDOW_SIZE_BREAKPOINT_SMALL) {
                        var goToCat = $('.freekit-categories-box.selected');
                        if (goToCat.length) {
                            $('html,body').animate({scrollTop: goToCat.offset().top}, 'slow');
                        }
                    }
                });
                $('.freekit-item').on('click', '.validation-notice', this, function (e) {
                    var item = $(this).parents('li');
                    item.children('.validation-notice').addInlineDisplayNone();
                    item.children('.empty').show();
                    FreeKit.setKitItem(item.get(0));
                });
                $('.freekit-item').on('click', '.content .wrap', this, function (e) {
                    var item = $(this).parents('li').get(0);
                    FreeKit.setKitItem(item);
                });
                $(".freekit-select-product").on('click', this, function (e) {
                    FreeKit.addSelectedProduct(this);
                });
                $(".freekit-reset-item-button").on('click', this, function (e) {
                    FreeKit.removeSelectedProduct(this);
                });
            },
            collectKitProducts: function () {
                this.kitCart = [];
                $.each(this.selectedProducts, function (indx, productId) {
                    var ids = productId.replace('freekit_select_product_id_', '');
                    this.kitCart.push(ids);
                }.bind(this));
            },
            addToCart: function (element) {
                if (!this.isCartValid()) {
                    return false;
                }
                // if (typeof hasFreeKitInCart !== 'undefined' && hasFreeKitInCart) {
                //     return false;
                // }
                FreeKit.collectKitProducts();
                //button.disable();
                var params = {
                    isAjax: 'true',
                    type: this.kitType,
                    selected: JSON.stringify(this.kitCart),
                    qty: this.qty
                };
                //var actionUrl = data.addToCartUrl;
                if (typeof AW_AjaxCartProObserverObjectQuiz !== 'undefined') {
                    AW_AjaxCartProObserverObjectQuiz.fireCustom(actionUrl, params);
                } else {
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
                }
                this.resetKit();
                this.hideCategoriesBox();
            },
            isCartValid: function () {
                if (Object.keys(this.selectedProducts).length != this.itemsAmount) {
                    this.showValidNoticeForItems();
                    return false;
                }
                return true;
            },
            showValidNoticeForItems: function () {
                var selectedItemsIds = Object.keys(this.selectedProducts);
                $('.freekit-item').each(function (idx, element) {
                    var id = element.id.replace(this.itemIdPrefix, '');
                    if ($.inArray(id, selectedItemsIds) < 0) {
                        $(element).find('.empty').addInlineDisplayNone();
                        $(element).find('.content').addInlineDisplayNone();
                        $(element).find('.validation-notice').removeInlineStyle();
                    }
                }.bind(this));
            },
            getProductId: function (element) {
                return element.id.replace(this.sampleProductIdPrefix, '');
            },
            addSelectedProduct: function (element) {
                var id = element.id.replace('freekit_select_product_id_', '');
                this.selectedProducts[this.currentItemId] = id;
                $('.freekit-select-product').removeClass('selected');
                $(element).addClass('selected');
                this.updateKitItemContent(this.getProductContent(element));
            },
            resetAllSelectedProduct: function () {
                this.selectedProducts = {};
            },
            resetKitCategory: function () {
                this.currentCategoryId = '';
                $('.freekit-select-products-box').removeClass('selected').hide();
            },
            $getCurrentProductSlider: function () {
                return $('#' + this.sampleBoxIdPrefix + this.currentCategoryId);
            },
            getCategoryId: function (element) {
                return element.id.replace(this.categoryIdPrefix, '');
            },
            setKitCategory: function setKitCategory(element) {
                var id = element.id.replace('freekit_category_id_', '');
                this.currentCategoryId = id;
                this.defaultCategory[this.currentItemId] = element;
                $('.freekit-category').removeClass('selected');
                $(element).addClass('selected');
                $('.freekit-select-products-box').removeClass('selected').hide();
                $('#' + this.sampleBoxIdPrefix + id).addClass('selected').show();
                FreeKit.goToPreviousSliderState();
            },
            goToPreviousSliderState: function () {
                // if (!(this.currentItemId in this.slidersPosition)) {
                //     this.slidersPosition[this.currentItemId] = {};
                // }
                // if (!(this.currentCategoryId in this.slidersPosition[this.currentItemId])) {
                //     this.slidersPosition[this.currentItemId][this.currentCategoryId] = 0;
                // }
                // var previousSliderPosition = this.slidersPosition[this.currentItemId][this.currentCategoryId];
                // var $slider = $('#' + this.sampleBoxIdPrefix + this.currentCategoryId);
                // if ($slider.length > 0) {
                //     var owlData = $slider.data('owlCarousel');
                //     owlData.goTo(previousSliderPosition);
                // }
            },
            setCurrentItem: function () {
                var $item = $('#freekit_item_id_' + this.currentItemId);
                $('.freekit-item').removeClass('selected');
                $item.addClass('selected').show();
                this.sampleBoxIdPrefix = "freekit_select_products_box_id_";
                $('#' + this.sampleBoxIdPrefix + this.currentItemId).addClass('selected').show();
            },
            setKitItem: function (element) {
                if (!element) {
                    return;
                }
                FreeKit.resetKitCategory();
                var id = element.id.replace('freekit_item_id_', '');
                this.currentItemId = id;
                FreeKit.setCurrentItem();
                FreeKit.setCategoriesBox();
            },
            setKitItemById: function (id) {
                if (!id)
                    return;
                this.resetKitCategory();
                this.currentItemId = id;
                this.setCurrentItem();
                this.setCategoriesBox();
            },
            hideCategoriesBox: function () {
                this.resetKitCategory();
                $('.freekit-categories-box').removeClass('selected').hide();
            },
            insertCategoryBox: function () {
                if ((this.currentItemId.length > 0)) {
                    //insert slider box after the right row
                    var itemIndex = this.currentItemId;
                    var rowIndex = Math.ceil(itemIndex / 3);
                    var lastItemInRowIndex = 3 * rowIndex;
                    var insertAfterItemIndex = (($('.freekit-item').length < lastItemInRowIndex) ? $('.freekit-item').length : lastItemInRowIndex) - 1;
                    var lng = $('.freekit-item').length;
                    this.itemsBoxa = $('#freekit_items_box_id' + lng);
                    this.itemsBoxa.children('.clr').remove();
                    $($('.freekit-item').get(insertAfterItemIndex))
                        .after('<div class="clr"></div>')
                        .after(FreeKit.productsConteiner)
                        .after(FreeKit.categoriesConteiner)
                        .after('<div class="clr"></div>');
                }
            },
            setCategoriesBox: function () {
                FreeKit.insertCategoryBox();
                $('.freekit-categories-box').removeClass('selected').hide();
                $('#freekit_categories_box_id_' + FreeKit.currentItemId).addClass('selected').show();
                FreeKit.setDefaultsCategory();
            },
            setDefaultsCategory: function () {
                var category;
                if (typeof this.defaultCategory[this.currentItemId] == 'undefined') {
                    category = $('#freekit_categories_box_id_' + this.currentItemId).find('.freekit-category').removeClass('selected').get(0);
                    if (!category) {
                        // if there is only one category
                        var products = $('.freekit-select-products-box.selected').get(0);
                        if (products) {
                            this.currentCategoryId = products.id.replace(this.sampleBoxIdPrefix, '');
                        }
                        return;
                    }
                    $(category).addClass('selected');
                    this.defaultCategory[this.currentItemId] = category;
                }
                this.setKitCategory(this.defaultCategory[this.currentItemId]);
            },
            updateKitItemContent: function (content) {
                var contentHtml = this.prepareProductContent(content);
                var id = '#freekit_item_id_' + this.currentItemId;
                var $element = $(id);
                if ($element.find('.content .wrap').length > 0) {
                    $element.find('.content .wrap').html(contentHtml);
                    $element.find('.empty').addInlineDisplayNone();
                    $element.find('.validation-notice').addInlineDisplayNone();
                    $element.find('.content').removeInlineStyle();
                }
            },
            updateSliderPosition: function (slider) {
                if (!(FreeKit.currentItemId in FreeKit.slidersPosition)) {
                    FreeKit.slidersPosition[FreeKit.currentItemId] = {};
                }
                FreeKit.slidersPosition[FreeKit.currentItemId][FreeKit.currentCategoryId] = slider.data('owlCarousel').currentItem;
            },
            prepareProductContent: function (content) {
                var contentHtml = content.image + content.name;
                return contentHtml;
            },
            getProductContent: function (element) {
                var content = {};
                content.name = $(element).find('.product-name').get(0).outerHTML;
                content.image = $(element).find('img').get(0).outerHTML;
                return content;

            },
            resetItemsContent: function (element) {
                if (typeof element == "undefined") {
                    element = this.items;
                }
                $(element).find('.content').addInlineDisplayNone();
                $(element).find('.content .wrap').html('');
                $(element).find('.empty').removeInlineStyle();
            },
            removeSelectedProduct: function (element) {
                var item = $(element).parents('li').get(0);
                var id = item.id.replace(this.itemIdPrefix, '');
                delete this.selectedProducts[id];
                FreeKit.resetItemsContent(item);
                FreeKit.setKitItem(item);
            },
            update: function () {
                if ($(window).width() > WINDOW_SIZE_BREAKPOINT_SMALL) {
                    this.setLarge();
                } else {
                    this.setSmall();
                }
            },
            setLarge: function () {
                if (FreeKit.selectCurrentState == 'large')
                    return true;
                this.selectCurrentState = 'large';
                this.hideCategoriesBox();
                //this.carousel.destroy();
                $(".quizkit-item").show();
                this.insertCategoryBox()
            },
            setSmall: function () {
                if (this.selectCurrentState == 'small')
                    return true;
                this.selectCurrentState = 'small';
                $('#freekit_categories_mobile').prepend(this.categoriesConteiner);
                $('#freekit_products_mobile').prepend(this.productsConteiner);
                this.itemsBox.children('.clr').remove();
                this.carousel.init();
                this.setCurrentItem();
            },
            getContent: function (option) {
                var content = $('#' + option).find('.category-name').clone();
                return content;
            },
            resetKit: function () {
                var that = this;
                $('.freekit-item').each(function (index) {
                    var id = this.id.replace(that.itemIdPrefix, '');
                    delete that.selectedProducts[id];
                });
                $('.freekit-item').removeClass('selected');
                $('.freekit-item').find('.content').addInlineDisplayNone();
                $('.freekit-item').find('.validation-notice').addInlineDisplayNone();
                $('.freekit-item').find('.content .wrap').html('');
                $('.freekit-item').find('.empty').removeInlineStyle();
                $('.freekit-select-products-box').parent('.free-coolor-samples').hide();
            }
        };
        FreeKit.funTest();
        (function ($) {
            $.fn.addInlineDisplayNone = function () {
                this.css('display', 'none');
            };
            $.fn.removeInlineStyle = function () {
                $.each(this, function (indx, element) {
                    if (typeof $(element).attr('style') != 'undefined')
                        $(element).removeAttr('style');
                });
            };
        }(jQuery));
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
                    this.$categoryItems = $element.find('.freekit-category');
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
                },
                run: function () {
                    debugger;
                }
            };
        }($));
        function carouselsGroup(carousels, kit) {
            this.carousels = carousels;
            this.init = function init() {
                $.each(this.carousels, function (idx, element) {
                    this.initElement(element);
                }.bind(this));
            };
            this.destroy = function destroy() {
                $.each(this.carousels, function (idx, element) {
                    this.destroyElement(element);
                }.bind(this));
            };
            this.initElement = function initElement(element) {
                if (typeof element.data('owlCarousel') == 'undefined') {
                    element.owlCarousel({
                        rewindNav: false,
                        navigation: true,
                        navigationText: false,
                        pagination: false,
                        autoPlay: false,
                        singleItem: true,
                        afterMove: function (element) {
                            if (!!this.slide) {
                                return;
                            }
                            if (kit.currentItemId == this.currentItem + 1)
                                return;
                            var kitItem = this.currentItem + 1;
                            kit.setKitItemById(kitItem);
                        }
                    });
                    element.data('owlCarousel').slide = true;
                    this.slideToCurrentItem(element);
                    element.data('owlCarousel').slide = false;
                }
            };
            this.destroyElement = function destroyElement(element) {
                var owl = element.data('owlCarousel');
                if (!owl)
                    return;
                if (element.hasClass('selected')) {
                    element.addClass('selected').show();
                }
                owl.destroy();
            };
            this.slideToCurrentItem = function slideToCurrentItem(element) {
                if (typeof kit.currentItemId != 'undefined') {
                    for (var step = 1; step < kit.currentItemId; step++) {
                        element.data('owlCarousel').next();
                    }
                }
            };
        }
    };
});
