$(function() {
    var translate = YouhonestCore.Translate;
    translate.setLanguage(YouhonestCore.getLanguage());

    var t = function(key, replace) {
        return translate.translate(key, replace);
    }

    var a = $('ul a');
    a.get(0).href = YouhonestConfig.publicURL + '/about';
    a.get(0).innerHTML = t('popup_about_link');
    a.get(1).href = YouhonestConfig.publicURL + '/feedback';
    a.get(1).innerHTML = t('popup_feedback_link');
    a.get(2).href = YouhonestConfig.vkGroup;
    a.get(2).innerHTML = t('popup_vk_group');

    var b = $(VK.Share.button(
        {
            url: YouhonestConfig.publicURL
        },
        {
            type: 'round',
            text: t('popup_vk_share')
        }
    ));

    var shareParams = {
        url: YouhonestConfig.publicURL,
        title: t('popup_vk_share_title'),
        description: t('popup_vk_share_desc'),
        image: YouhonestConfig.URL + '/images/logo.png',
        noparse: 'true'
    }
    var params = [];
    for (var i in shareParams) {
        params.push(i + '=' + encodeURIComponent(shareParams[i]));
    }
    var href = 'https://vk.com/share.php?' + params.join('&');
    b.find('a').attr('href', href);
    $('body #vk_share').append(b);
    $('body a').attr('target', '_blank');
});


