// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

(function(){

    BaseModule = $.jClass({

        init: function() {
            // abstract
        },

        start: function() {
            // abstract
        },

        rollBack: function() {
            // abstract
            // means that all DOM changes should be deleted
            // because this network is disabled on server
        },

        getName: function() {
            // abstract
        },

        getUrlPattern: function() {
            // abstract
        },

        isModuleEnabled: function() {
            return 1 == YouhonestCore.DB.getSetting( this._getEnabledField() );
        },

        askLoginOnCurrentPage: function() {
            // abstract
        },

        disable: function(callback) {
            this.ajax({
                url: 'settings/disablenetwork',
                context: this,
                data: {
                    networkId: this.ID
                },
                complete: function() {
                    YouhonestCore.DB.setSetting( this._getEnabledField(), 0 );
                    callback();
                },
                // showl be defined here
                error: function() {}
            })
        },

        getLanguage: function() {
            return YouhonestCore.DB.getSetting('NetworkLanguage_' + this.ID);
        },

        setLanguage: function(lang) {
            YouhonestCore.DB.setSetting('NetworkLanguage_' + this.ID, lang);
        },

        ajax: function(params) {
            if (typeof params.type === 'undefined') {
                params.type = 'POST';
            }
            if (typeof params.dataType === 'undefined') {
                params.dataType = 'json';
            }
            params.data.networkId = this.ID;
            YouhonestCore.Ajax.call(params);
        },

        translate: function(key, replace) {
            return YouhonestCore.Translate.translate(key, replace);
        },

        // just alias
        t: function(key, replace) {
            return this.translate(key, replace);
        },

        _getEnabledField: function() {
            return 'NetworkEnabled_' + this.ID;
        }

    })
})();