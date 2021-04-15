/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define(["jquery", "imageLoadedMin", "lookbookJss"], function ($) {
    return function (data, node) {
        var LookBook = {
            data: {},
            qty: 1,
            allAssociatedProducts: '',
            addToCartUrl: '',
            currentType: '',
            currentLookId: '',
            currentLookIndex: '',
            currentCloseupId: '',
            //dayCloseUp: '',
            //nightCloseUp: '',
            lookBooks: {},
            lookBooksCount: 0,
            switchDayBtn: '',
            switchNightBtn: '',
            nextBtn: '',
            prevBtn: '',
            hotSpotBtn: '',
            hotSpotPoints: '',
            closeupItems: '',
            activProducts: {},
            productUrl: '',
            kitId: '',
            lookIndex: '',
            lookBookMarkers: function () {
                var titles = $('input[name^=daylookindex]').map(function (idx, elem) {
                    var baass = $(elem).val();
                    this.lookIndex = baass;
                    this.look = $('#lookbook_' + baass);
                    this.lookImg = $('#lookImg_' + baass);
                    var that = this;
                    this.lookImg.imagesLoaded().done(function () {
                        that.repositionElements();
                        $(window).resize(function () {
                            that.repositionElements();
                        });
                        that.repositionElements();
                        $(document).on('updateLook', function () {
                            that.repositionElements();
                        });
                    });
                    this.repositionElements = function () {
                        if (!this.look.hasClass('selected'))
                            return false;
                        var that = this;
                        this.getGroupPluses().each(function () {
                            var $el = $(this),
                                $img = that.lookImg;
                            var productId = $el.attr('data-productid');
                            newX = that.getPositionX(productId) * $img.width() - ($el.width() / 2);
                            newY = that.getPositionY(productId) * $img.height() - ($el.height() / 2);
                            $el.css({left: newX + 'px', top: newY + 'px'});
                        });
                    };
                    this.getGroupPluses = function () {
                        return this.look.find('.hot-spot-points');
                    };
                    this.getPositionX = function (productId) {
                        return this.getPointById(productId).attr('data-pos-x');
                    };
                    this.getPositionY = function (productId) {
                        return this.getPointById(productId).attr('data-pos-y');
                    };
                    this.getPointById = function (productId) {
                        return $('#hotspot_point_' + this.lookIndex + '_' + productId)
                    };
                }).get();
            },
            sktData: function () {
                this.productUrl = data.productUrl;
                this.addToCartUrl = data.addToCartUrl;
                this.allAssociatedProducts = JSON.parse(data.allAssociatedProducts);
                var that = this;
                this.lookBooks = $('.choose-your-look-content');
                this.switchDayBtn = $('.day-night-switch').find('.day-look');
                this.switchNightBtn = $('.day-night-switch').find('.night-look');
                this.nextBtn = $('.look-skip-next');
                this.prevBtn = $('.look-skip-previous');
                this.hotSpotBtn = $('.hot-spot-btn');
                this.hotSpotPoints = $('.hot-spot-points');
                this.closeupItems = $('.closeup-item');
                this.lookImg = $('.look-image');
                this.closeupImg = $('.closeup-image');
                this.showAllBtn = $('.show-all-products-btn');
                this.thisKitBtn = $('.buy-this-look-btn');
                this.kitId = this.thisKitBtn.attr('data-product-id');
                this.lookBooksCount = this.lookBooks.length;
                $('.closeup-item').on('click', function () {
                    var $closeup = $(this);
                    //persist current selected close up id
                    //that[that.currentType + 'CloseUp'] = $closeup.attr('id');
                    that._checkActions(false);
                    that.closeupItems.removeClass('selected');
                    if (!$closeup.hasClass('selected'))
                        $closeup.addClass('selected');
                    that.unsetAllProducts();
                    if (/main_img/.test(this.id)) {
                        that.closeupImg.hide();
                        that.lookImg.show();
                        return false;
                    } else {
                        that.lookImg.hide();
                        that.closeupImg.show();
                    }
                    that.closeupImg.empty();
                    var imgSrc = $closeup.find('.closeup-thumbnail').css('background-image').replace(/^url\(['"]?(.+)['"]?\)/, '$1');
                    that.closeupImg.append('<img src="' + imgSrc + '" />');
                    $closeup.find('.closeup-data').each(function () {
                        var productId = $(this).attr('data-productid');
                        that.updateProduct(productId);
                    });
                });
                $('.show-all-products-btn').on('click', function () {
                    that._checkActions('showAllBtn');
                    if (that.showAllBtn.hasClass('active')) {
                        that._showAllBtnHideAction();
                    } else {
                        that._showAllBtnShowAction();
                    }
                });
                $('.buy-this-look-btn').on('click', function () {
                    that._checkActions('thisKitBtn');
                    if (that.thisKitBtn.hasClass('active')) {
                        that._thisKitBtnHideAction();
                    } else {
                        that._thisKitBtnShowAction();
                    }
                });
                $('.hot-spot-points').on('click', function () {
                    var $point = $(this),
                        productId = $point.attr('data-productid');
                    that._checkActions('hotSpotPoints');
                    that.hotSpotPoints.removeClass('selected');
                    if (!$point.hasClass('selected'))
                        $point.addClass('selected');
                    that.unsetAllProducts();
                    that.updateProduct(productId);
                });
                $('.day-night-switch').on('click', '.day-look', function () {
                    that._checkActions(false);
                    if ($(this).hasClass('selected') == false) {
                        that.setCurrentType('day');
                        that.updateLook();
                        that.unsetAllProducts();
                    }
                });
                $('.day-night-switch').on('click', '.night-look', function () {
                    that._checkActions(false);
                    if ($(this).hasClass('selected') == false) {
                        that.setCurrentType('night');
                        that.updateLook();
                        that.unsetAllProducts();
                    }
                });
                $('.hot-spot-btn').on('click', function () {
                    var $el = that.hotSpotBtn;
                    if ($el.hasClass('active')) {
                        $el.removeClass('active');
                        $el.text('Hide hot spots');
                        that.showHotSpotPoints();
                    } else {
                        $el.addClass('active');
                        $el.text('Show hot spots');
                        that.hideHotSpotPoints();
                    }
                });
                $(document).on('loadProduct', function (e) {
                    console.log('Load Product form ID' + e.target.id);
                    that.setActivProducts();
                });
                $(document).on('updateLook', function (e) {
                    // find close up of the lookbook
                    var $lookbookCloseup = $(e.target).find(".lookbook");
                    if ($lookbookCloseup.length) {
                        $lookbookCloseup.click();
                    }
                });
            },
            addToCart: function (button) {
                //button.disable();
                var params = {
                    isAjax: 'true',
                    product: this.currentLookId,
                    qty: this.qty
                };
                var actionUrl = this.addToCartUrl;
                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: params,
                    dataType: "json",
                    success: function (data) {
                        if (data.redirect) {
                            window.location.replace(data.redirect);
                        }
                    }
                });
            },
            unsetAllProducts: function () {
                $('.makeupconter-product-info').removeClass('selected').hide();
                for (var i in this.activProducts) {
                    if (this.activProducts.hasOwnProperty(i) && this.activProducts[i] != 'undefined') {
                        this.activProducts[i] = false;
                    }
                }
            },
            updateProduct: function (productId) {
                if (!this.isProductValid(productId))
                    return false;
                if (typeof this.activProducts[productId] == 'undefined') {
                    //this.loadProducts(productId);
                }
                this.activProducts[productId] = true;
                this.setActivProducts();
            },
            setActivProducts: function () {
                for (var id in this.activProducts) {
                    if (this.activProducts[id] === true) {
                        $('#product_addtocart_form_' + id).addClass('selected').show();
                    }
                }
            },
            isProductValid: function (productId) {
                return typeof productId != 'undefined' && /^\d+$/.test(productId) != false;
            },
            showHotSpotPoints: function () {
                this.hotSpotPoints.show();
            },
            hideHotSpotPoints: function () {
                this.hotSpotPoints.hide();
            },
            updateLook: function () {
                var that = this;
                $('.choose-your-look-content').map(function (index) {
                    var $el = $(this),
                        id = new RegExp(that.currentType + that.currentLookId);
                    if (id.test(this.id)) {
                        that.currentLookIndex = index;
                        $el.show();
                        if (!$el.hasClass('selected'))
                            $el.addClass('selected');
                    } else {
                        $el.hide();
                        if ($el.hasClass('selected'))
                            $el.removeClass('selected');
                    }
                });
                $($('.choose-your-look-content')[that.currentLookIndex]).trigger('updateLook');
            },
            setCurrentType: function (type) {
                this.currentType = type;
                switch (type) {
                    case 'night':
                        if ($('.day-night-switch').find('.day-look').hasClass('selected'))
                            $('.day-night-switch').find('.day-look').removeClass('selected');
                        if (!$('.day-night-switch').find('.night-look').hasClass('selected'))
                            $('.day-night-switch').find('.night-look').addClass('selected');
                        break;
                    case 'day':
                        if ($('.day-night-switch').find('.night-look').hasClass('selected'))
                            $('.day-night-switch').find('.night-look').removeClass('selected');
                        if (!$('.day-night-switch').find('.day-look').hasClass('selected'))
                            $('.day-night-switch').find('.day-look').addClass('selected');
                        break;
                }
            },
            setCurrentLookId: function (id) {
                this.currentLookId = id;
            },
            setCurrentCloseupId: function (id) {
                this.currentCloseupId = id;
            },
            setDefault: function () {
                this.setCurrentType(data.type);
                this.setCurrentLookId(data.lookId);
                this.updateLook();
            },
            _checkActions: function (skip) {
                if ('showAllBtn' !== skip && this.showAllBtn.hasClass('active')) {
                    this._showAllBtnHideAction();
                }
                if ('thisKitBtn' !== skip && this.thisKitBtn.hasClass('active')) {
                    this._thisKitBtnHideAction();
                }
                if ('hotSpotPoints' !== skip && this.hotSpotPoints.hasClass('selected')) {
                    this._hotSpotPointsHideAction();
                }
            },
            _showAllBtnShowAction: function () {
                var that = this;
                this.showAllBtn.addClass('active').text('Hide all products in look');
                $.each(that.allAssociatedProducts, function (index, productId) {
                    that.updateProduct(productId);
                });
            },
            _showAllBtnHideAction: function () {
                this.showAllBtn.removeClass('active').text('Show all products in look');
                this.unsetAllProducts();
            },
            _thisKitBtnShowAction: function () {
                this.thisKitBtn.addClass('active').html('Hide this kit');
                this.unsetAllProducts();
                this.updateProduct(this.kitId);
            },
            _thisKitBtnHideAction: function () {
                this.thisKitBtn.removeClass('active').html('Show this kit');
                this.unsetAllProducts();
            },
            _hotSpotPointsHideAction: function () {
                this.hotSpotPoints.removeClass('selected');
            }
        };
        LookBook.sktData();
        LookBook.lookBookMarkers();
        LookBook.setDefault();
    };
});
