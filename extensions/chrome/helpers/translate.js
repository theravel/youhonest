YouhonestCore.Translate = {

    DEFAULT_LANGUAGE: 'ru',

    Languages: [],

    _lang: null,

    setLanguage: function(lang) {
        this._lang = lang;
    },

    getLanguages: function() {
        var result = [];
        for (var i in this.Languages) {
            result.push({
                code: i,
                name: this.Languages[i].name
            });
        }
        return result;
    },

    translate: function(key, replace) {
        var lang = this._lang;
        if (typeof this.Languages[lang] == 'undefined') {
            var langParts = lang.replace('_', '-').split('-');
            if (langParts.length == 2) {
                lang = langParts[0];
            }
            if (typeof this.Languages[lang] == 'undefined') {
                lang = this.DEFAULT_LANGUAGE;
            }
        }
        var value = this.Languages[ lang ][ key ];

        if (typeof value == 'undefined') {
            throw 'Translation not found for "' + key + '"';
        }
        
        if (replace) {
            for (var i in replace) {
                value = value.replace('_' + i + '_', replace[i]);
            }
        }

        return value;
    }

}