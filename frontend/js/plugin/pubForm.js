/*
 * 单选、复选
 * cearte by yuanl02 2015-5-7
*/

define(function (require, exports, module) {
    jQuery.pubForm = function (options) {
        var defaults = {
            cName : 'selected',
            afterOpera: null
        }
        var options = $.extend({}, defaults, options);
        var o = new pubForm(this, options);
        o.init();
    };

    function pubForm(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
    }

    pubForm.prototype = {
        init : function () {
            this.radioClick();
            this.checkboxClick();
        },
        radioClick : function(){
            var _this = this;
            $('.public-radio-list').on('click','.radio-item',function(){
                $(this).addClass(_this.cName).siblings().removeClass(_this.cName);
            });
        },
        checkboxClick : function(){
            var _this = this;
            $('.public-checkbox-list').on('click','.checkbox-item',function(){
                if(_this.afterOpera){
                    _this.afterOpera(this);
                }
                if(!$(this).hasClass(_this.cName)){
                    $(this).addClass(_this.cName);
                }else{
                    $(this).removeClass(_this.cName);
                }
            });
        }
    }
});