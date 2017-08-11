function fbrev_popup(url, width, height, cb) {
    var top = top || (screen.height/2)-(height/2),
        left = left || (screen.width/2)-(width/2),
        win = window.open(url, '', 'location=1,status=1,resizable=yes,width='+width+',height='+height+',top='+top+',left='+left);
    function check() {
        if (!win || win.closed != false) {
            cb();
        } else {
            setTimeout(check, 100);
        }
    }
    setTimeout(check, 100);
}

function fbrev_facebook(btn) {
    fbrev_popup('https://app.widgetpack.com/auth/fbrev?scope=manage_pages', 670, 520, function() {
        WPacXDM.get('https://embed.widgetpack.com', 'https://app.widgetpack.com/widget/facebook/accesstoken', {}, function(res) {
            WPacFastjs.jsonp('https://graph.facebook.com/me/accounts', {access_token: res.accessToken}, function(res) {
                var pagesEL = WPacFastjs.next(btn);
                WPacFastjs.each(res.data, function(page) {
                    var pageEL = WPacFastjs.create('div', 'fbrev-page');
                    //pageEL.innerHTML = '<div>' + page.name + '</div>';
                    pageEL.innerHTML =
                        '<img src="https://graph.facebook.com/' + page.id +  '/picture" class="fbrev-page-photo">' +
                        '<div class="fbrev-page-name">' + page.name + '</div>';
                    pagesEL.appendChild(pageEL);
                    WPacFastjs.on(pageEL, 'click', function() {
                        //WPacFastjs.next(pagesEL).value = page.name;
                        var parents = jQuery(this).parents('form');
                        parents.find('#widget-fbrev_widget-page_id').val(page.id);
                        parents.find('#widget-fbrev_widget-page_name').val(page.name);
                        parents.find('#widget-fbrev_widget-page_access_token').val(page.access_token);
                        //WPacFastjs.next(WPacFastjs.next(pagesEL)).value = page.id;
                        //WPacFastjs.next(WPacFastjs.next(WPacFastjs.next(pagesEL))).value = page.access_token;
                        WPacFastjs.remcl(pagesEL.querySelector('.active'), 'active');
                        WPacFastjs.addcl(pageEL, 'active');
                        return false;
                    });
                });
            });
        });
    });
    return false;
}

jQuery(document).ready(function($) {
    $('.fbrev-options-toggle').unbind("click").click(function () {
        $(this).toggleClass('toggled');
        $(this).next().slideToggle();
    })
});