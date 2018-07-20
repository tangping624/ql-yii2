if (window.addEventListener) {
    window.addEventListener('message', function (event) {
        redirectLogin(event);
    }, false);
} else if (window.attachEvent) {
    window.attachEvent('onmessage', function (event) {
        redirectLogin(event);
    });
}
function redirectLogin(event) {
    if (event.data) {
        var data = {};
        try {
            data = JSON.parse(event.data);
        } catch (e) {
        }
        if (data.dataType == 'redirectLogin') {
            //替换returnUrl参数
            var re = eval('/(returnUrl=)([^&]*)/gi');
            window.location.href = data.url.replace(re, 'returnUrl=' + encodeURIComponent(window.location.href));
        }
    }
}