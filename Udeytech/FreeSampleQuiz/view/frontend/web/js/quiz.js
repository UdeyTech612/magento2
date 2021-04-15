/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define(["jquery", "quiz", 'jquery/jquery.cookie', 'mage/url'], function ($, quiz, jqueryCookie, url) {
    "use strict";
    return function (data, node) {
        var WINDOW_SIZE_BREAKPOINT_SMALL = 769;
        var WINDOW_SIZE_BREAKPOINT_FOR_FORMULA_SMALL = 920;
        $(document).ready(function () {
            $(window).resize(function () {
                FormulaSelect.update();
                QuizKit.update();
                QuizBrush.update();
            });
            $(document).on("click", ".quiz-start-btn", function () {
                Quiz.nextStep();
            });
            $(document).on("click", ".quiz-answers", this, function () {
                Quiz.submitQuestion(this);
            });
            $(document).on("click", ".formula-btn", this, function () {
                Quiz.nextStep(FormulaSelect.currentOption);
            });
            $(document).on("click", ".final-btn", this, function () {
                QuizKit.submit();
                //$("#quiz_step_color").hide();
                // $("#quiz_step_basket").show();
            });
            $(document).on("click", ".again-btn", this, function () {
                Quiz.resetQuiz();
            });
            $("#formula_select").on('click', '.item', this, function (e) {
                FormulaSelect.setCurrentCategory(this);
            });
            $("#quiz_add_to_cart").on("click", this, function () {
                QuizKit.addToCart(this);
            });
            var FormulaSelect = {
                select: {},
                options: {},
                selectCurrentState: '',
                currentOption: '',
                button: {},
                contentBox: {},
                contentBoxHtml: '',
                productsContentBox: {},
                products: {},
                firstProduct: {},
                lastProduct: {},
                firstProductContent: {},
                lastProductContent: {},
                firstCode: '',
                lastCode: '',
                setCurrentCategory: function (item) {
                    var $item = $(item);
                    $('#formula_select').find('.item').removeClass('selected');
                    $item.addClass('selected');
                    this.currentOption = $item.attr('data-formula');
                    $('#formula_btn').show();
                    if (this.selectCurrentState == 'small') {
                        //this.contentBox.html(this.getContent(this.currentOption)).show();
                        $("#formula_small_content_box").html(this.getContent(this.currentOption)).show();
                    }
                    this.setCurrentProducts();
                },
                resetCategory: function () {
                    $('#formula_select').find('.item').removeClass('selected');
                    this.setEmptyProducts();
                    this.currentOption = '';
                    this.removeQuizKitProducts();
                    $('#formula_btn').hide();
                },
                setEmptyProducts: function () {
                    $('#formula_btn').hide();
                    $('#formula_select').find('.item').removeClass('selected');
                    $('#formula_product_first').find('.empty').show();
                    $('#formula_product_first').find('.content').html('').hide();
                    $('#formula_product_last').find('.empty').show();
                    $('#formula_product_last').find('.content').html('').hide();
                },
                updateProducts: function () {
                    if (!this.firstProduct || !this.lastProduct) {
                        this.setEmptyProducts();
                        return;
                    }
                    $('#formula_product_first').find('.empty').hide();
                    $('#formula_product_first').find('.content').html($('#formula_product_first').html()).show();
                    $('#formula_product_last').find('.empty').hide();
                    $('#formula_product_last').find('.content').html($('#formula_product_first').html()).show();
                    this.setQuizKitProducts();
                },
                testCategoriesForEnableProducts: function () {
                    if (!this.firstProduct || !this.lastProduct) {
                        this.disabledCategory(categoryId);
                    }
                },
                setCurrentProducts: function () {
                    this.removeQuizKitProducts();
                    this.firstProduct = false;
                    this.lastProduct = false;
                    $('.quizkit-formula-product').each(function (idx, element) {
                        var $product = $(element);
                        var firstCode = RegExp(this.firstCode);
                        var lastCode = RegExp(this.lastCode);
                        var currentCategory = RegExp(this.currentOption);
                        var productName = $product.find('.bases-name').text();
                        var productCategory = $product.attr('data-categories');
                        if (currentCategory.test(productCategory)) {
                            if (firstCode.test(productName))
                                this.firstProduct = $product;
                            if (lastCode.test(productName))
                                this.lastProduct = $product;
                        }
                    }.bind(this));
                    this.updateProducts();
                },
                removeQuizKitProducts: function () {
                    if (typeof QuizKit.selectedProducts['firstBases'] != 'undefined')
                        delete QuizKit.selectedProducts['firstBases'];
                    if (typeof QuizKit.selectedProducts['lastBases'] != 'undefined')
                        delete QuizKit.selectedProducts['lastBases'];
                },
                setQuizKitProducts: function () {
                    QuizKit.selectedProducts['firstBases'] = this.getProductId(this.firstProduct);
                    QuizKit.selectedProducts['lastBases'] = this.getProductId(this.lastProduct);
                },
                getProductId: function (product) {
                    if (Object.keys(product).length)
                        return product.attr('id').replace(this.productIdPrefix, '');
                    return null;
                },
                setAssociatedCodes: function (codes) {
                    if (codes[0] != this.firstCode || codes[1] != this.lastCode)
                        this.setEmptyProducts();
                    this.firstCode = codes[0];
                    this.lastCode = codes[1];
                    $('#formula_product_first').find('.bases-code').text(this.firstCode);
                    $('#formula_product_last').find('.bases-code').text(this.lastCode);
                },
                setSmall: function () {
                    var that = this;
                    if (this.selectCurrentState == 'small')
                        return true;
                    this.select.find('.item').each(function () {
                        var $item = $(this);
                        $item.find('.content').html('');
                    });
                    if (this.currentOption != '') {
                        $("#formula_small_content_box").html(this.getContent(this.currentOption)).show();
                        this.button.show();
                    }
                    this.selectCurrentState = 'small';
                },
                getContent: function (option) {
                    var id = 'formula_option_' + option;
                    var content = $('#' + id).find('.content').html();
                    return content;
                },
                setLarge: function () {
                    if (this.selectCurrentState == 'large')
                        return true;
                    var that = this;
                    $("#formula_small_content_box").hide();
                    $('#formula_select').find('.item').each(function () {
                        var $item = $(this);
                        $item.find('.content').html(that.getContent($item.attr('data-formula')));
                    });
                    this.selectCurrentState = 'large';
                },
                update: function () {
                    if ($(window).width() > WINDOW_SIZE_BREAKPOINT_FOR_FORMULA_SMALL) {
                        this.setLarge();
                    } else {
                        this.setSmall();
                    }
                },
                init: function () {
                    this.select = data.select;
                    this.options = data.options;
                    this.button = data.button;
                    this.contentBox = data.content;
                    this.productsContentBox = data.productsContentBox;
                    this.productIdPrefix = data.productIdPrefix;
                    this.products = data.products;
                    this.firstProductContent = data.firstProduct;
                    this.lastProductContent = data.lastProduct;
                    this.update();
                }
            };
            var Quiz = {
                steps: {},
                stepFormula: false,
                stepColor: false,
                stepBasket: false,
                paginator: {},
                stepsCount: false,
                currentStep: false,
                answers: {},
                answersArray: [],
                associatedCodes: {},
                testCodeResult: {},
                kitAlreadyInCart: false,
                submitQuestion: function (element) {
                    var answersCode = $(element).attr('data-id');
                    var questionId = '';
                    // var questionId = this.steps[this.currentStep].attr('id').replace('quiz_step_question_', '');
                    if (this.currentStep === 1) {
                        questionId = 1;
                    }
                    if (this.currentStep === 2) {
                        questionId = 2;
                    }
                    if (this.currentStep === 3) {
                        questionId = 3;
                    }
                    if (this.currentStep === 4) {
                        questionId = 4;
                        this.setFormula();
                    }
                    this.answers[questionId] = answersCode;
                    //var nextStepId = this.steps[this.currentStep + 1].attr('id');
// if (nextStepId == 'quiz_step_formula') {
// this.setFormula();
// }
                    this.nextStep();
                },
                setFormula: function () {
                    this.updateAnswersArray();
                    this.testCodeResult = $.extend(true, {}, this.associatedCodes);
                    $.each(this.answersArray, function (idx, attr) {
                        this.filterAssociatedCodesByAttr(attr);
                    }.bind(this));
                    var codes = Object.keys(this.testCodeResult);
                    FormulaSelect.setAssociatedCodes(codes);
                },
                updateAnswersArray: function () {
                    this.answersArray = [];
                    $.each(this.answers, function (indx, element) {
                        if (element != "") {
                            var attributes = element.split(',');
                            this.answersArray = this.answersArray.concat(attributes);
                        }
                    }.bind(this));
                },
                filterAssociatedCodesByAttr: function (attr) {
                    var that = this;
                    that.testAttribute = attr;
                    $.each(this.testCodeResult, function (code, element) {
                        if (element[that.testAttribute] == false)
                            delete that.testCodeResult[code];
                    }.bind(that));
                },
                init: function () {
                    var that = this;
                    this.associatedCodes = data.associatedCodes;
                    this.steps = data.steps;
                    $.each(this.steps, function (idx, element) {
                        var elmId = element.replace(/[^a-zA-Z-_ ]/g, "");
                        switch (elmId) {
                            case 'quiz_step_formula':
                                this.stepFormula = idx;
                                break;
                            case 'quiz_step_color':
                                this.stepColor = idx;
                                break;
                            case 'quiz_step_basket':
                                this.stepBasket = idx;
                                break;
                        }
                    }.bind(this));
                    this.paginator = data.paginator;
                    this.stepsCount = Object.keys(this.steps).length;
                    $("#quiz_paginator").find('.prev-btn').on('click', function () {
                        that.prevStep();
                    });
                    var params = {
                        isAjax: 'true'
                    };
                    var actionUrl = data.isFreeKitDuplicateCheckUrl;
                    $.ajax({
                        type: "POST",
                        url: actionUrl,
                        data: params,
                        dataType: "json",
                        success: function (data) {
                            if ('isfreekitduplicate' in data) {
                                this.kitAlreadyInCart = data.isfreekitduplicate;
                            }
                            this.setDefault();
                        }.bind(this)
                        // todo: think about error handling
                    });
                },
                setDefault: function () {
                    if (!this.kitAlreadyInCart) {
                        QuizCookies.get();
                        this.currentStep = QuizCookies.getCurrentStep();
                        this.answers = QuizCookies.getAnswers();
                        QuizKit.selectedProducts = QuizCookies.getSelectedProducts();
                        QuizBrush.selectedBrush = QuizCookies.getSelectedBrash();
                        if (this.currentStep <= this.stepColor) {
                            QuizKit.resetColorProduct();
                        }
                        this.setFormula();
                    } else {
                        this.currentStep = this.stepsCount;
                        QuizCookies.reset();
                    }
                    this.setCurrentStep();
                },
                resetQuiz: function () {
                    QuizCookies.reset();
                    if (typeof (QuizKit) !== 'undefined') {
                        QuizKit.resetKitCategory();
                        QuizKit.resetKit();
                    }

                    if (typeof (FormulaSelect) !== 'undefined') {
                        FormulaSelect.resetCategory();
                    }

                    if (typeof (QuizBrush) !== 'undefined') {
                        QuizBrush.resetBrush();
                    }

                    this.currentStep = 1;
                    this.setCurrentStep();
                },
                updatePaginator: function () {
                    this.paginator = data.paginator;
                    $("#quiz_paginator").addClass('step-' + this.currentStep);
                    if (this.currentStep == 0 || this.currentStep == this.stepsCount) {
                        $("#quiz_paginator").find('.points').empty();
                        $("#quiz_paginator").hide();
                        return;
                    }
                    var paginatorPointElements = '';
                    for (var i = 1; i <= this.stepsCount - 2; i++) {
                        if (i <= this.currentStep) {
                            paginatorPointElements += '<li class="point selected"></li>';
                        } else {
                            paginatorPointElements += '<li class="point"></li>';
                        }
                    }
                    $("#quiz_paginator").find('.points').html(paginatorPointElements);
                    $("#quiz_paginator").show();
                },
                setCurrentStep: function () {
                    var that = this;
                    this.updatePaginator();
                    var obj = this.steps;
                    //var result = Object.keys(obj).map((key) => [Number(key), obj[key]]);
                    var result = Object.keys(obj).map((key) => [obj[key]]);
                    if (that.currentStep == 0) {
                        $("#quiz_step_question_1").hide();
                        $("#quiz_step_start").show();
                    }
                    if (that.currentStep == 1) {
                        $("#quiz_step_question_1").show();
                        $("#quiz_step_start").hide();
                    } else {
                        $("#quiz_step_question_1").hide();
                    }
                    if (that.currentStep == 2) {
                        $("#quiz_step_question_2").show();
                    } else {
                        $("#quiz_step_question_2").hide();
                    }
                    if (that.currentStep == 3) {
                        $("#quiz_step_question_3").show();
                    } else {
                        $("#quiz_step_question_3").hide();
                    }
                    if (that.currentStep == 4) {
                        $("#quiz_step_question_4").show();
                    } else {
                        $("#quiz_step_question_4").hide();
                    }
                    if (that.currentStep == 5) {
                        $("#quiz_step_formula").show();
                    } else {
                        $("#quiz_step_formula").hide();
                    }
                    if (that.currentStep == 6) {
                        $("#quiz_step_color").show();
                    } else {
                        $("#quiz_step_color").hide();
                    }
                    if (that.currentStep == 7) {
                        $("#quiz_step_basket").show();
                    } else {
                        $("#quiz_step_basket").hide();
                    }
                    if (that.currentStep == 8) {
                        $("#quiz_step_finish").show();
                    } else {
                        $("#quiz_step_finish").hide();
                    }
                    // $.each(result, function (key, step) {
                    //    if (key != that.currentStep) {
                    //       step.hide();
                    //      } else {
                    //       step.show();
                    //     }
                    //  });
                    if (this.currentStep == 1) {
                        QuizKit.resetColorProduct();
                    }
                    if (this.currentStep <= this.stepBasket) {
                        QuizBrush.selectedBrush = '';
                    }
                    QuizCookies.set();
                },
                nextStep: function () {
                    this.currentStep++;
                    if (this.currentStep > this.stepsCount) {
                        this.currentStep--;
                        return;
                    }
                    $('body').scrollTop(0);
                    this.restoreState();
                    this.setCurrentStep();
                },
                restoreState: function () {
                    // var stepId = this.steps[this.currentStep].attr('id');
                    if (this.currentStep == 1) {
                        var questionId = this.currentStep;
                        delete this.answers[questionId];
                    }
                    if (this.currentStep == 2) {
                        var questionId = this.currentStep;
                        delete this.answers[questionId];
                    }
                    if (this.currentStep == 3) {
                        var questionId = this.currentStep;
                        delete this.answers[questionId];
                    }
                    if (this.currentStep == 4) {
                        var questionId = this.currentStep;
                        delete this.answers[questionId];
                    }
                    if (this.currentStep === '5' && typeof (FormulaSelect) !== 'undefined') {
                        FormulaSelect.resetCategory();
                    }
                    if (this.currentStep === '6' && typeof (QuizKit) !== 'undefined') {
                        QuizKit.resetKit();
                    }
                    if (this.currentStep === '7' && typeof (QuizBrush) !== 'undefined') {
                        QuizBrush.resetBrush();
                    }
                },
                prevStep: function () {
                    this.currentStep--;
                    if (this.currentStep < 1) {
                        $("#quiz_step_start").show();
                        $("#quiz_step_question_1").hide();
                        // this.currentStep++;
                        return;
                    }
                    this.restoreState();
                    this.setCurrentStep();
                }
            };
            var QuizCookies = {
                data: {},
                reset: function () {
                    this.data = {};
                    $.cookie('Quiz', null);
                },
                set: function () {
                    this.data.currentStep = Quiz.currentStep;
                    this.data.answers = Quiz.answers;
                    if (Quiz.currentStep >= Quiz.stepColor) {
                        this.data.selectedProducts = QuizKit.selectedProducts;
                    } else {
                        this.data.selectedProducts = {};
                    }
                    if (Quiz.currentStep >= Quiz.stepBasket) {
                        this.data.selectedBrush = QuizBrush.selectedBrush;
                    } else {
                        this.data.selectedBrush = {};
                    }
                    $.cookie('Quiz', JSON.stringify(this.data));
                },
                get: function () {
                    var cookies = $.cookie('Quiz');
                    if (typeof cookies == 'undefined' || cookies == null)
                        return false;
                    cookies = JSON.parse(cookies);
                    if (typeof cookies == 'undefined' || cookies == null)
                        return false;
                    if (!Object.keys(cookies).length)
                        return false;
                    this.data = cookies;
                },
                getCurrentStep: function () {
                    if (typeof this.data.currentStep != 'undefined' && this.data.currentStep != null) {
                        if (this.data.currentStep != Quiz.stepsCount) {
                            return this.data.currentStep;
                        }
                    }
                    return 1;
                },
                getAnswers: function () {
                    if (typeof this.data.answers != 'undefined' && this.data.answers != null) {
                        return this.data.answers;
                    }

                    return {};
                },
                getSelectedProducts: function () {
                    if (typeof this.data.selectedProducts != 'undefined' && this.data.selectedProducts != null) {
                        return this.data.selectedProducts;
                    }

                    return {};
                },
                getSelectedBrash: function () {
                    if (typeof this.data.selectedBrush != 'undefined' && this.data.selectedBrush != null) {
                        return this.data.selectedBrush;
                    }

                    return false;
                }
            };
            var QuizKit = {
                addToCartUrl: '',
                qty: 1,
                kitType: 'free',
                items: [],
                itemIdPrefix: '',
                samplesBox: [],
                sampleBoxIdPrefix: '',
                products: [],
                removeBtn: [],
                itemsAmount: 5,
                currentItemId: '',
                currentCategoryId: '',
                selectedProducts: {},
                sampleProducts: [],
                sampleProductIdPrefix: '',
                selectCurrentState: '',
                tabsBox: {},
                kitCart: [],
                addToCartItem: {},
                gotoMakeupcounterItem: {},
                related_product: '',
                collectKitProducts: function () {
                    this.kitCart = [];
                    $.each(this.selectedProducts, function (indx, productId) {
                        var ids = productId.replace('freekit_select_product_id_', '');
                        this.kitCart.push(ids);
                    }.bind(this));
                    //this.related_product = QuizBrush.selectedBrush;
                },
                submit: function (){
                    // if (!this.isCartValid()) {
                    //     return false;
                    // }
                    Quiz.nextStep();
                },
                addToCart: function (button) {
                    this.collectKitProducts();
                    var params = {
                        isAjax: 'true',
                        type: this.kitType,
                        selected: JSON.stringify(this.kitCart),
                        related_product: this.related_product,
                        qty: this.qty
                    };
                    var actionUrl = url.build("freesamplepage/ajaxpostb/ajpostb");
                    $.ajax({
                        type: "POST",
                        url: actionUrl,
                        data: params,
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                Quiz.nextStep();
                                location.reload();
                            }
                        }.bind(this)
                    });
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
                    this.items.each(function (idx, element) {
                        var id = element.id.replace(this.itemIdPrefix, '');
                        if ($.inArray(id, selectedItemsIds) < 0) {
                            $(element).find('.empty').addInlineDisplayNone();
                            $(element).find('.content').addInlineDisplayNone();
                            $(element).find('.validation-notice').removeInlineStyle();
                        }
                    }.bind(this));
                },
                addSelectedProduct: function (element) {
                    var id = element.id.replace(this.sampleProductIdPrefix, '');
                    this.selectedProducts[this.currentItemId] = id;
                    $('.quizkit-select-product').removeClass('selected');
                    $(element).addClass('selected');
                    this.updateKitItemContent(this.getProductContent(element));
                },
                resetAllSelectedProduct: function () {
                    this.selectedProducts = {};
                    this.related_product = '';
                },
                resetColorProduct: function () {
                    $.each(this.selectedProducts, function (idx, element) {
                        if (idx != 'firstBases' && idx != 'lastBases')
                            delete this.selectedProducts[idx];
                    }.bind(this));
                },
                resetKitCategory: function () {
                    this.currentCategoryId = '';
                    $('.quizkit-select-products-box').removeClass('selected').hide();
                    $('.quizkit-select-products-box').parent('.free-coolor-samples').show();
                },
                setCurrentItem: function () {
                    var $item = $('#quizkit_item_id_' + this.currentItemId);
                    $('.quizkit-item').removeClass('selected');
                    $item.addClass('selected').show();
                    if (this.selectCurrentState == 'large')
                    //$item.find('.validation-notice').addInlineDisplayNone();
                    //$item.find('.empty').removeInlineStyle();
                        $('#quizkit_select_products_box_id_' + this.currentItemId).addClass('selected').show();
                },
                setKitItem: function (element) {
                    this.resetKitCategory();
                    var id = element.id.replace("quizkit_item_id_", ''); //* remove all before '-'
                    this.currentItemId = id;
                    this.setCurrentItem();
                },
                updateKitItemContent: function (content) {
                    var contentHtml = this.prepareProductContent(content);
                    var id = '#quizkit_item_id_' + this.currentItemId;
                    if ($(id + ' .content .wrap').length == 0) {
                    } else {
                        $(id + ' .content .wrap').html(contentHtml);
                        $(id + ' .empty').addInlineDisplayNone();
                        $(id + ' .validation-notice').addInlineDisplayNone();
                        $(id + ' .content').removeInlineStyle();
                    }
                },
                prepareProductContent: function (content) {
                    var contentHtml = content.image;
                    return contentHtml;
                },
                getProductContent: function (element) {
                    var content = {};
                    content.image = $(element).find('.img').get(0).outerHTML;
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
                    this.resetItemsContent(item);
                    this.setKitItem(item);
                },
                setDefault: function () {
                },
                update: function () {
                    if ($(window).width() > WINDOW_SIZE_BREAKPOINT_SMALL) {
                        this.setLarge();
                    } else {
                        this.setSmall();
                    }
                },
                setLarge: function () {
                    if (this.selectCurrentState == 'large')
                        return true;
                    $("#quizkit_item_tabs").hide();
                    $('.quizkit-item').show();
                    $('.quizkit-item').find('.category-name').show();
                    this.selectCurrentState = 'large';
                },
                setSmall: function () {
                    if (this.selectCurrentState == 'small')
                        return true;
                    $('.quizkit-item').hide();
                    this.setCurrentItem();
                    $("#quizkit_item_tabs").html('');
                    $('.quizkit-item').find('.category-name').each(function (idx, element) {
                        var $item = $(element);
                        $item.clone().appendTo("#quizkit_item_tabs");
                        $item.hide();
                    }.bind(this));
                    $("#quizkit_item_tabs").show().find('.category-name').removeAttr('style');
                    this.selectCurrentState = 'small';
                    $("#quizkit_item_tabs").on('click', '.category-name', this, function (e) {
                        e.data.tabsBox.find('.category-name').removeClass('selected');
                        var $item = $(this);
                        if (!$item.hasClass('selected'))
                            $item.addClass('selected');
                        e.data.items.hide();
                        var element = $('#' + e.data.itemIdPrefix + $item.attr('data-item-id')).show().get(0);
                        e.data.setKitItem(element);
                    });
                },
                getContent: function (option) {
                    var content = $('#' + option).find('.category-name').clone();
                    return content;
                },
                resetKit: function () {
                    var that = this;
                    $('.quizkit-item').each(function (index) {
                        var id = this.id.replace(that.itemIdPrefix, '');
                        delete that.selectedProducts[id];
                    });
                    $('.quizkit-item').removeClass('selected');
                    $('.quizkit-item').find('.content').addInlineDisplayNone();
                    $('.quizkit-item').find('.validation-notice').addInlineDisplayNone();
                    $('.quizkit-item').find('.content .wrap').html('');
                    $('.quizkit-item').find('.empty').removeInlineStyle();
                    $('.quizkit-select-products-box').parent('.free-coolor-samples').hide();
                },
                init: function () {
                    this.gotoMakeupcounterItem = data.gotoMakeupcounterItem;
                    this.addToCartUrl = data.addToCartUrl;
                    this.items = data.items;
                    this.products = data.products;
                    this.removeBtn = data.removeBtn;
                    this.itemIdPrefix = data.itemIdPrefix;
                    this.samplesBox = data.samplesBox;
                    this.sampleBoxIdPrefix = data.sampleBoxIdPrefix;
                    this.sampleProducts = data.sampleProducts;
                    this.sampleProductIdPrefix = data.sampleProductIdPrefix;
                    this.tabsBox = data.tabsBox;
                    $("#quiz_add_to_cart").on('click', this, function (e) {
                    });
                    $(".quizkit-item").on('click', '.empty', this, function (e) {
                        var item = $(this).parents('li').get(0);
                        QuizKit.setKitItem(item);
                    });
                    $(".quizkit-item").on('click', '.validation-notice', this, function (e) {
                        $(this).parents('li').children('.empty').show();
                        var item = $(this).parents('li').get(0);
                        QuizKit.setKitItem(item);
                    });
                    $(".quizkit-item").on('click', '.content .wrap', this, function (e) {
                        var item = $(this).parents('li').get(0);
                        QuizKit.setKitItem(item);
                    });
                    $(".quizkit-select-product").on('click', this, function (e) {
                        QuizKit.addSelectedProduct(this);
                    });
                    $(".quizkit-reset-item-button").on('click', this, function (e) {
                        QuizKit.removeSelectedProduct(this);
                    });
                    this.setDefault();
                    QuizKit.update();
                    this.resetAllSelectedProduct();
                }
            };
            var QuizBrush = {
                addBrushItem: {},
                addToCartItem: {},
                gotoMakeupcounterItem: {},
                brushesWrap: {},
                brushesBox: {},
                brushesProductIdPrefix: '',
                brushes: [],
                removeBtn: {},
                selectedBrush: '',
                selectCurrentState: '',
                update: function () {
                    if ($(window).width() > WINDOW_SIZE_BREAKPOINT_SMALL) {
                        this.setLarge();
                    } else {
                        this.setSmall();
                    }
                },
                destroyCarousel: function (element) {
                    if (typeof element.data('owlCarousel') == 'undefined')
                        return true;
                    element.data('owlCarousel').destroy();
                },
                setLarge: function () {
                    if (this.selectCurrentState == 'large')
                        return true;
                    this.destroyCarousel($('#quizkit_brush_products_box'));
                    //init large carousel
                    $('#quizkit_brush_products_box').owlCarousel({
                        items: 3,
                        itemsTablet: [1199, 1],
                        itemsMobile: [769, 1],
                        stopOnHover: true,
                        navigation: true,
                        navigationText: false,
                        pagination: false,
                        rewindNav: false
                    });
                    this.selectCurrentState = 'large';
                },
                setSmall: function () {
                    if (this.selectCurrentState == 'small')
                        return true;
                    this.destroyCarousel($('#quizkit_brush_products_box'));
//init small carousel
                    $('#quizkit_brush_products_box').owlCarousel({
                        items: 3,
                        itemsTablet: [1199, 1],
                        itemsMobile: [769, 1],
                        stopOnHover: true,
                        navigation: true,
                        navigationText: false,
                        pagination: false,
                        rewindNav: false,
                        animateOut: 'slideOutUp',
                        animateIn: 'slideInUp'
                    });
                    this.selectCurrentState = 'small';
                },
                addSelectedProduct: function addSelectedProduct(element) {
                    var id = element.id.replace(this.brushesProductIdPrefix, '');
                    this.selectedBrush = id;
                    this.brushes.removeClass('selected');
                    $(element).addClass('selected');
                    this.updateKitItemContent(this.getProductContent(element));
                },
                getProductContent: function getProductContent(element) {
                    var content = {};
                    content.image = $(element).find('img').get(0).outerHTML;
                    return content;
                },
                updateKitItemContent: function updateKitItemContent(content) {
                    var contentHtml = this.prepareProductContent(content);
                    //var id = '#' + this.addBrushItem.attr('id');
                    var id = '#quiz_add_brush';
                    if ($(id + ' .content .wrap').length == 0) {
//$(id).append(contentHtml);
                    } else {
                        $(id + ' .content .wrap').html(contentHtml);
                        $(id + ' .content').show();
                        $(id + ' .empty').hide();
                    }
                },
                prepareProductContent: function prepareProductContent(content) {
                    var contentHtml = content.image;
                    return contentHtml;
                },
                setBrushItem: function () {
                    $('#quiz_add_brush').addClass('selected');
                    $('#quiz_add_to_cart').hide();
                    $('#quiz_goto_makeupcounter').hide();
                    var brashStyle = '<style>'
                        + '.quiz-basket-list.quiz-basket-list{width: 33.333%;}'
                        + '.quiz-basket-list.quiz-basket-list li{width: 100%;}'
                        + '.quiz-basket-list.quiz-basket-list li:before{left: 50%;}'
                        + '</style>';
                    $('#quiz_add_brush').append(brashStyle);
                    $('.quiz-brushes-wrap').removeInlineStyle();
                    $('#quizkit_brush_products_box').show();
                },
                unsetBrushItem: function () {
                    $('#quiz_add_brush').removeClass('selected');
                    $('.quiz-brushes-wrap').addInlineDisplayNone();
                    $('#quizkit_brush_products_box').hide();
                    $('#quiz_add_brush').find('style').empty();
                    $('#quiz_add_to_cart').show();
                    $('#quiz_goto_makeupcounter').show();
                },
                resetItemsContent: function (element) {
                    if (typeof element == "undefined") {
                        element = this.items;
                    }
                    $(element).find('.content').hide();
                    $(element).find('.content .wrap').html('');
                    $(element).find('.empty').show();
                },
                removeSelectedProduct: function (element) {
                    this.selectedBrush = '';
                    var item = $(element).parents('li').get(0);
                    this.resetItemsContent(item);
                    this.setBrushItem();
                },
                resetBrush: function () {
                    this.unsetBrushItem();
                    this.resetItemsContent(this.addBrushItem);
                    this.selectedBrush = '';
                },
                init: function () {
                    this.addBrushItem = data.addBrushItem;
                    this.addToCartItem = data.addToCartItem;
                    this.gotoMakeupcounterItem = data.gotoMakeupcounterItem;
                    this.brushesWrap = data.brushesWrap;
                    this.brushesBox = data.brushesBox;
                    this.brushesProductIdPrefix = data.brushesProductIdPrefix;
                    this.brushes = data.brushes;
                    this.removeBtn = data.removeBtn;
                    var that = this;
                    if (this.brushes.length == 0)
                        return false;
                    $('#quiz_add_brush').find('.empty').on('click', function () {
                        if (that.selectedBrush == '' && $("#quiz_add_brush").hasClass('selected')) {
                            that.unsetBrushItem();
                            return;
                        }
                        that.setBrushItem();
                    });
                    $('#quiz_add_brush').find('.content').on('click', function () {
                        if (that.selectedBrush != '' && !that.addBrushItem.hasClass('selected')) {
                            that.setBrushItem();
                        } else {
                            that.unsetBrushItem();
                        }
                    });
                    $('.quizkit-brush-product').on('click', function () {
                        that.addSelectedProduct(this);
                        that.unsetBrushItem();
                    });
                    $('#quizkit_brash_remove_btn').on('click', function () {
                        that.removeSelectedProduct(this);
                    });
                    this.update();
                }
            };
            FormulaSelect.init();
            QuizKit.init();
            Quiz.init();
            QuizBrush.init();
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
            }($));
        });
    };
});
