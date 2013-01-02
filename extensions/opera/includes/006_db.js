// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

(function(){

    var DB = $.jClass({

        _settings: {},

        start: function(callback) {
            oext.postMessage({
                action: 'getAllSettings',
                data: []
            });
            oext.onmessage = function(event) {
                if (event.data.action != 'getAllSettings') {
                    return;
                }
                self._settings = event.data.response;
                callback();
            }
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
                data: {
                    name: name,
                    value: value
                }
            }
            // We dont expect some response here
            oext.postMessage(params);
        }

    });

    var self = new DB();
    YouhonestCore.DB = self;

})();