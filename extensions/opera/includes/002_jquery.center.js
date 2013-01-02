// ==UserScript==
// @include http://vk.com/*
// @include http://*.vk.com/*
// @include https://vk.com/*
// @include https://*.vk.com/*
// ==/UserScript==

(function(){

    /* jQuery Center */
    jQuery.fn.center = function (absolute) {
        return this.each(function () {
            var t = window.jQuery(this);
            t.css({
                position:    absolute ? 'absolute' : 'fixed',
                left:        '50%',
                top:        '50%',
                zIndex:        '99'
            }).css({
                marginLeft:    '-' + (t.outerWidth() / 2) + 'px',
                marginTop:    '-' + (t.height() / 2) + 'px'
            });

            if (absolute) {
                t.css({
                    marginTop:    parseInt(t.css('marginTop'), 10) + window.jQuery(window).scrollTop(),
                    marginLeft:    parseInt(t.css('marginLeft'), 10) + window.jQuery(window).scrollLeft()
                });
            }
        });
    };
    
})();