// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

(function(){
    
    VkontakteDOM = {

        DISLIKE_WRAPPER_CLASS: '_youhonest_dislike_wrapper',
        LAYER_WRAPPER_CLASS: '_youhonest_dislike_layer',
        LAYER_COUNTER: '_youhonest_layer_counter',

        getPostButton: function(text, clickHandler) {
            var dislikeTemplate = this._getDislikeTemplate();

            var postLikeLink = $('<span/>')
                .addClass('post_like_link fl_l')
                .html(text);

            var postLikeIcon = $('<i/>')
                .addClass('post_like_icon fl_l');

            var postLikeCount = $('<span/>')
                .addClass('post_like_count fl_l');

            return dislikeTemplate
                .clone()
                .addClass('post_like')
                .bind('click', clickHandler)
                .append(postLikeLink, postLikeIcon, postLikeCount);
        },

        getCommentButton: function(text, clickHandler) {
            var dislikeTemplate = this._getDislikeTemplate();

            var commentLikeLink = $('<span/>')
                .addClass('like_link fl_l')
                .html(text);

            var commentLikeIcon = $('<i/>')
                .addClass('like_icon fl_l')
                .css('opacity', 0);

            var commentLikeCount = $('<span/>')
                .addClass('like_count fl_l');

            return dislikeTemplate
                .clone()
                .addClass('like_wrap')
                .bind('click', clickHandler)
                .append(commentLikeLink, commentLikeIcon, commentLikeCount);
        },

        getVideoButton: function(text, clickHandler, contentId) {
            var dislikeTemplate = this._getDislikeTemplate(true);

            var videoLikeLink = $('<span/>')
                .addClass('fl_l')
                .html(text);

            var videoLikeIcon = $('<i/>')
                .addClass('fl_l');

            var videoLikeCount = $('<span/>')
                .addClass('fl_l ' + this.LAYER_COUNTER);

            return dislikeTemplate
                .clone()
                .addClass('mv_like_wrap ' + this.LAYER_WRAPPER_CLASS)
                .attr('id', 'mv_like_wrap')
                .data('contentId', contentId)
                .bind('click', clickHandler)
                .append(videoLikeLink, videoLikeIcon, videoLikeCount);
        },

        getPhotoButton: function(text, clickHandler, contentId) {
            var dislikeTemplate = this._getDislikeTemplate(true);

            var photoLikeLink = $('<span/>')
                .addClass('fl_l')
                .html(text);

            var photoLikeIcon = $('<i/>')
                .addClass('fl_l');

            var photoLikeCount = $('<span/>')
                .addClass('fl_l ' + this.LAYER_COUNTER);

            return dislikeTemplate
                .clone()
                .addClass('pv_like_wrap ' + this.LAYER_WRAPPER_CLASS)
                .attr('id', 'pv_like_wrap')
                .data('contentId', contentId)
                .bind('click', clickHandler)
                .append(photoLikeLink, photoLikeIcon, photoLikeCount);
        },

        getExpandedPostButton: function(text, clickHandler) {
            var dislikeTemplate = this._getDislikeTemplate(true);

            var postLikeLink = $('<span/>')
                .addClass('fw_like_link fl_l')
                .html(text);

            var postLikeIcon = $('<i/>')
                .addClass('fw_like_icon fl_l');

            var postLikeCount = $('<span/>')
                .addClass('fw_like_count fl_l');

            var divider = $('<span/>')
                .addClass('divide')
                .html('|');

            return dislikeTemplate
                .clone()
                .addClass('fw_like_wrap')
                .bind('click', clickHandler)
                .append(postLikeLink, postLikeIcon, postLikeCount, divider);
        },

        setVideoId: function(videoId) {
            $('#mv_box').parent().attr('id', videoId);
        },

        setPhotoId: function(photoId) {
            $('#pv_wide').parent().attr('id', photoId);
        },

        setExpandedPostId: function(postId) {
            $('.fw_post_table').attr('id', postId);
        },

        getLayerCounterClass: function() {
            return this.LAYER_COUNTER;
        },

        getElementsClass: function() {
            return this.DISLIKE_WRAPPER_CLASS;
        },

        _getDislikeTemplate: function(useOnlyCss) {
            var element = $('<div/>')
                .addClass( this.DISLIKE_WRAPPER_CLASS );
            if (!useOnlyCss) {
                element.css({
                    'float': 'right',
                    'position': 'static'
                });
            }
            return element;
        }

    }
})();