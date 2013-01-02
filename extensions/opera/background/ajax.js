(function() {

    var Ajax = $.jClass({

        AUTH_COOKIE: 'auth_cookie',

        call: function(params, callback){
            // Dirty hack
            // See http://stackoverflow.com/questions/13697496/why-are-cookies-unavailable-from-opera-extension-background-page
            var cookie = YouhonestCore.DB.get(self.AUTH_COOKIE);
            params.headers = { 'Youhonest-Token' : cookie };

            params.success = function(data) {
                callback({
                    result: 'success',
                    data: data
                })
            }
            params.error = function(jqXHR, textStatus, errorThrown) {
                callback({
                    result: 'error',
                    jqXHR: {status: jqXHR.status},
                    textStatus: textStatus,
                    errorThrown: errorThrown
                })
            }
            $.ajax(params);
        }
        
    });

    var self = new Ajax();

    YouhonestCore.Ajax = self;

})();