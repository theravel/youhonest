(function() {

    var DB = $.jClass({

        _settings: {},

        start: function(callback) {
            chrome.extension.sendMessage({action: 'getAllSettings'}, function(response) {
                self._settings = response.settings;
                callback();
            });
        },

        getSetting: function(name) {
            if (typeof this._settings[name] == 'undefined') {
                throw 'Setting is not suppoted';
            }
            return this._settings[name];
        },

        setSetting: function(name, value) {
            var params = {
                action: 'setSetting',
                name: name,
                value: value
            }
            // We dont expect some response here
            chrome.extension.sendMessage(params, function(){});
        }
        
    });

    var self = new DB();
    YouhonestCore.DB = self;

})();