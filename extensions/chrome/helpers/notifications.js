(function() {

    var Notifications = $.jClass({

        UPDATE_INTERVAL: 3600, // sec
        WINDOW_ID: '_youhonest_notification_window',
        CLOSE_ID: '_youhonest_close_button',

        start: function() {            
            var now = Math.round(new Date().getTime() / 1000);
            var lastDate = this._getLastNotificationDate();
            if (now - lastDate > this.UPDATE_INTERVAL) {
                YouhonestCore.Ajax.call({
                    url: 'news/news',
                    data: {
                        'language': YouhonestCore.getLanguage()
                    },
                    context: this,
                    dataType: 'json',
                    success: function(data) {
                        if (data.news) {
                            this._showNotification(data.news);
                        } else {
                            this._rememberLastNotificationDate(now);
                        }
                    }
                })
            }
        },

        _showNotification: function(notification) {
            var chromeHtml = $(notification.content).find('#chrome');
            chromeHtml.find('script').remove();
            var content = $.trim(chromeHtml.html());
            if (content == '') {
                return;
            }
            
            var notifyClose = $('<div/>')
                .attr('id', this.CLOSE_ID)
                .html( YouhonestCore.Translate.translate('close_notification') )
                .on('click', function() {
                    $('#' + self.WINDOW_ID).fadeOut(500, function() {
                        $(this).remove();
                    });
                });
            var notifyWindow = $('<div/>')
                .attr('id', this.WINDOW_ID);
            notifyWindow
                .append('<div id="_youhonest_notification_top"></div>')
                .append('<div id="_youhonest_notification_middle"></div>')
                .append('<div id="_youhonest_notification_bottom"></div>');
            notifyWindow.find('#_youhonest_notification_middle')
                .html(content);
            notifyWindow.append(notifyClose);
            $('body').append(notifyWindow);
            setTimeout(function(){
                notifyWindow.fadeIn(800);
            }, 1000);
        },

        _getLastNotificationDate: function() {
            return YouhonestCore.DB.getSetting('lastNotificationDate');
        },

        _rememberLastNotificationDate: function(date) {
            YouhonestCore.DB.setSetting('lastNotificationDate', date);
        }

    });

    var self = new Notifications();
    YouhonestCore.Notifications = self;

})();