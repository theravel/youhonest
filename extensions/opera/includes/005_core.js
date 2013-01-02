// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

(function(){

    YouhonestCore = {

        OVERLAY_ID: '_youhonest_overlay',
        DISABLE_ID: '_youhonest_network_disable',

        _modules: {},
        _language: 'ru',

        _loginShown: false,

        addModule: function(module) {
            this._modules[ module.getName() ] = module;
        },

        start: function() {
            var self = this;
            this.DB.start(function(){
                self._internalStart.call(self);
            });
        },

        startOptions: function() {
            var self = this;
            this.DB.start(function(){
                self._internalOptionsStart.call(self);
            });
        },

        _internalStart: function() {
            var module = this._getModuleByUrl();
            this._language = this.getLanguage( module );
            this.Translate.setLanguage(this._language);

            if (module.isModuleEnabled()) {
                module.start();
                this.Notifications.start();
            }
        },

        _internalOptionsStart: function() {
            var language = this.getLanguage();
            this._modules.options.start(language);
        },

        callLogin: function() {
            if (this._loginShown) {
                return;
            }

            try {
                var module = this._getModuleByUrl();
            } catch (e) {
                return;
            }
            if (!module.isModuleEnabled() || !module.askLoginOnCurrentPage()) {
                return;
            }

            this._loginShown = true;
            this.Ajax.call({
                url: 'index/login',
                type: 'POST',
                dataType: 'html',
                context: this,
                data: {
                    networkId: module.ID,
                    language: this.getLanguage()
                },
                success: function(data) {
                    var self = this;
                    var layout = $(data);
                    var overlay = $('<div/>')
                        .attr('id', this.OVERLAY_ID);
                    layout.find('#' + this.DISABLE_ID)
                        .data('network', module.ID)
                        .bind('click', function() {
                            self.Translate.setLanguage(self.getLanguage());
                            var confirmText = self.Translate.translate('are_you_sure_to_disable');
                            if (confirm(confirmText)) {
                                module.disable(function() {
                                    setTimeout(function(){
                                        window.location.reload();
                                    }, 400);
                                });
                            }
                        });
                    $('body').append(overlay, layout);
                    layout.center();
                }
            });
        },

        rollBackNetwork: function() {
            this._getModuleByUrl().rollBack();
        },

        getLanguage: function(network) {
            var browserLanguage = window.navigator.userLanguage || window.navigator.language;
            if (typeof network == 'undefined') {
                return browserLanguage;
            }
            var networkLanguage = network.getLanguage();
            if (networkLanguage) {
                network.setLanguage(networkLanguage);
                return networkLanguage;
            }
            network.setLanguage(browserLanguage);
            return browserLanguage;
        },

        _getModuleByUrl: function() {
            var url = window.location.host;
            for (var i in this._modules) {
                if ( this._modules[i].getUrlPattern().test(url) ) {
                    return this._modules[i];
                }
            }
            throw 'Module was not found';
        }

    }
})();