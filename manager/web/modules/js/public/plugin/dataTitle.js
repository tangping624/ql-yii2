//自定义title插件

;(function ($) {
    function textTips() {
        this.defaults = {
            tipsName:'title-tips',
            b_tipsName : 'b-tips'
        }
    }

    textTips.prototype = {
        //自动定位
        autoPosition:function($o,$tips){
            var o = {};
            var tips = {};
            var win = {};
            o.w = $o.outerWidth();
            o.h = $o.outerHeight();
            o.l = $o.offset().left;
            o.t = $o.offset().top;
            tips.w = $tips.outerWidth();
            tips.h = $tips.outerHeight(true);
            win.w = $(window).width();
            win.h = $(window).height()+$(document).scrollTop();

            //水平方向
            if(tips.w > o.w){
                if(Math.ceil(tips.w/2) > o.l + Math.ceil(o.w/2)){
                    $tips.css({
                        left: o.l + 'px'
                    });
                }else if(o.l + Math.ceil((o.w + tips.w)/2) > win.w){
                    $tips.css({
                        left: o.l -(tips.w - o.w) + 'px'
                    });
                }else{
                    $tips.css({
                        left: o.l - Math.ceil((tips.w - o.w)/2) + 'px'
                    });
                }
            }else{
                $tips.css({
                    left: o.l - Math.ceil((tips.w - o.w)/2) + 'px'
                });
            }
            //垂直方向
            if(tips.h < o.t){
                $tips.css({
                    top: o.t - tips.h + 'px'
                });
            }else{
                $tips.addClass(this.defaults.b_tipsName).css({
                    top:  o.t + o.h + 'px'
                });
            }
        },
        showTips:function($el,title){
            this.targetEl = $el[0];
            this.layout(title);
            this.autoPosition($el,this.$tips);
        },
        hideTips:function(){
            this.targetEl = null;
            if(this.$tips){
                this.$tips.remove();
            }
        },
        layout:function(title){
            var str='<div class="'+this.defaults.tipsName+'"><p class="tips-msg">'+title+'</p></div>';
            $('body').append(str);
            this.$tips = $('.'+this.defaults.tipsName);
        }
    }

    var tip = new textTips();

    $(document).mousemove(function(e) {
        var $el = $(e.target);
        var textVal;
        if($el.attr('data-title')){
            tip.hideTips();
            if(tip.targetEl != e.target){
                textVal = $el.attr('data-title');
                tip.showTips($el,textVal);
            }
        }else {
            tip.hideTips();
        }
    });
})(jQuery);