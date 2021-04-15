/*
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'Udeytech_Instagram/js/scroll',
    'Udeytech_Instagram/lightbox2/js/lightbox'
], function ($) {
    $(function () { // to ensure that code evaluates on page load
        InstagramUsers = {
            _waiting: [],
            _users: [],
            getUser: function (uid) {
                return this._users[uid] ? this._users[uid] : null;
            },
            setUser: function (uid, user) {
                this._users[uid] = user;
            },
            loadUser: function (uid, path, element, callback) {
                if (this._waiting[uid] && this._waiting[uid].length) {
                    this._waiting[uid].push({
                        el: element, fn: callback
                    });
                } else {
                    this._waiting[uid] = [{
                        el: element, fn: callback
                    }];
                    var that = this;
                    $.get(path,
                        function (result) {
                            that.setUser(uid, result.graphql.shortcode_media.owner);
                            while (that._waiting[uid].length) {
                                var listener = that._waiting[uid].pop();
                                listener.fn(that.getUser(uid), listener.el);
                            }
                        }
                    ).fail(function () {
                        while (that._waiting[uid].length) {
                            var listener = that._waiting[uid].pop();
                            listener.fn(that.getUser(uid), listener.el);
                        }
                    });
                }
            }
        };
        $.udeytech.lightbox();
    });
});
