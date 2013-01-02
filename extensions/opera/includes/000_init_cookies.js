// ==UserScript==
// @include http://youhonest.com/*
// @include http://*.youhonest.com/*
// @include https://youhonest.com/*
// @include https://*.youhonest.com/*
// ==/UserScript==

// Cookie Hack
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

var cookieValue = readCookie('auth_cookie') || '_';

opera.extension.postMessage({
    action: 'setSetting',
    data: {
        name: 'auth_cookie',
        value: cookieValue
    }
});
