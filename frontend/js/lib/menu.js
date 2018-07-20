(function(window, $, undefined) {
    var _mod = location.pathname.split('/')[1],
        _reg = new RegExp(".+?\\/(" + _mod + "[^?]+).*"),
        _menuItems = $('.js-menu-item'),
        _menuLinks = _menuItems.find('a'),
        _menuLinksArr = [], _href, menuShortArr = [],
        _r = location.href.replace(_reg, '$1'), _flag = false; 

    _menuLinks.each(function(k, link) {
        _href = $(link).attr('href');//.replace(_reg, '$1');
        _menuLinksArr.push(_href);

        _href = _href.split('/').slice(0,-1).join('/');
        menuShortArr.push(_href);
    });

     //记录当前点击的页面所在位置
    $('.js-menu-item').on('click', 'a', function(e) {
        var index = _menuLinks.index(this);
        $.sessionS.setItem('currentIndex', index);
    });

    // 当前页选中
    var _setCurOn = function(i) {
        _menuItems.removeClass('on').eq(i).addClass('on');
        $.sessionS.setItem('currentIndex', i);
    };

    //设置当前页
    var _setCurPage = function() {
        var currentIndex = $.sessionS.getItem('currentIndex'), s, j;
        _menuItems.removeClass('on').eq(currentIndex).addClass('on');

        //如果是从页面链接跳转的，重新设置当前页选中
        for(var i = 0, len = _menuLinksArr.length; i < len; i++) {
            if(-1 < _menuLinksArr[i].indexOf(_r)) {
                _setCurOn(i); _flag = true; break;
            }
        }

        // 可能是直接url地址访问
        if(!_flag) {
            s = _r.split('/').slice(0,-1).join('/');
            j = menuShortArr.indexOf(s);

            j > -1 ? _setCurOn(j) : _setCurOn(currentIndex);
        }
    };

    _setCurPage();
})(this, jQuery);
