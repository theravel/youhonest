(function() {

    var VkontakteModule = BaseModule.extend({

        ID: 1,
        // this post was already explored
        MARKED_CLASS: '_youhonest_marked',
        // every dislike has such wrapper
        DISLIKE_WRAPPER_CLASS: '_youhonest_dislike_wrapper',
        // how often you will lookup for DOM hanges
        UPDATE_INTERVAL: 500,
        // dislike has your dislike
        DISLIKE_ENABLED_CLASS: '_youhonest_selected',
        // this means that comment contains dislike, but not your
        DISLIKE_NOT_EMPTY_COMMENT_CLASS: '_youhonest_not_empty_comment',

        _tablesCount: 0,
        _videoId: null,
        _photoId: null,
        _expandedPostId: null,
        _timeoutIntervalId: null,

        getName: function() {
            return 'Vkontakte';
        },

        getUrlPattern: function() {
            return /vk.com/;
        },

        askLoginOnCurrentPage: function() {
            if (/(oauth.vk.com\/oauth\/authorize)|(vk.com\/login.php)/.test(window.location)) {
                return false;
            }
            if ($('#quick_login_form').length) {
                return false;
            }
            return true;
        },

        start: function() {
            // call first time (because setInterval creates a delay)
            this._ajaxListener();

            this._importHoverPopup();
            
            this._timeoutIntervalId = setInterval((function(self) {
                return function() {
                    self._ajaxListener(); 
                }
            })(this), this.UPDATE_INTERVAL);
        },

        rollBack: function() {
            clearInterval(this._timeoutIntervalId);
            this._removeIcons();
        },

        _ajaxListener: function() {
            var ids = this._processPosts();
            
            var expandedPostId = this._processExpandedPosts();
            if (expandedPostId) {
               ids.push(expandedPostId);
            }

            var photoId = this._processPhoto();
            if (photoId) {
                ids.push(photoId);
            }

            var videoId = this._processVideo();
            if (videoId) {
                ids.push(videoId);
            }

            if (!ids.length) {
                return;
            }
            
            this.ajax({
                url: 'network/getdislikes',
                data: {
                    postIds: ids.join(',')
                },
                context: this,
                success: function(data) {
                    self._fillDislikes(data.dislikes);
                }
            });
            
        },

        _processPosts: function() {
            var tables = $('.post, .reply, .fw_reply');
            var length = tables.length;
            if (this._tablesCount !== length) {
                this._tablesCount = length;
                var elements = tables.not( '.' + this.MARKED_CLASS );
                // just create icons and bind events
                this._createIcons(elements);
                // then get real dislikes (here is a normal delay)
                return elements.map(function() {
                    return this.id;
                }).get();
            }
            return [];
        },
        
        _processExpandedPosts: function() {
            if ($('#fw_post_wrap').length && /wall-?\d+_\d+/.test(window.location.href)) {
                var postId = 'post' + /-?\d+_\d+/.exec(window.location.href)[0];
                if ($('#' + postId + ' .' + VkontakteDOM.getElementsClass()).length == 0) {
                    this._expandedPostId = postId;
                    VkontakteDOM.setExpandedPostId(postId);
                    var dislikeButton = VkontakteDOM.getExpandedPostButton(
                        this.t('dislike'),
                        this._expandedPostClickAction,
                        postId
                    );
                    $('.fw_post_table .fw_like_wrap').before(dislikeButton);
                    return postId;
                }
            } else {
                this._expandedPostId = null;
            }
            return null;
        },

        _processPhoto: function() {
            if ($('#pv_box').is(':visible') && /photo-?\d+_\d+/.test(window.location.href)) {
                var photoId = /photo-?\d+_\d+/.exec(window.location.href)[0];
                if ($('#' + photoId + ' .' + VkontakteDOM.getElementsClass()).length == 0) {
                    this._photoId = photoId;
                    VkontakteDOM.setPhotoId(photoId);
                    var dislikeButton = VkontakteDOM.getPhotoButton(
                        this.t('dislike'),
                        this._layerClickAction,
                        photoId
                    );
                    $('#pv_like_wrap').before(dislikeButton);
                    return photoId;
                }
            } else {
                this._photoId = null;
            }
            return null;
        },

        _processVideo: function() {
            if ($('#mv_box').is(':visible') && /video-?\d+_\d+/.test(window.location.href)) {
                var videoId = /video-?\d+_\d+/.exec(window.location.href)[0];
                if ($('#' + videoId + ' .' + VkontakteDOM.getElementsClass()).length == 0) {
                    this._videoId = videoId;
                    VkontakteDOM.setVideoId(videoId);
                    var dislikeButton = VkontakteDOM.getVideoButton(
                        this.t('dislike'),
                        this._layerClickAction,
                        videoId
                    );
                    $('#mv_like_wrap').before(dislikeButton);
                    return videoId;
                }
            } else {
                this._videoId = null;
            }
            return null;
        },

        _createIcons: function(elements) {
            elements.addClass( this.MARKED_CLASS );
            elements.filter('.reply')
                .on('mouseover', this._showCommentButton)
                .on('mouseout', this._hideCommentButton);
            
            var dislikePost = VkontakteDOM.getPostButton(
                this.t('dislike'),
                this._postClickAction
            );
                
            var dislikeHidden = VkontakteDOM.getCommentButton(
                this.t('dislike'),
                this._commentClickAction
            );

            var expandedComment = VkontakteDOM.getExpandedPostButton(
                this.t('dislike'),
                this._commentClickAction
            );

            // post in the wall or feed
            elements.find('.post_like').after(dislikePost);
            // post in comments
            elements.filter('.reply').find('.like_wrap').after(dislikeHidden);
            // comments in expanded view
            elements.filter('.fw_reply').find('.like_wrap').before(expandedComment);
        },

        _removeIcons: function() {
            var className = VkontakteDOM.getElementsClass();
            $('.' + className).remove();
            YouhonestCore.VkontakteHover.removeBlocks();
        },

        _hoverAction: function(event) {
            event.stopPropagation();
            return false;
        },

        _postClickAction: function() {
            var enabled = $(this).hasClass(self.DISLIKE_ENABLED_CLASS) ? 1 : 0;
            var postId = $(this).parents('.post').attr('id');

            self.ajax({
                url: 'network/dislike',
                context: this,
                data: {
                    postId: postId,
                    // 1 if it was already disliked, 0 otherwise
                    cancel: enabled
                },
                success: function(data) {
                    self._updateDislikesCount(enabled, this, '.post_like_count', data, postId);
                }
            })
        },

        _expandedPostClickAction: function() {
            var enabled = $(this).hasClass(self.DISLIKE_ENABLED_CLASS) ? 1 : 0;
            var postId = $(this).parents('.fw_post_table').attr('id');

            self.ajax({
                url: 'network/dislike',
                context: this,
                data: {
                    postId: postId,
                    // 1 if it was already disliked, 0 otherwise
                    cancel: enabled
                },
                success: function(data) {
                    self._updateDislikesCount(enabled, this, '.fw_like_count', data, postId);
                }
            })
        },

        _updateDislikesCount: function(enabled, element, counterSelector, data, postId) {
            element = $(element);
            if (enabled) {
                element.removeClass(self.DISLIKE_ENABLED_CLASS);
            } else {
                element.addClass(self.DISLIKE_ENABLED_CLASS);
            }
            var countText = data.people.dislikes ? data.people.count : '';
            element.find(counterSelector).text(countText);

            self._bindHoverEvents(element, postId);
            element.data('dislikes', data.people.dislikes ? data.people.dislikes : null);
            element.data('dislikesCount', data.people.dislikes ? data.people.count : 0);
            if (!data.people.dislikes) {
                self._dislikeMouseOut.call(element);
            } else {
                self._dislikeMouseOver.call(element);
            }
        },

        _layerClickAction: function() {
            var enabled = $(this).hasClass(self.DISLIKE_ENABLED_CLASS) ? 1 : 0;
            var postId = $(this).data('contentId');

            self.ajax({
                url: 'network/dislike',
                context: this,
                data: {
                    postId: postId,
                    // 1 if it was already disliked, 0 otherwise
                    cancel: enabled
                },
                success: function(data) {
                    var selector = '.' + VkontakteDOM.getLayerCounterClass();
                    self._updateDislikesCount(enabled, this, selector, data, postId);
                }
            })
        },

        _commentClickAction: function(event) {
            event.stopPropagation();
            var enabled = $(this).hasClass(self.DISLIKE_ENABLED_CLASS) ? 1 : 0;
            var postId = $(this).parents('.reply, .fw_reply').attr('id');

            self.ajax({
                url: 'network/dislike',
                context: this,
                data: {
                    postId: postId,
                    // 1 if it was already disliked, 0 otherwise
                    cancel: enabled
                },
                success: function(data) {
                    self._updateDislikesCount(enabled, this, '.like_count, .fw_like_count', data, postId);
                    if (0 === data.people.length) {
                        $(this).removeClass(self.DISLIKE_NOT_EMPTY_COMMENT_CLASS);
                    }
                }
            })
        },

        _showCommentButton: function() {
            $(this).find('.like_link, .like_count')
                .css('opacity', 1);
            $(this).find('.like_icon').css('opacity', 0.4);
        },

        _hideCommentButton: function() {
            $(this).find('.' + self.DISLIKE_WRAPPER_CLASS).find('.like_link, .like_icon, .like_count')
                .css('opacity', 0);
        },

        _fillDislikes: function(dislikes) {
            for (var postId in dislikes) {
                var element = $('#' + postId).find('.' + self.DISLIKE_WRAPPER_CLASS).first();
                self._bindHoverEvents(element, postId);
                element
                    .data('dislikes', dislikes[postId].dislikes)
                    .data('dislikesCount', dislikes[postId].count)
                    .find('.post_like_count, .like_count, .fw_like_count, .' + VkontakteDOM.getLayerCounterClass())
                    .text(dislikes[postId].count);
                element
                    .find('.like_count')
                    .parent()
                    .addClass(self.DISLIKE_NOT_EMPTY_COMMENT_CLASS);
                if (dislikes[postId].hasMy) {
                    element.addClass(self.DISLIKE_ENABLED_CLASS);
                }
            }
        },

        _bindHoverEvents: function(element, postId) {
            element
                .data('postId', postId)
                .unbind('mouseenter')
                .unbind('mouseleave')
                .bind('mouseenter', self._dislikeMouseOver)
                .bind('mouseleave', self._dislikeMouseOut)
        },

        _dislikeMouseOver: function() {
            var element = $(this);
            var postId = element.data('postId');
            var people = element.data('dislikes');
            var count = element.data('dislikesCount');
            if (typeof people !== 'object') {
                return;
            }            
            // Due to server issues, we can have 7 elements here, but we really need only 6
            people = people.slice(0, 6);
            var position = element.offset();
            YouhonestCore.VkontakteHover.setPeople(people, postId, count);
            YouhonestCore.VkontakteHover.show(position.left, position.top);
        },

        _dislikeMouseOut: function() {
            YouhonestCore.VkontakteHover.hide();
        },

        _hoverPopupClick: function(postId) {
            self._extendedPaginationClick(postId, 0);
        },

        _extendedPaginationClick: function(postId, pageNum) {
            self.ajax({
                url: 'network/getdislikesbypost',
                context: self,
                data: {
                    postId: postId,
                    page: pageNum
                },
                success: function(data) {
                    if (!data.dislikes || !data.dislikes.dislikes) {
                        return;
                    }
                    YouhonestCore.VkontakteHover.showPeopleExtended(
                        postId,
                        data.dislikes.dislikes,
                        pageNum,
                        data.dislikes.count,
                        self._extendedPaginationClick
                    );
                }
            });
        },

        _importHoverPopup: function() {
            YouhonestCore.VkontakteHover.insertEmptyBlock(self._hoverPopupClick);
        }

    });

    var self = new VkontakteModule();

    YouhonestCore.addModule(self);

})();