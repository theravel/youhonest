(function(){

    // =================== AJAX handling ==========================
    
    var actions = {
        ajax: [
            YouhonestCore.Ajax.call,
            function(event) {
                return function(response) {
                    event.source.postMessage({
                        action: 'ajax',
                        response: response
                    })
                }
            }
        ],
        getAllSettings: [
            YouhonestCore.DB.getAll,
            function(event) {
                return function(response) {
                    event.source.postMessage({
                        action: 'getAllSettings',
                        response: response
                    })
                }
            }
        ],
        setSetting: [
            function(data){
                YouhonestCore.DB.set(data.name, data.value);
            },
            function(){}
        ]
    }

    opera.extension.onmessage = function(event) {
        var action = event.data.action;
        if (!action) {
            throw 'Action was not specified';
        }
        if (typeof actions[action] == 'undefined') {
            throw 'Action "' + action + '" is not implemented';
        }
        var callableAction = actions[action][0];
        var responseCallback = actions[action][1](event);
        callableAction(event.data.data, responseCallback);
    }

    // ===================== UI ==========================
    var buttonOptions = {
        disabled: false,
        title: 'Youhonest',
        icon: 'images/icon_18.png',
        popup: {
            href: 'popup/popup.html',
            width: 150,
            height: 110
        },
        badge: {}
    };

    var button = opera.contexts.toolbar.createItem(buttonOptions);
    opera.contexts.toolbar.addItem(button);

})()