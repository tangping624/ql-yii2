define(function(require, exports, module) {
    jQuery.fn.tab=function(options){
        var tabNav = $(this),
            tablis = tabNav.find('li'),
            tabIndex = options.tabIndex ? options.tabIndex :0,
            tabCon = options.tabCon,
            tabChange = options.change;

        tablis.css('cursor','pointer');
        var showTab = function(index){
            tablis.removeClass('on').css('cursor','pointer');
            tablis.eq(index).addClass('on').css('cursor','default');
            
            if(tabCon&&tabCon.length>0){
                $(tabCon.join(',')).hide();
                $(tabCon[index]).removeClass('hide').show();
            }
        }
        
        var changeTab = function(index){
            showTab(index);
            tabChange&&tabChange(index);
        }
        
        changeTab(tabIndex-1);
        tablis.on('click',function(){
            var tab = $(this);
            if(tab.hasClass('on')) return;
            changeTab(tablis.index(tab))
        })
    }
})