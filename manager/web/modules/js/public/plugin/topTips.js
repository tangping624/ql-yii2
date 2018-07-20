//顶部提示信息插件

define(function (require, exports, module) {
    jQuery.topTips = function (options) {
        var defaults = {
            mode : 'normal',
            tip_text : ''
        }
        var options = $.extend({}, defaults, options);
        var o = new topTips(this, options);
        o.init();
    };

    function topTips(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
    }
    topTips.prototype = {
        init : function(){
            this.layout();
        },
        layout : function(){
            var str = '';
            if(this.mode === 'normal'){
                str = '<div class="top-tips top-tips-success">'+this.tip_text+'</div>';
            }else if(this.mode === 'warning'){
                str = '<div class="top-tips top-tips-warning">'+this.tip_text+'</div>';
            }
            $('body').append(str);
            var $tips = $('.top-tips');
            var w = $tips.outerWidth();
            $tips.css('margin-left',-Math.ceil(w/2)+'px');
            if(this.mode === 'normal'){
                this.customAnimate($tips,2000);
            }else if(this.mode === 'warning'){
                this.customAnimate($tips,4000);
            }
        },
        customAnimate : function($o,t){
            $o.fadeIn(500);
            setTimeout(function(){
                $o.fadeOut(800,function(){
                    $(this).remove();
                });
            },t);
        }
    }
});