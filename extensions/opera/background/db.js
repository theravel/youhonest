(function(){

    var self =
    YouhonestCore.DB = {

        _defaultSettings: {
            lastErrorDate: 0,
            lastNotificationDate: 0,
            NetworkEnabled_1: 1,
            NetworkEnabled_2: 1,
            NetworkEnabled_3: 1,
            NetworkLanguage_1: 'ru',
            NetworkLanguage_2: 'en',
            NetworkLanguage_3: 'en',
            auth_cookie: '_'
        },

        set: function(name, value) {
            localStorage[name] = value;
        },

        get: function(name) {
            if (typeof localStorage[name] !== 'undefined') {
                return localStorage[name];
            }
            if (typeof self._defaultSettings[name] !== 'undefined') {
                return self._defaultSettings[name];
            }
            throw 'Setting "' + name + '" is not supported';
        },

        getAll: function(params, callback) {
            var all = {};
            for (var i in self._defaultSettings) {
                all[i] = self.get(i);
            }
            callback(all);
        }
    }
    
})();