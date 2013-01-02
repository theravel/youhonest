// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

(function(){

    VkontakteHover = $.jClass({

        HOVER_BLOCK_ID: '_youhonest_hover',
        DISLIKE_TITLE: '_youhonest_dislike_title',
        DISLIKE_TABLE: '_youhonest_disliked_people',
        MARGIN_TOP: 7,

        _hover: null,
        _popup: null,
        _hideTimeout: null,
        _externalClickAction: null,

        _html: '<div class="tt rich like_tt bottom" id="_youhonest_hover" style="position: absolute; opacity: 1; display: none; top: 0;"> <table cellspacing="0" cellpadding="0" class="toup1"> <tbody> <tr> <td colspan="3" class="tt_top"> <div class="top_pointer" style="margin-left: 26px;"></div> </td> </tr> <tr> <td class="side_sh"></td> <td class="outer"> <table cellspacing="0" cellpadding="0"> <tbody> <tr> <td class="side_sh"></td> <td class="wrapped"> <div class="header"> <div class="like_head_wrap"> <span id="_youhonest_dislike_title">{%s}</span> </div> </div> <div class="wrap"> <div class="content"> <div class="hider"> <table cellspacing="0" cellpadding="0" id="_youhonest_disliked_people" class="like_stats" style="left: 0px"> <tbody> <tr></tr> </tbody> </table> </div> </div> </div> </td> <td class="side_sh"></td> </tr> <tr> <td colspan="3"> <div class="bottom_sh"></div> </td> </tr> </tbody> </table> </td> <td class="side_sh"></td> </tr> <tr> <td colspan="3" class="tt_bottom"> <div class="bottom_sh"></div> <div class="bottom_pointer" style="margin-left: 26px;"></div> </td> </tr> </tbody> </table> </div>',

        _item: '<td><a title="{}" href="{}" class="like_tt_usr" onclick="return nav.go(this, event);"><img class="like_tt_stats_photo" src="{}" width="30" height="30"></a></td>',

        _extendedHtml: '<div class="popup_box_container" style="width: 478px; height: auto; margin-top: 10px; "><div class="box_layout" onclick="__bq.skip=true;"><div class="box_title_wrap" style=""><div class="box_x_button"></div><div class="box_title">{%s}</div></div><div class="box_body" style="display: block; padding: 0px; "><div class="tabbed_box"> <div class="tabbed_sh tabbed_sh1"></div> <div class="tabbed_sh tabbed_sh2"></div> <div class="tabbed_sh tabbed_sh3"></div> <div class="tabbed_sh tabbed_sh4"></div> <div class="tabbed_container clear_fix " style="height:310px;"><div style="padding: 7px 5px 5px;"><div class="fl_r" style="padding:0 5px;width:200px;"><ul class="page_list fl_r"> <li class="current">1</li> <li><a>2</a></li> <li><a>3</a></li></ul></div><h4 style="border-bottom: 1px solid #DAE1E8;margin:0 5px 10px;padding:5px 0 2px;">{%s}</h4><table cellpadding="0" cellspacing="0"><tbody></tbody></table></div></div> <div class="tabbed_sh tabbed_sh4"></div> <div class="tabbed_sh tabbed_sh3"></div> <div class="tabbed_sh tabbed_sh2"></div> <div class="tabbed_sh tabbed_sh1"></div> <div class="tabbed_sh tabbed_sh0"></div></div></div><div class="box_controls_wrap" style="display: block; "><div class="box_controls"><table cellspacing="0" cellpadding="0" class="fl_r"><tbody><tr><td><div class="button_blue"><button>{%s}</button></div></td></tr></tbody></table><div class="progress" id="box_progress1"></div><div class="box_controls_text"></div></div></div></div></div>',

        _extendedItem: '<td><div class="liked_box_row"><div class="liked_box_thumb"><a href="{}" onclick="return nav.go(this, event)"><img width="50" height="50" src=""></a></div><div style="text-align: center;"><a href="{}" onclick="return nav.go(this, event)">{}</a></div></div></td>',

        insertEmptyBlock: function(clickAction) {
            this._externalClickAction = clickAction;
            $('body').append($(this._html));
            this._hover = $('#' + this.HOVER_BLOCK_ID)
                .bind('click', this._clickAction)
                .bind('mouseenter', this._mouseEnter)
                .bind('mouseleave', this._mouseOut);

            var tooltips = $('<link/>')
                .attr('type', 'text/css')
                .attr('rel', 'stylesheet')
                .attr('href', '/css/al/tooltips.css');

            var boxes = $('<link/>')
                .attr('type', 'text/css')
                .attr('rel', 'stylesheet')
                .attr('href', '/css/al/boxes.css');

            $('head').append(tooltips, boxes);
        },

        removeBlocks: function() {
            this._hover.remove();
        },

        setPeople: function(people, postId, count) {
            var title = YouhonestCore.Translate.translate('n_people_like_this', {N: count});
            this._hover
                .data('postId', postId)
                .data('dislikesCount', count)
                .find('#' + this.DISLIKE_TITLE).text(title);
            var tr = this._hover.find('#' + this.DISLIKE_TABLE + ' tr').empty();

            for (var i = 0; i < people.length; i++) {
                var item = $(this._item);
                item.find('a')
                    .attr('title', people[i].name)
                    .attr('href', '/id' + people[i].id);
                item.find('img')
                    .attr('src', people[i].photo);
                tr.append(item);
            }
        },

        showPeopleExtended: function(postId, people, pageNum, count, paginationClick) {
            this._createExtendedPopup(postId, pageNum, count, paginationClick);

            var tr = null;
            var table = this._popup.find('.box_body table');
            for (var i = 0; i < people.length; i++) {
                if (i % 8 == 0) {
                    if (tr !== null) {
                        table.append(tr);
                    }
                    tr = $('<tr/>');
                }

                var item = $(this._extendedItem);
                item.find('a')
                    .attr('href', '/id' + people[i].id);
                item.find('a:last')
                    .text(people[i].name);
                item.find('img')
                    .attr('src', people[i].photo);
                tr.append(item);
            }
            table.append(tr);
        },

        show: function(left, top) {
            clearTimeout(this._hideTimeout);
            this._hover
                .css({
                    top: top - this._hover.height() - this.MARGIN_TOP + 'px',
                    left: left + 'px'
                })
                .fadeIn(300);
        },

        hide: function() {
            this._hideTimeout = setTimeout(function(){
                self._hover.fadeOut(300);
            }, 200);
        },

        _mouseEnter: function() {
            clearTimeout(self._hideTimeout);
        },

        _mouseOut: function() {
            self.hide();
        },

        _clickAction: function() {
            var postId = $(this).data('postId');
            var count = $(this).data('dislikesCount');
            if (count <= 6) {
                return;
            }
            self.hide();
            self._externalClickAction(postId);
        },

        _createExtendedPopup: function(postId, pageNum, count, paginationClick) {
            if (this._popup) {
                this._popup.remove();
            }
            this._popup = $(this._extendedHtml)
                .data('postId', postId);
            this._popup.find('.box_title')
                .text(YouhonestCore.Translate.translate('people_who_like_this'));
            this._popup.find('h4')
                .text(YouhonestCore.Translate.translate('n_people_like_this', {N: count}));
            this._popup.find('button')
                .text(YouhonestCore.Translate.translate('close'));
            this._popup.find('.box_x_button, button')
                .bind('click', function() {
                    self._popup.remove();
                    $('#box_layer_wrap, #box_layer_bg').hide();
                });
            this._createPagination(pageNum, count, paginationClick);

            $('#box_layer').append(this._popup);
            $('#box_layer_wrap, #box_layer_bg').show();

            this._popup.center();
        },

        _createPagination: function(pageNum, count, paginationClick) {
            var a, li;
            var ul = this._popup.find('.page_list').empty();
            var pageCount = Math.ceil(count / 24);

            if (pageNum > 2) {
                li = $('<li/>');
                a = $('<a/>').html('&laquo;').data('num', 0);
                li.append(a);
                ul.append(li);
            }
            for (var i = pageNum - 1; i < pageNum + 4; i++) {
                if (i <= 0 || i > pageCount) {
                    continue;
                }
                li = $('<li/>');
                if (i == pageNum + 1) {
                    li.text(i).addClass('current');
                } else {
                    a = $('<a/>').text(i).data('num', i - 1);
                    li.append(a);
                }
                ul.append(li);
            }
            if (pageNum < pageCount - 3) {
                li = $('<li/>');
                a = $('<a/>').html('&raquo;').data('num', pageCount - 1);
                li.append(a);
                ul.append(li);
            }

            ul.find('a').bind('click', function(){
                var num = $(this).data('num');
                var postId = self._popup.data('postId');
                paginationClick(postId, num);
            });
        }

    });

    var self = new VkontakteHover();
    YouhonestCore.VkontakteHover = self;

})();