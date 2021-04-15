/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    "jquery/ui"
], function ($) {
    'use strict';
    $.widget('udeytech.instagram',
        {
            BY_USER_ID_CODE: 1,
            BY_USER_ID_LABEL: 'By User ID',
            BY_HASHTAG_CODE: 2,
            BY_HASHTAG_LABEL: 'By Hashtag',
            BY_PRODUCT_HASHTAG_CODE: 3,
            BY_PRODUCT_HASHTAG_LABEL: 'By Product Hashtag',
            BY_CATEGORY_HASHTAG_CODE: 5,
            BY_CATEGORY_HASHTAG_LABEL: 'By Category Hashtag',
            BY_USER_NAME_CODE: 4,
            BY_USER_NAME_LABEL: 'By User Name',

            endpoints: {
                BASE_URL: 'https://www.instagram.com',
                ACCOUNT_PAGE: 'https://www.instagram.com/{username}',
                MEDIUM_LINK: 'https://www.instagram.com/p/{code}',
                ACCOUNT_MEDIA: 'https://www.instagram.com/{username}/media?max_id={max_id}',
                ACCOUNT_JSON_INFO: 'https://www.instagram.com/{username}/?__a=1',
                MEDIUM_JSON_INFO: 'https://www.instagram.com/p/{code}/?__a=1',
                MEDIA_JSON_BY_LOCATION_ID: 'https://www.instagram.com/explore/locations/{{facebookLocationId}}/?__a=1&max_id={{maxId}}',
                MEDIA_JSON_BY_TAG: 'https://www.instagram.com/explore/tags/{tag}/?__a=1&max_id={max_id}',
                MEDIA_JSON_BY_TAG_QUERY: 'https://www.instagram.com/query/?q=ig_hashtag({tag}){media.after({max_id},20){count,nodes{caption,code,comments{count},comments_disabled,date,dimensions{height,width},display_src,id,is_video,likes{count},owner{id},thumbnail_src,video_views},page_info}}',
                GENERAL_SEARCH: 'https://www.instagram.com/web/search/topsearch/?query={query}',
                ACCOUNT_JSON_INFO_BY_ID: 'https://www.instagram.com/query/?q=ig_user({userId}){id,username,external_url,full_name,profile_pic_url,biography,followed_by{count},follows{count},media{count},is_private,is_verified}',
                LAST_COMMENTS_BY_CODE: 'https://www.instagram.com/query/?q=ig_shortcode({{code}}){comments.last({{count}}){count,nodes{id,created_at,text,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}',
                COMMENTS_BEFORE_COMMENT_ID_BY_CODE: 'https://www.instagram.com/query/?q=ig_shortcode({{code}}){comments.before({{commentId}},{{count}}){count,nodes{id,created_at,text,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}',
                LAST_LIKES_BY_CODE: 'https://www.instagram.com/query/?q=ig_shortcode({{code}}){likes{nodes{id,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}'
            },
            thumbnails: ['thumbnail', 'low_resolution', 'standard_resolution'],

            options: {
                dataPath: 'udeytech_instagrampro/data?path=',
                listSelector: '.udeytech-inst-list',
                listItemSelector: '.udeytech-inst-list-item',
                listItemTemplateSelector: '.template',
                listMoreBtnSelector: '.udeytech_inst_more',
                listLoadingSelector: '.udeytech_inst_loading',
                onInitEndTrigger: 'udeytech-loaded'
            },

            _init: function () {
                this._isLoading;
                this.list = this.element.find(this.options.listSelector);
                this.items;
                this.pageInfo;
                this.template;
                if (this.options.config.content == '') {
                    this.element.hide();
                } else {
                    this.limit = this.options.config.type === 'slider'
                        ? this.options.config.limit * 1
                        : this.options.config.columns * this.options.config.height;
                    this._initComponents();
                    // load list from instagram
                    this.loadList();
                }
            },

            getPath: function (mode, data, lastIndex) {
                var ret;
                switch (mode) {
                    case this.BY_USER_ID_CODE:
                    //                ret = this.endpoints.ACCOUNT_JSON_INFO_BY_ID.replace('{userId}', data);
                    //                break;
                    case this.BY_USER_NAME_CODE:
                        ret = this.endpoints.ACCOUNT_MEDIA.replace('{username}', data);
                        break;
                    default:
                        if (this.items && this.items.length) {
                            ret = this.endpoints.MEDIA_JSON_BY_TAG;
                        } else {
                            ret = this.endpoints.MEDIA_JSON_BY_TAG;
                        }
                        ret = ret.replace('{tag}', data);
                }
                if (lastIndex) {
                    ret = ret.replace('{max_id}', lastIndex);
                } else {
                    ret = ret.replace('{max_id}', '');
                }
                return ret;
            },

            getCount: function (number) {
                var ret = -1;
                while (number > 1000) {
                    number /= 1000.0;
                    ret++;
                }
                number = (parseFloat(Math.round(number * 100) / 100).toFixed(1)) * 1.0;
                if (number - Math.floor(number) === 0) {
                    number = number.toFixed(0);
                }
                number = number.toLocaleString();
                return number + ' ' + (ret >= 0 && this.options.config.countShorts ? this.options.config.countShorts[ret] : '');
            },

            selectThumbnail: function (item) {
                var src = this.options.config.thumbSrc;
                var i = 0;
                var thumbs = this.thumbnails;
                switch (src) {
                    case 'big':
                        if (item.display_src) {
                            return item.display_src;
                        } else {
                            if (item.images) {
                                for (i = thumbs.length - 1; i >= 0; i--) {
                                    if (item.images[thumbs[i]]) {
                                        return item.images[thumbs[i]].url;
                                    }
                                }
                            }
                        }
                        break;
                    default:
                        if (item.images) {
                            if (item.images[src]) {
                                return item.images[src].url;
                            } else {
                                for (i = thumbs.indexOf(src) - 1; i >= 0; i++) {
                                    if (item.images[thumbs[i]]) {
                                        return item.images[thumbs[i]].url;
                                    }
                                }
                            }
                        }
                        break;
                }
                return item.thumbnail_src;
            },

//        getTemplate : function(){ return this.options.template; },

            _initComponents: function () {
                var opts = this.options;
                var cfg = opts.config;
                var element = this.element.find(opts.titleSelector);
                if (cfg.title !== "") {
                    if (element.children().length) {
                        $(element.children()[0]).text(cfg.title);
                    } else {
                        element.text(cfg.title);
                    }
                } else {
                    element.hide();
                }
                element = this.element.find(opts.descriptionSelector);
                if (cfg.description !== "") {
                    if (element.children().length) {
                        $(element.children()[0]).text(cfg.description);
                    } else {
                        element.text(cfg.description);
                    }
                } else {
                    element.hide();
                }


                // get block item template
                var tpl = this.list.find(opts.listItemTemplateSelector);
                tpl.removeClass(opts.listItemTemplateSelector.replace('.', ''));
                tpl.find('img').attr('src', opts.loadingImage);
                var img = tpl.find(opts.imageThumbnailSelector);
                if (cfg.type === 'slider') {
                    var width = cfg.width * 1;
                    if (!width) {
                        width = tpl.width();
                    } else {
                        tpl.width(width);
                    }
                    img.attr('width', width).attr('height', width);
                } else {
                    img.attr('width', cfg.imageWidth).attr('height', cfg.imageWidths);
                }
//            tpl.width(cfg.thumbnailSize.width);
                if (cfg.useLightbox === "1") {
                    img.parent().attr('data-lightbox', cfg.lightboxId);
                }
                if (!(cfg.showDetails * 1)) {
                    tpl.find('.udeytech_inst_rating').hide();
                    tpl.find('.udeytech_inst_photo_info').hide();
                }
                if (cfg.show.avatar !== "1" && cfg.show.userName !== "1") {
                    tpl.find('.udeytech_inst_photo_account').hide();
                    tpl.find('.udeytech_inst_photo_account:before').hide();
                } else {
                    if (cfg.show.avatar !== "1") tpl.find('.udeytech_inst_photo_account').hide();
                    if (cfg.show.userName !== "1") tpl.find('.udeytech_inst_photo_userName').hide();
                }
                if (cfg.show.likes !== "1") tpl.find('.udeytech_inst_likes').hide();
                if (cfg.show.comments !== "1") tpl.find('.udeytech_inst_comments').hide();
                if (cfg.show.postTime !== "1") tpl.find('.udeytech_inst_photo_postTime').hide();
                if (cfg.show.link !== "1") tpl.find('.link').hide();

                tpl.removeClass(opts.listItemTemplateSelector);
                this.template = tpl.clone();
                tpl.remove();

                var that = this;
                // set "show more" click
                this._getShowMoreBtn().click(function () {
                    var step = that.options.config.step * 1;
                    var items = that._getListItems();
                    var nextCount = items.length + (step * that.options.config.width);
                    if (nextCount > that.items.length) {
                        that.loadList();
                    } else {
                        that.initItems(step * that.options.config.width);
                    }
                });
            },

            /**
             * Loads list of items from instagram.
             * @param list Element with ".udeytech-instagram-list" class
             * @param index Starting index of new elements if there is an update
             */
            loadList: function (count) {
                this._startLoading();
                var that = this;
                var list = this.list;
                var lastIndex = null;
                if (this.pageInfo) {
                    if (this.pageInfo.has_next) {
                        lastIndex = this.pageInfo.last_cursor;
                    } else {
                        return 1;
                    }
                }
                var path = encodeURIComponent(
                    this.getPath(
                        this.options.config.mode * 1,
                        this.options.config.content,
                        lastIndex
                    )
                );

                $.ajax({
                    url: this.options.dataPath + path,
                    type: "GET",
                    dataType: "json",
                    cache: false,
                    success: function (data, status) {
                        if (data) {
                            that.processData(data, count);
                        } else {
                            $('.udeytech-instagram-list').show();
                            that.initItems(count);
                            console.log('error with Instagram!');
                        }
                    },
                    fail: function () {
                        if (typeof console === 'function')
                            console.log(arguments);
                    }
                });
            },

            processData: function (data, count) {
                var list = this.list;
                var cfg = this.options.config;
                if (cfg.mode * 1 === this.BY_USER_NAME_CODE ||
                    cfg.mode * 1 === this.BY_USER_ID_CODE
                ) {
                    if (data.items.length) {
                        this.addUserItems(data.items);
                        this.pageInfo = {
                            last_cursor: data.items[data.items.length - 1].id,
                            has_next: data.more_available
                        };
                    } else {
                        this.pageInfo = {
                            last_cursor: null,
                            has_next: false
                        };
                        this.items = []
                    }
                } else {
                    var nodes;
                    if (data.tag) {
                        if (data.tag.media.nodes.length) {
                            nodes = data.tag.media.nodes;
                        }
                        this.pageInfo = {
                            last_cursor: data.tag.media.page_info.end_cursor,
                            has_next: data.tag.media.page_info.has_next_page
                        };
                    } else if (data.media) {
                        if (data.media.nodes.length) {
                            nodes = data.media.nodes;
                        }
                        this.pageInfo = {
                            last_cursor: data.media.page_info.end_cursor,
                            has_next: data.media.page_info.has_next_page
                        };
                    }
                    this.addTagItems(nodes);
                }
                var step = cfg.step;
                cfg.stepped *= 1;
                var existingItemcount = list.find(this.options.listItemSelector).length;
                var allItemcount = this.items.length;
                if (cfg.type === 'slider') {
                    if (this.pageInfo.has_next && allItemcount < cfg.limit * 1) {
                        this.loadList();
                    } else {
                        this.initItems();
                        list.trigger(this.options.onInitEndTrigger);
                    }
                } else {
                    if (this.pageInfo.has_next && (
                        (cfg.stepped && (allItemcount - existingItemcount) < step * cfg.columns)
                        || (!cfg.stepped && allItemcount < (cfg.columns * cfg.height))
                    )) {
                        this.loadList(count);
                    } else {
                        list.trigger(this.options.onInitEndTrigger);
                        this.initItems(count);
                    }
                }
            },

            addUserItems: function (items) {
                for (var j = 0; j < items.length; j++) {
                    var item = items[j];
                    var images = item.images;
                    item.caption = item.caption ? item.caption.text : '';
                    item.comments_disabled = false;
                    item.date = item.created_time;
                    item.dimensions = {
                        width: images.standard_resolution.width,
                        height: images.standard_resolution.height
                    };
//                item.display_src = images.standard_resolution.url;
                    item.is_video = false;
                    if (item.type === 'video') {
                        item.is_video = true;
                    }
                    item.owner = item.user;
                    item.user.profile_pic_url = item.user.profile_picture;
                    InstagramUsers.setUser(item.user.id, item.user);
                    item.thumbnail_src = images.thumbnail.url;
                }
                if (this.items) {
                    this.items = this.items.concat(items);
                } else {
                    this.items = items;
                }
            },

            addTagItems: function (items) {
                if (this.items) {
                    this.items = this.items.concat(items);
                } else {
                    this.items = items;
                }
            },

            initItems: function (count) {
                var items = this.items;
                var tpl = this.template;
                var list = this.list;
//                if (items.length == 0) {
//                    this.element.hide()
//                    this._endLoading();
//                    return;
//                }
                if (!count) {
                    if (this.options.config.type === 'slider') {
                        count = this.limit;
                    } else {
                        count = this.options.config.height * this.options.config.columns;
                    }
                }
                var that = this;
                var cfg = this.options.config;
                var start = list.find(this.options.listItemSelector).length;
                var idx = 1;
                for (var i = start; i < items.length; i++, idx++) {

                    if (i >= this.limit || (count && idx > count)) {
                        if (i >= this.limit) {
                            $(this.list).parent().find(this.options.listMoreBtnSelector).hide();
                        } else {
                            this._endLoading();
                        }
                        break;
                    }

                    var item = items[i];
                    var el = tpl.clone();
                    list.append(el);
                    item.element = el;
                    if (item.display_src) {
                        var eel = $(item.element.find('a.instagram-thumbnail')[0]);
                        if (cfg.useLightbox * 1) {
                            eel.attr("href", item.display_src);
                        } else {
                            eel.attr("href", this.endpoints.MEDIUM_LINK.replace('{code}', item.code));
                        }
                    } else {
                        this.loadMedium(item, function (item) {
                            var eel = $(item.element.find('a.instagram-thumbnail')[0]);
                            if (cfg.useLightbox * 1) {
                                eel.attr("href", item.display_src);
                            } else {
                                eel.attr("href", that.endpoints.MEDIUM_LINK.replace('{code}', item.code));
                            }
                        });
                    }

                    this.loadImage(el, this.selectThumbnail(items[i]), item);

                    el.find('a.udeytech_inst-thumbnail,a.link,img.instagram-thumbnail').attr('title', items[i].caption);
                    el.find('img.instagram-thumbnail').attr('alt', items[i].caption);
                    el.attr('data-item', JSON.stringify(item).replace('"', '\''));
                    if (!item.is_video) {
                        el.find('.glyphicon-play-circle').remove();
                        //if (this.options.config.showDetails*1) {
                        this.getUser(item.owner.id, item.code, el,
                            function (user, element) {
                                that._setUserIcons(user, element);
                                element.find('.instagram-thumbnail').trigger('loaded');
                            });
                        //}
                    } else {
//                    el.addClass('video');
                        if (!item.videos) {
                            item.element = el;
                            this.loadMedium(item, function (item) {
                                item.element.addClass('udeytech_inst_video');
                            });
                        } else {
                            el.find('img.instagram-thumbnail').attr('data-video-src', item.videos.standard_resolution.url);
                            //  if (this.options.config.showDetails*1) {
                            this.getUser(item.owner.id, item.code, el,
                                function (user, element) {
                                    element.find('.udeytech_inst-thumbnail').trigger('loaded');
                                    element.addClass('udeytech_inst_video');
                                    that._setUserIcons(user, element);
                                });
                            //}
                        }
                    }
                    this._setIcons(item, el);
                    if (that.options.config.showDetails * 1 === 0) {
                        el.find('.instagram-thumbnail + .icons').hide();
                    }
                }
                this._endLoading();
            },

            _setIcons: function (item, el) {
                var likes = el.find('.instagram-thumbnail + .icons .likes').contents().first();
                if (likes) likes[0].textContent = this.getCount(
                    item.is_video ?
                        item.video_view_count :
                        item.__typename ?
                            item.edge_media_preview_like.count :
                            item.likes.count);
                var comm = el.find('.instagram-thumbnail + .icons .comments').contents().first();
                if (item.is_video) {
                    if (likes) likes.parent().find('span').removeClass('glyphicon-heart').addClass('glyphicon-film');
                    if (comm) comm.parent().hide()
                } else {
                    if (comm) comm[0].textContent = this.getCount(
                        item.__typename ? item.edge_media_to_comment.count : item.comments.count
                    );
                }
                el.find('.instagram-thumbnail + .icons .postTime').text(this.time_ago((item.is_video ? item.taken_at_timestamp : item.date) * 1000));
                el.trigger('updated');
            },
            _setUserIcons: function (user, element) {
                element.find('.instagram-thumbnail + .icons .account')
                    .attr('href', this.endpoints.ACCOUNT_PAGE.replace('{username}', user.username))
                    .attr('title', user.full_name);
                element.find('.instagram-thumbnail + .icons .account .user-image')
                    .attr('src', user.profile_pic_url);
                element.find('.instagram-thumbnail + .icons .account .user-name').text(user.full_name ? user.full_name : user.username);
                element.trigger('updated');
            },

            loadImage: function (element, imagePath, item) {
                // When image to show is preloaded
                var preloader = new Image();
                var that = this;
                preloader.onload = function () {
                    element.find('img.instagram-thumbnail').attr('src', imagePath);
                    element.find('a.instagram-thumbnail').css({"min-height": that.options.config.imageWidth + 'px'});
                };
                preloader.src = imagePath;
            },

            loadMedium: function (item, callback) {
                var that = this;
                var cfg = this.options.config;
                var element = item.element;
                item.element.addClass('udeytech_inst_loading');
                $.get(this.options.dataPath
                    + this.endpoints.MEDIUM_JSON_INFO.replace('{code}', item.code),
                    function (result) {
                        if (!result || !result.graphql) return;
                        var owner = result.graphql.shortcode_media.owner;
                        var user = InstagramUsers.getUser(owner.id);
                        if (!user) {
                            InstagramUsers.setUser(owner.id, owner)
                        }

                        if (result.graphql.shortcode_media.video_url) {
                            element.find('.instagram-thumbnail').attr('data-video-src', result.graphql.shortcode_media.video_url);
                            element.addClass('thumbnail-video');
                        }
                        if (!item.display_src) {
                            item.display_src = result.graphql.shortcode_media.display_src;
                        }
                        var eel = element.find('a.instagram-thumbnail');
                        var data = JSON.parse('{"' + element.attr('data-item').substring(2));
                        if (cfg.useLightbox * 1) {
                            eel.attr("href", data.thumbnail_src);
                        } else {
                            eel.attr("href", data.link);
                        }

                        element.find('.instagram-thumbnail').attr('data-video-src', result.graphql.shortcode_media.video_url);

                        var eel = element.find('a.instagram-thumbnail');
                        if (cfg.useLightbox * 1) {
                            eel.attr("href", result.graphql.shortcode_media.display_url);
                        } else {
                            eel.attr("href", result.graphql.shortcode_media.link);
                        }

//                        element.attr('data-item', JSON.stringify(result.graphql.shortcode_media).replace('"','\''));
                        element.removeClass('udeytech_inst_loading');
                        element.find('.instagram-thumbnail').trigger('loaded');
                        //if (that.options.config.showDetails*1) {
                        that.getUser(item.owner.id, item.code, element,
                            function (user, element) {
                                element.find('.instagram-thumbnail').trigger('loaded');
                                element.find('a.instagram-thumbnail').css({"min-height": that.options.config.imageWidth + 'px'});
                                that._setUserIcons(user, element);
                                if (callback) {
                                    that._setIcons(result.graphql.shortcode_media, element);
                                    callback(item);
                                }
                            });
//                    }
                    }
                ).fail(function () {
                    var eel = $(item.element.find('a.instagram-thumbnail')[0]);
                    if (cfg.useLightbox * 1) {
                        eel.attr("href", item.display_src);
                    } else {
                        eel.attr("href", that.endpoints.MEDIUM_LINK.replace('{code}', item.code));
                    }
                });
            },

            getUser: function (uid, code, element, callback) {
                var user = InstagramUsers.getUser(uid);
                if (!user && undefined !== code) {
                    var that = this;
                    InstagramUsers.loadUser(uid,
                        this.options.dataPath + this.endpoints.MEDIUM_JSON_INFO.replace('{code}', code),
                        element,
                        callback);
                } else {
                    callback(user, element);
                }
            },

            _getShowMoreBtn: function () {
                return this.element.find(this.options.listMoreBtnSelector);
            },
            /**
             * Selects elements with this.options.listItemSelector
             * @returns jQuery object
             */
            _getListItems: function () {
                return this.list.find(this.options.listItemSelector)
            },

            _getLoadingEl: function () {
                return this.element.find('.loading')
            },

            _startLoading: function () {
                var list = this.list;
                this._isLoading = true;
                //        if (list.find(this.options.listItemSelector).length){
                //
                //        }
                var more = this._getShowMoreBtn();
                if (more.length) more.hide();
                var loading = this._getLoadingEl();
                loading.show();
            },
            _endLoading: function (index) {
                var list = this.list;
                this._isLoading = false;
                var more = this._getShowMoreBtn();
                var loading = this._getLoadingEl();
//                if (more.length) more.show();
                loading.hide();
                this.element.find('.udeytech-instagram-list').show();
            },
            _errorLoading: function (index) {
                var list = this.list;
                this._isLoading = false;
                var more = this._getShowMoreBtn();
                var loading = this._getLoadingEl();
                if (more.length) more.show();
                loading.hide();
            },

            time_ago: function (time) {
                if (!time) return 0;
                switch (typeof time) {
                    case 'number':
                        break;
                    case 'string':
                        time = +new Date(time);
                        break;
                    case 'object':
                        if (time.constructor === Date) time = time.getTime();
                        break;
                    default:
                        time = +new Date();
                }
                var time_formats = [
                    [60, 'seconds', 1], // 60
                    [120, '1 minute ago', '1 minute from now'], // 60*2
                    [3600, 'minutes', 60], // 60*60, 60
                    [7200, '1 hour ago', '1 hour from now'], // 60*60*2
                    [86400, 'hours', 3600], // 60*60*24, 60*60
                    [172800, 'Yesterday', 'Tomorrow'], // 60*60*24*2
                    [604800, 'days', 86400], // 60*60*24*7, 60*60*24
                    [1209600, 'Last week', 'Next week'], // 60*60*24*7*4*2
                    [2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
                    [4838400, 'Last month', 'Next month'], // 60*60*24*7*4*2
                    [29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
                    [58060800, 'Last year', 'Next year'], // 60*60*24*7*4*12*2
                    [2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
                    [5806080000, 'Last century', 'Next century'], // 60*60*24*7*4*12*100*2
                    [58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
                ];
                var seconds = (+new Date() - time) / 1000,
                    token = 'ago', list_choice = 1;

                if (seconds == 0) {
                    return 'Just now'
                }
                if (seconds < 0) {
                    seconds = Math.abs(seconds);
                    token = 'from now';
                    list_choice = 2;
                }
                var i = 0, format;
                while (format = time_formats[i++])
                    if (seconds < format[0]) {
                        if (typeof format[2] == 'string')
                            return format[list_choice];
                        else {
                            return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
                        }
                    }
                return time;
            }
        });

    return $.udeytech.instagram;
});
