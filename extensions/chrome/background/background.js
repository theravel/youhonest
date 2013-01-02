(function() {

    var DB = {

        _started: false,
        _store: null,
        _callback: null,

        // defaults
        _settings: {
            lastErrorDate: 0,
            lastNotificationDate: 0,
            NetworkEnabled_1: 1,
            NetworkEnabled_2: 1,
            NetworkEnabled_3: 1,
            NetworkLanguage_1: 'ru',
            NetworkLanguage_2: 'en',
            NetworkLanguage_3: 'en'
        },

        start: function(callback) {
            if (this._started) {
                callback();
                return;
            }
            for (var i in this._settings) {
                // mark settings as not selected from DB
                this._settings[i] = [this._settings[i], false];
            }
            this._started = true;
            this._callback = callback;
            this._initStore();
        },

        getSetting: function(name) {
            if (typeof this._settings[name] == 'undefined') {
                throw 'Setting is not suppoted';
            }
            return this._settings[name][0];
        },

        getSettings: function() {
            var result = {};
            for (var i in this._settings) {
                result[i] = this._settings[i][0];
            }
            return result;
        },

        setSetting: function(name, value) {
            if (typeof this._settings[name] == 'undefined') {
                throw 'Setting is not suppoted';
            }
            value += '';
            var update = this._settings[name][1];
            this._settings[name] = [value, true];
            this._store.transaction(function(tx) {
                if (update) {
                    tx.executeSql(
                        'UPDATE youhonest_settings SET value = ? WHERE name = ?',
                        [value, name]
                    );
                } else {
                    tx.executeSql(
                        'INSERT INTO youhonest_settings VALUES(?, ?)',
                        [name, value]
                    );
                }
            });
        },

        _processSettings: function(tx, result){
            for (var i = 0; i < result.rows.length; i++) {
                var name = result.rows.item(i)['name'];
                var value = result.rows.item(i)['value'];
                DB._settings[name] = [value, true];
            }
            DB._callback();
        },

        _initStore: function(){
            try {
                if (window.openDatabase) {
                    var dbSize = 5 * 1024 * 1024; // 5MB
                    this._store = openDatabase('YouhonestExtension', '1.0', 'Youhonest LDB', dbSize);
                    if (!this._store) {
                        console.error('Failed to open local DB');
                    } else {
                        this._store.onError = function(tx, err){
                            console.error('Cannot execute DB query', tx, err);
                        };
                        this._store.transaction(function(tx) {
                            tx.executeSql(
                                'CREATE TABLE IF NOT EXISTS youhonest_settings (name TEXT, value TEXT);',
                                []
                            );
                        });
                        this._store.transaction(function(tx) {
                            tx.executeSql(
                                'SELECT * FROM youhonest_settings',
                                [],
                                DB._processSettings,
                                DB._store.onError
                            );
                        });
                    }
                } else {
                    console.error('OpenDB does not exist');
                }
            } catch(e) {
                console.error('DB error', e);
            }
        }

    };

    chrome.extension.onMessage.addListener(
        function(request, sender, sendResponse) {
            switch (request.action) {
                case 'getAllSettings':
                    DB.start(function(){
                        var settings = DB.getSettings();
                        sendResponse({settings: settings});
                    });
                    break;
                case 'setSetting':
                    DB.setSetting(request.name, request.value);
            }
            return true;
        }
    );

})();