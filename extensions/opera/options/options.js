(function() {

    var OptionsModule = BaseModule.extend({

        start: function(language) {
            YouhonestCore.Ajax.setErrorDisplayTimeout(0);
            YouhonestCore.Translate.setLanguage(language);
            this._initWordings();
            this.ajax({
                url: 'index/networks',
                data: {
                    language: YouhonestCore.getLanguage()
                },
                context: this,
                success: this._showNetworks
            })
        },

        getName: function() {
            return 'options';
        },

        getUrlPattern: function() {
            return /0/;
        },

        isModuleEnabled: function() {
            return true;
        },

        askLoginOnCurrentPage: function() {
            return false;
        },

        ajax: function(params) {
            if (typeof params.type === 'undefined') {
                params.type = 'POST';
            }
            if (typeof params.dataType === 'undefined') {
                params.dataType = 'json';
            }
            YouhonestCore.Ajax.call(params);
        },

        _initWordings: function() {
            $('title').text(this.t('settings_title'));
            $('.header h2').text(this.t('settings_header_settings'));
            $('.header h1').text(this.t('product'));
            $('#no_networks').text(this.t('no_networks_available'));
            $('#save_btn').text(this.t('settings_save'));
            $('#cancel_btn').text(this.t('settings_reset'));
        },

        _showNetworks: function(data) {
            if (0 === data.networks.length) {
                return;
            }

            var settings = $('.settings');
            var languages = YouhonestCore.Translate.getLanguages();
            $('#no_networks').remove();

            var template = $('.hidden .network-row');
            var select = template.find('select').empty();
            for (var j = 0; j < languages.length; j++) {
                select.append('<option value="' + languages[j].code + '">' + languages[j].name + '</option>');
            }

            for (var i in data.networks) {
                var network = data.networks[i];

                // override server settings by my
                network.enabled = parseInt(YouhonestCore.DB.getSetting('NetworkEnabled_' + network.id));
                network.language = YouhonestCore.DB.getSetting('NetworkLanguage_' + network.id);

                var row = template.clone()
                    .data('info', network);
                row.find('input')
                    .attr('id', 'id_chk_' + network.id);
                row.find('label')
                    .attr('for', 'id_chk_' + network.id)
                    .text( self.t('enable_for_network', {NETWORK: network.name}) );

                row.find('select').val( network.language ).attr('selected', 'selected');

                if (network.enabled) {
                    row.find('input').attr('checked', 'checked');
                    row.find('select').removeAttr('disabled');
                } else {
                    row.find('input').removeAttr('checked');
                    row.find('select').attr('disabled', 'disabled');
                }

                settings.prepend(row);
            }

            template.remove();
            this._attachEvents();
        },

        _attachEvents: function() {
            $('.network-row input').click(function(){
                var row = $(this).parent();
                if ($(this).is(':checked')) {
                    row.find('select').removeAttr('disabled');
                } else {
                    row.find('select').attr('disabled', 'disabled');
                }
            });

            $('#cancel_btn').click(function(){
                $('.network-row').each(function(){
                    var row = $(this);
                    var data = row.data('info');
                    if (data.enabled) {
                        row.find('input').attr('checked', 'checked');
                        row.find('select').removeAttr('disabled');
                    } else {
                        row.find('input').removeAttr('checked');
                        row.find('select').attr('disabled', 'disabled');
                    }
                    row.find('select').val( data.language ).attr('selected', 'selected');
                });
            });

            $('#save_btn').click(function(){
                var settings = [];
                $('.network-row').each(function(){
                    var row = $(this);
                    var data = row.data('info');
                    var lang = row.find('select').val();
                    var enabled = row.find('input').is(':checked');
                    settings.push({
                        id: data.id,
                        enabled: enabled,
                        language: lang
                    });

                    YouhonestCore.DB.setSetting('NetworkEnabled_' + data.id, enabled ? 1 : 0);
                    YouhonestCore.DB.setSetting('NetworkLanguage_' + data.id, lang);
                });

                self.ajax({
                    url: 'settings/setsettings',
                    data: {
                        settings: settings
                    }
                });

                var success = self.t('settings_saved');
                alert(success);
            });
        }

    });

    var self = new OptionsModule();

    YouhonestCore.addModule(self);

})();