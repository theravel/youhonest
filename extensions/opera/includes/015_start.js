// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

window.addEventListener('DOMContentLoaded', function(){
    var injectCss = function(path) {
        var fileObj = opera.extension.getFile(path);
        if (fileObj) {
            var fr = new FileReader();
            fr.onload = function() {
                var link = $('<style/>')
                    .attr('rel', path)
                    .html(fr.result)
                    .appendTo('head');
            };
            fr.readAsText(fileObj);
        } else {
            throw 'File not found: ' + path;
        }
    }
    injectCss('/css/common.css');
    injectCss('/css/login.css');
    injectCss('/css/main.css');

    YouhonestCore.start();
}, false);