/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define(["jquery"]), function ($) {
    "use strict";
    return function (data, node) {
        $(document).ready(function () {
            LookBookAdminCloseUps.form = $('#product_edit_form');
            $('.cell-image').on('click', function () {
                LookBookAdminCloseUps.openCloseupsPopup(this);
            });
        });
        var LookBookAdminCloseUps = {
            closeUpsData: [],
            loading: 0,
            form: '',
            editUrl: '',
            popupPosition: {},
            showedId: false,
            submit: function (el) {
                if (this.loading) {
                    return false;
                }
                var id = el.id.replace('closeup_save_', '');
                var form = $j('#closeup_' + id);
                var formParams = form.serializeArray();
                var actionUrl = form.attr('action');
                this.loading = true;
                var that = this;
                $j('#loading-mask').show();
                $j.post(
                    actionUrl,
                    formParams,
                    function (data) {
                        $j('#loading-mask').hide();
                        alert(data);
                        that.loading = false;
                        that.cancel();
                    }
                ).fail(function () {
                    $j('#loading-mask').hide();
                    alert('Something went wrong. Please, reload this page and try again.');
                });
            },
            cancel: function () {
                this.showedId = false;
                $('.closeups-popup').remove();
            },
            getFormKey: function () {
                return this.form.find('input[name=form_key]').val();
            },
            getPopupPosition: function (el) {
                var element = $(el);
                this.popupPosition = element.offset();
                this.popupPosition.left += element.width();

            },
            setPopUpPosition: function () {
                $('.closeups-popup').offset(this.popupPosition);
            },
            openCloseupsPopup: function (el) {

                var uniqueImgPath = this.getImagePath(el);
                if (this.showedId && this.showedId == uniqueImgPath) {
                    return false;
                }
                this.showedId = uniqueImgPath;

                this.getPopupPosition(el);
                var image = this.getFileFromPreview(el),
                    imageId = this.getImageIdByFile(image),
                    productId = this.getProductIdByFile(image),
                    formKey = this.getFormKey(),
                    actionUrl = this.editUrl,
                    that = this;

                if (!image) {
                    return false;
                }

                var params = {
                    product_id: productId,
                    image_id: imageId,
                    form_key: formKey
                };
                $j('#loading-mask').show();
                $j.post(
                    actionUrl,
                    params,
                    function (data) {
                        $j('#loading-mask').hide();
                        $j('#product_edit_form').after(data);
                        that.setPopUpPosition();
                        that.loading = false;
                    }
                ).fail(function () {
                    $j('#loading-mask').hide();
                    alert('Something went wrong. Please, reload this page and try again.');
                });
            },
            getFileFromPreview: function (el) {
                var url = $(el).find('img').attr('src');
                if (typeof url == 'undefined')
                    return false;
                return url.substring(url.lastIndexOf('product') + 7);
            },
            getImageIdByFile: function (file) {
                if (!file)
                    return false;
                return media_gallery_contentJsObject.getImageByFile(file).value_id;
            },
            getProductIdByFile: function (file) {
                if (!file)
                    return false;
                return media_gallery_contentJsObject.getImageByFile(file).product_id;
            },
            getAssociatedProducts: function () {
                var productsCanvas = this.form.find('#daygroup-image').find('.group-canvas');
                var products = productsCanvas.map(function () {
                    return $(this).attr('data-product-id');
                });
                return products.get();
            },
            getImagePath: function (el) {
                var uniqueImg = $(el).find("img");
                if (uniqueImg.length) {
                    return uniqueImg[0].src;
                }

                return false;
            }
        };

        function LookBookAdmin(type) {
            this.canvasInitFlag = false;
            this.classType = type;
            this.initFlow = function () {
                this.hideAllPluses();
                this.addOnClickImagesEvent();
                this.initDraggable();
                this.repositionElements();
                this.initCanvas();
            };
            this.getMapInputs = function () {
                return $('.map_input.' + this.classType);
            };
            this.getGroupImg = function () {
                return $('#' + this.classType + 'group-image');
            };
            this.getCanvasInputById = function (id) {
                return $('#' + this.classType + 'canvas_' + id + ' input');
            };
            this.getCanvasById = function (id) {
                return $('#' + this.classType + 'canvas_' + id);
            };
            this.getPlusById = function (id) {
                return $('#' + this.classType + 'plus_' + id);
            };
            this.getGroupPluses = function () {
                return $('.group-plus.' + this.classType);
            };
            this.getGroupCanvases = function () {
                return $('.group-canvas.' + this.classType);
            };
            this.getPositionX = function (id) {
                return $('#' + this.classType + 'positionX_' + id);
            };
            this.getPositionY = function (id) {
                return $('#' + this.classType + 'positionY_' + id);
            };
            this.getGroupProdImages = function () {
                return $('.group-prod-image.' + this.classType);
            };
            this.hideAllPluses = function () {
                this.getGroupPluses().each(function (i, m) {
                    m.hide();
                });
            };
            this.initDraggable = function () {
                var that = this;
                var containment = '#' + this.classType + 'group-image';
                this.getGroupPluses().draggable({
                    containment: containment,
                    scroll: false,
                    stop: function () {
                        tmp_el = $(this);
                        currentY = ($(this).position().top + $(this).height() / 2) / that.getGroupImg().height();
                        currentX = ($(this).position().left + $(this).width() / 2) / that.getGroupImg().width();
                        var id = tmp_el.attr('data-productid');
                        that.getPositionX(id).attr('value', currentX);
                        that.getPositionY(id).attr('value', currentY);
                    }
                }).addClass('ui-widget-content');
            };
            this.initCanvas = function () {
                var that = this;
                this.getGroupImg().find('canvas').each(function () {
                    id = $(this).attr('data-product-id');
                    var mapId = '#' + that.classType + 'map_' + id;
                    $(this).canvasArea({'input_selector': mapId});
                });
            };
            this.addOnClickImagesEvent = function () {
                var that = this;
                this.getGroupProdImages().each(function (i, el) {
                    el.on('click', function (k) {
                        that.canvasInit();
                        that.getGroupProdImages().each(function (i, m) {
                            m.removeClassName('selected');
                        });
                        that.getGroupPluses().each(function (i, m) {
                            m.hide();
                        });
                        var id = $(el).attr('data-productid');
                        that.getPlusById(id).get(0).show();
                        that.getGroupCanvases().hide();
                        var image = that.getGroupImg();
                        that.getCanvasById(id).find('canvas').attr('width', image.width()).attr('height', image.height());
                        that.getCanvasById(id).show();
                        el.addClassName('selected');
                        that.repositionElements();
                    });
                });
            };
            this.convertToPixels = function () {
                var that = this;
                this.getMapInputs().each(function () {
                    id = $(this).attr('data-productid');
                    tmp = $(this).val();
                    percents_array = tmp.split(',');
                    new_array = [];
                    for (var i = 0; i < percents_array.length; i++) {
                        new_array.push(parseInt(percents_array[i] * that.getGroupImg().find('img').width()));
                        i++;
                        new_array.push(parseInt(percents_array[i] * that.getGroupImg().find('img').height()));
                    }
                    that.getCanvasInputById(id).attr('value', new_array.join());
                });

            };
            this.canvasInit = function () {
                this.convertToPixels();
                if (!this.canvasInitFlag) {
                    this.canvasInitFlag = true;
                }
            };
            this.repositionElements = function () {
                var that = this;
                this.getGroupPluses().each(function () {
                    id = $(this).attr('data-productid');
                    newX = that.getPositionX(id).attr('value') * that.getGroupImg().width() - ($(this).width() / 2);
                    newY = that.getPositionY(id).attr('value') * that.getGroupImg().height() - ($(this).height() / 2);
                    $(this).css({left: newX + 'px', top: newY + 'px'});
                });
            };
        }
    };
};
