(function() {

    var Ajax = $.jClass({

        DISPLAY_INTERVAL: 1800, //sec
        ERROR_WINDOW_ID: '_youhonest_notification_window',
        ERROR_WINDOW_CLASS: '_youhonest_error',
        CLOSE_ID: '_youhonest_close_button',

        _errorShown: false,
        _displayInterval: null,

        setErrorDisplayTimeout: function(timeout) {
            this._displayInterval = timeout;
        },

        call: function(params){
            var self = this;
            if (this._displayInterval === null) {
                this._displayInterval = this.DISPLAY_INTERVAL;
            }
            params.url = YouhonestConfig.URL + '/' + params.url;
            if (!params.error) {
                params.error = this._connectionError;
            }
            var successCallback = params.success;
            params.success = function(data){
                self._connectionSuccess();
                if (typeof successCallback == 'function') {
                    successCallback.call(this, data);
                }
            }
            $.ajax(params);
        },

        _connectionError: function(jqXHR, textStatus, errorThrown) {
            switch (jqXHR.status) {
                case 401: // needs authorization
                    YouhonestCore.callLogin.call(YouhonestCore);
                    return;
                case 405: // network is disabled on server
                    YouhonestCore.rollBackNetwork.call(YouhonestCore);
                    return;
            }

            if (self._errorShown) {
                return;
            }
            var now = Math.round(new Date().getTime() / 1000);
            var lastDate = self._getLastErrorDate();
            if (now - lastDate < self._displayInterval) {
                return;
            }
            
            self._errorShown = true;
            var text = YouhonestCore.Translate.translate('connection_error');
            var errorClose = $('<div/>')
                .attr('id', self.CLOSE_ID)
                .html( YouhonestCore.Translate.translate('close_notification') )
                .on('click', function() {
                    $('#' + self.ERROR_WINDOW_ID).fadeOut(500, function() {
                        $(this).remove();
                    });
                });
            var errorWindow = $('<div/>')
                .attr('id', self.ERROR_WINDOW_ID)
                .addClass(self.ERROR_WINDOW_CLASS);
            errorWindow
                .append('<div id="_youhonest_notification_top"></div>')
                .append('<div id="_youhonest_notification_middle"></div>')
                .append('<div id="_youhonest_notification_bottom"></div>');
            errorWindow.find('#_youhonest_notification_middle')
                .html(text);
            errorWindow.append(errorClose);
            $('body').append(errorWindow);
            setTimeout(function(){
                errorWindow.fadeIn(800);
            }, 500);
            self._rememberLastErrorDate(now);
        },

        _connectionSuccess: function() {
            if (this._errorShown) {
                $('#' + this.ERROR_WINDOW_ID).hide(300, function() {
                    $(this).remove();
                    self._errorShown = false;
                });                
            }
        },

        _getLastErrorDate: function() {
            return YouhonestCore.DB.getSetting('lastErrorDate');
        },

        _rememberLastErrorDate: function(date) {
            YouhonestCore.DB.setSetting('lastErrorDate', date);
        }
    });

    var self = new Ajax();
    YouhonestCore.Ajax = self;

})();