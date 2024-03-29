/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    "jquery/ui"
], function ($) {
    'use strict';
    $.widget('udeytech.lightbox', {

        _create: function () {
            this.album = [];
            this.currentImageIndex = void 0;
            this.init();

            this.option(this.options);
        },

        // Descriptions of all options available on the demo site:
        // http://lokeshdhakar.com/projects/lightbox2/index.html#options
        options: {
            albumLabel: 'Image %1 of %2',
            alwaysShowNavOnTouchDevices: false,
            fadeDuration: 500,
            fitImagesInViewport: true,
            // maxWidth: 800,
            // maxHeight: 600,
            positionFromTop: 50,
            resizeDuration: 700,
            showImageNumberLabel: true,
            wrapAround: false,
            disableScrolling: false
        },

        imageCountLabel: function (currentImageNum, totalImages) {
            return this.options.albumLabel.replace(/%1/g, currentImageNum).replace(/%2/g, totalImages);
        },

        init: function () {
            this.enable();
            this.build();
        },

        // Loop through anchors and areamaps looking for either data-lightbox attributes or rel attributes
        // that contain 'lightbox'. When these are clicked, start lightbox.
        enable: function () {
            var self = this;
            $('body').on('click', 'a[rel^=lightbox], area[rel^=lightbox], a[data-lightbox], area[data-lightbox]', function (event) {
                self.start($(event.currentTarget));
                return false;
            });
        },

        // Build html for the lightbox and the overlay.
        // Attach event handlers to the new DOM elements. click click click
        build: function () {
            var self = this;
            this.imagePlaceholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
            this.imageTpl = '<img class="lb-image" src="' + this.imagePlaceholder + '" />';
            this.videoTpl = '<video class="lb-image" poster="' + this.imagePlaceholder + '" src="" />';
            $('<div id="lightboxOverlay" class="lightboxOverlay"></div><div id="lightbox" class="lightbox"><div class="lb-outerContainer"><div class="lb-container">'
                + this.imageTpl
                + '<div class="lb-nav"><a class="lb-prev" href="" ></a><a class="lb-next" href="" ></a></div><div class="lb-loader"><a class="lb-cancel"></a></div></div></div><div class="lb-dataContainer"><div class="lb-data"><div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div><div class="lb-closeContainer"><a class="lb-close"></a></div></div></div></div>').appendTo($('body'));

            // Cache jQuery objects
            this.$lightbox = $('#lightbox');
            this.$overlay = $('#lightboxOverlay');
            this.$outerContainer = this.$lightbox.find('.lb-outerContainer');
            this.$container = this.$lightbox.find('.lb-container');

            // Store css values for future lookup
            this.containerTopPadding = parseInt(this.$container.css('padding-top'), 10);
            this.containerRightPadding = parseInt(this.$container.css('padding-right'), 10);
            this.containerBottomPadding = parseInt(this.$container.css('padding-bottom'), 10);
            this.containerLeftPadding = parseInt(this.$container.css('padding-left'), 10);

            // Attach event handlers to the newly minted DOM elements
            this.$overlay.hide().on('click', function () {
                self.end();
                return false;
            });

            this.$lightbox.hide().on('click', function (event) {
                if ($(event.target).attr('id') === 'lightbox') {
                    self.end();
                }
                return false;
            });

            this.$outerContainer.on('click', function (event) {
                if ($(event.target).attr('id') === 'lightbox') {
                    self.end();
                }
                return false;
            });

            this.$lightbox.find('.lb-prev').on('click', function () {
                if (self.currentImageIndex === 0) {
                    self.changeImage(self.album.length - 1);
                } else {
                    self.changeImage(self.currentImageIndex - 1);
                }
                return false;
            });

            this.$lightbox.find('.lb-next').on('click', function () {
                if (self.currentImageIndex === self.album.length - 1) {
                    self.changeImage(0);
                } else {
                    self.changeImage(self.currentImageIndex + 1);
                }
                return false;
            });

            this.$lightbox.find('.lb-loader, .lb-close').on('click', function () {
                self.end();
                return false;
            });
        },

        // Show overlay and lightbox. If the image is part of a set, add siblings to album array.
        start: function ($link) {
            var self = this;
            var $window = $(window);

            $window.on('resize', $.proxy(this.sizeOverlay, this));

            $('select, object, embed').css({
                visibility: 'hidden'
            });

            this.sizeOverlay();

            this.album = [];
            var imageNumber = 0;

            function addToAlbum($link, index) {
                var that = self;
                $link.parent().bind('updated', function () {
                    if (that.galeryId
                        && $link.attr('data-lightbox') === that.galeryId
                        && index === that.currentImageIndex
                    ) {
                        var medium = that.album[that.currentImageIndex];
                        var $image = $('.lb-image');
                        self.sizeContainer($image.width(), $image.height(), function () {
                            if (medium.element) {
                                var videoSrc = medium.element.attr('data-video-src');
                                if (videoSrc) {
                                    var el = $(that.videoTpl);
                                    el.attr('controls', 'controls');
                                    el.width($image.width());
                                    el.height($image.height());
                                    el.attr('poster', medium.link);
                                    $image.replaceWith(el);
                                    $image = that.$lightbox.find('.lb-image');
                                    $image.attr('src', videoSrc);
                                }
                            }
                        });
                        that.updateDetails(false);
                    }
                });
                var vid = $link.find('img');
                var data = {
                    link: $link.attr('href'),
                    is_video: vid.attr('data-video-src') ? true : false,
                    title: $link.attr('data-title') || $link.attr('title'),
                };
                if (vid.length) {
                    data.src = vid.attr('data-video-src');
                }
                data.element = vid;

                self.album.push(data);
            }

            // Support both data-lightbox attribute and rel attribute implementations
            var dataLightboxValue = $link.attr('data-lightbox');
            var $links;

            if (dataLightboxValue) {
                $links = $($link.prop('tagName') + '[data-lightbox="' + dataLightboxValue + '"]');
                for (var i = 0; i < $links.length; i = ++i) {
                    addToAlbum($($links[i]), i);
                    if ($links[i] === $link[0]) {
                        imageNumber = i;
                    }
                }
            } else {
                if ($link.attr('rel') === 'lightbox') {
                    // If image is not part of a set
                    addToAlbum($link);
                } else {
                    // If image is part of a set
                    $links = $($link.prop('tagName') + '[rel="' + $link.attr('rel') + '"]');
                    for (var j = 0; j < $links.length; j = ++j) {
                        addToAlbum($($links[j]), i);
                        if ($links[j] === $link[0]) {
                            imageNumber = j;
                        }
                    }
                }
            }
            // Position Lightbox
            var top = $window.scrollTop() + this.options.positionFromTop;
            var left = $window.scrollLeft();
            this.$lightbox.css({
                top: top + 'px',
                left: left + 'px'
            }).fadeIn(this.options.fadeDuration);

            // Disable scrolling of the page while open
            if (this.options.disableScrolling) {
                $('body').addClass('lb-disable-scrolling');
            }

            this.changeImage(imageNumber);
        },


        // Hide most UI elements in preparation for the animated resizing of the lightbox.
        changeImage: function (imageNumber) {
            var self = this;

            this.disableKeyboardNav();
            var $image = this.$lightbox.find('.lb-image');

            this.$overlay.fadeIn(this.options.fadeDuration);

            $('.lb-loader').fadeIn('slow');
            this.$lightbox.find('.lb-image, .lb-nav, .lb-prev, .lb-next, .lb-dataContainer, .lb-numbers, .lb-caption').hide();

            this.$outerContainer.addClass('animating');
            var medium = this.album[imageNumber];
            $image.replaceWith(this.imageTpl);
            $image = this.$lightbox.find('.lb-image');
            $image.addClass('loading');

            // When image to show is preloaded, we send the width and height to sizeContainer()
            var preloader = new Image();
            preloader.onload = function () {
                var $preloader;
                var imageHeight;
                var imageWidth;
                var maxImageHeight;
                var maxImageWidth;
                var windowHeight;
                var windowWidth;

                $image.attr('src', medium.link);

                $preloader = $(preloader);

                $image.width(preloader.width);
                $image.height(preloader.height);

                if (self.options.fitImagesInViewport) {
                    // Fit image inside the viewport.
                    // Take into account the border around the image and an additional 10px gutter on each side.

                    windowWidth = $(window).width();
                    windowHeight = $(window).height();
                    maxImageWidth = windowWidth - self.containerLeftPadding - self.containerRightPadding - 20;
                    maxImageHeight = windowHeight - self.containerTopPadding - self.containerBottomPadding - 120;

                    // Check if image size is larger then maxWidth|maxHeight in settings
                    if (self.options.maxWidth && self.options.maxWidth < maxImageWidth) {
                        maxImageWidth = self.options.maxWidth;
                    }
                    if (self.options.maxHeight && self.options.maxHeight < maxImageWidth) {
                        maxImageHeight = self.options.maxHeight;
                    }

                    // Is there a fitting issue?
                    if ((preloader.width > maxImageWidth) || (preloader.height > maxImageHeight)) {
                        if ((preloader.width / maxImageWidth) > (preloader.height / maxImageHeight)) {
                            imageWidth = maxImageWidth;
                            imageHeight = parseInt(preloader.height / (preloader.width / imageWidth), 10);
                            $image.width(imageWidth);
                            $image.height(imageHeight);
                        } else {
                            imageHeight = maxImageHeight;
                            imageWidth = parseInt(preloader.width / (preloader.height / imageHeight), 10);
                            $image.width(imageWidth);
                            $image.height(imageHeight);
                        }
                    }
                }
                self.sizeContainer($image.width(), $image.height(), function () {
                    if (medium.is_video) {
                        var el = $(self.videoTpl);
                        el.attr('controls', 'controls');
                        el.width($image.width());
                        el.height($image.height());
                        el.attr('poster', medium.link);
                        $image.replaceWith(el);
                        $image = self.$lightbox.find('.lb-image');
                        $image.attr('src', medium.src);
                    }
                });
            };
            preloader.src = this.album[imageNumber].link;
            this.currentImageIndex = imageNumber;
        },

        // Stretch overlay to fit the viewport
        sizeOverlay: function () {
            this.$overlay
                .width($(document).width())
                .height($(document).height());
        },

        // Animate the size of the lightbox to fit the image we are showing
        sizeContainer: function (imageWidth, imageHeight, callback) {
            var self = this;

            var oldWidth = this.$outerContainer.outerWidth();
            var oldHeight = this.$outerContainer.outerHeight();
            var newWidth = imageWidth + this.containerLeftPadding + this.containerRightPadding;
            var newHeight = imageHeight + this.containerTopPadding + this.containerBottomPadding;

            function postResize() {
                self.$lightbox.find('.lb-dataContainer').width(newWidth);
                self.$lightbox.find('.lb-prevLink').height(newHeight);
                self.$lightbox.find('.lb-nextLink').height(newHeight);
                self.showImage();
                if (typeof callback == 'function') {
                    callback();
                }
            }

            if (oldWidth !== newWidth || oldHeight !== newHeight) {
                this.$outerContainer.animate({
                    width: newWidth,
                    height: newHeight
                }, this.options.resizeDuration, 'swing', function () {
                    postResize();
                });
            } else {
                postResize();
            }

        },

        // Display the image and its details and begin preload neighboring images.
        showImage: function () {
            this.$lightbox.find('.lb-loader').stop(true).hide();
            this.$lightbox.find('.lb-image').fadeIn('slow');

            this.updateNav();
            this.updateDetails();
            this.preloadNeighboringImages();
            this.enableKeyboardNav();
        },

        // Display previous and next navigation if appropriate.
        updateNav: function () {
            // Check to see if the browser supports touch events. If so, we take the conservative approach
            // and assume that mouse hover events are not supported and always show prev/next navigation
            // arrows in image sets.
            var alwaysShowNav = false;
            try {
                document.createEvent('TouchEvent');
                alwaysShowNav = (this.options.alwaysShowNavOnTouchDevices) ? true : false;
            } catch (e) {
            }

            this.$lightbox.find('.lb-nav').show();

            if (this.album.length > 1) {
                if (this.options.wrapAround) {
                    if (alwaysShowNav) {
                        this.$lightbox.find('.lb-prev, .lb-next').css('opacity', '1');
                    }
                    this.$lightbox.find('.lb-prev, .lb-next').show();
                } else {
                    if (this.currentImageIndex > 0) {
                        this.$lightbox.find('.lb-prev').show();
                        if (alwaysShowNav) {
                            this.$lightbox.find('.lb-prev').css('opacity', '1');
                        }
                    }
                    if (this.currentImageIndex < this.album.length - 1) {
                        this.$lightbox.find('.lb-next').show();
                        if (alwaysShowNav) {
                            this.$lightbox.find('.lb-next').css('opacity', '1');
                        }
                    }
                }
            }
        },

        // Display caption, image number, and closing button.
        updateDetails: function (fullUpdate) {
            var self = this;
            fullUpdate = fullUpdate ? fullUpdate : true;
            var medium = this.album[this.currentImageIndex];
            if (medium) {
                if (fullUpdate) {
                    // Enable anchor clicks in the injected caption html.
                    // Thanks Nate Wright for the fix. @https://github.com/NateWr
                    if (medium.title !== 'undefined' && medium.title !== '') {
                        this.$lightbox.find('.lb-caption')
                            .html(medium.title)
                            .fadeIn('fast')
                            .find('a').on('click', function (event) {
                            if ($(this).attr('target') !== undefined) {
                                window.open($(this).attr('href'), $(this).attr('target'));
                            } else {
                                location.href = $(this).attr('href');
                            }
                        });
                    }
                }
                if (medium.element) {
                    this.galeryId = medium.element.parent().attr('data-lightbox');
                    var icons = medium.element.parent().parent().find('.icons');
                    if (icons.length) {
                        this.$lightbox.find('.lb-details .icons').remove();
                        this.$lightbox.find('.lb-details').prepend(icons.clone());
                    }
                }
            }
            if (fullUpdate) {
                if (this.album.length > 1 && this.options.showImageNumberLabel) {
                    var labelText = this.imageCountLabel(this.currentImageIndex + 1, this.album.length);
                    this.$lightbox.find('.lb-number').text(labelText).fadeIn('fast');
                } else {
                    this.$lightbox.find('.lb-number').hide();
                }

                this.$outerContainer.removeClass('animating');

                this.$lightbox.find('.lb-dataContainer').fadeIn(this.options.resizeDuration, function () {
                    return self.sizeOverlay();
                });
            }
        },

        // Preload previous and next images in set.
        preloadNeighboringImages: function () {
            if (this.album.length > this.currentImageIndex + 1) {
                var preloadNext = new Image();
                preloadNext.src = this.album[this.currentImageIndex + 1].link;
            }
            if (this.currentImageIndex > 0) {
                var preloadPrev = new Image();
                preloadPrev.src = this.album[this.currentImageIndex - 1].link;
            }
        },

        enableKeyboardNav: function () {
            $(document).on('keyup.keyboard', $.proxy(this.keyboardAction, this));
        },

        disableKeyboardNav: function () {
            $(document).off('.keyboard');
        },

        keyboardAction: function (event) {
            var KEYCODE_ESC = 27;
            var KEYCODE_LEFTARROW = 37;
            var KEYCODE_RIGHTARROW = 39;

            var keycode = event.keyCode;
            var key = String.fromCharCode(keycode).toLowerCase();
            if (keycode === KEYCODE_ESC || key.match(/x|o|c/)) {
                this.end();
            } else if (key === 'p' || keycode === KEYCODE_LEFTARROW) {
                if (this.currentImageIndex !== 0) {
                    this.changeImage(this.currentImageIndex - 1);
                } else if (this.options.wrapAround && this.album.length > 1) {
                    this.changeImage(this.album.length - 1);
                }
            } else if (key === 'n' || keycode === KEYCODE_RIGHTARROW) {
                if (this.currentImageIndex !== this.album.length - 1) {
                    this.changeImage(this.currentImageIndex + 1);
                } else if (this.options.wrapAround && this.album.length > 1) {
                    this.changeImage(0);
                }
            }
        },

        // Closing time. :-(
        end: function () {
            this.disableKeyboardNav();
            $(window).off('resize', this.sizeOverlay);
            var that = this;
            this.$lightbox.fadeOut(this.options.fadeDuration, function () {
                var img = that.$lightbox.find('.lb-image');
                img.replaceWith(that.imageTpl);
            });
            this.$overlay.fadeOut(this.options.fadeDuration);
            $('select, object, embed').css({
                visibility: 'visible'
            });
            if (this.options.disableScrolling) {
                $('body').removeClass('lb-disable-scrolling');
            }
        }
    });
    return $.udeytech.lightbox;
});
