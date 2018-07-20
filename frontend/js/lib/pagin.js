// 分页
define(function(require, exports, module) {
    var Pagin = function (id) {
        var $cont = $(id || '#pagin');
        this.$cont = $cont;
        this.$pre = $cont.find('.page-prev');
        this.$next = $cont.find('.page-next');
        this.$pageNum = $cont.find('.page-num');
        this.$input = $cont.find('.inp');
        this.$go = $cont.find('.page-go');
        this.hasBindEvent = false;
    };

    Pagin.prototype = {
        /**
         * 显示
         * @param  {[type]} pages 总页数
         * @param  {[type]} fn    上一页、下一页、跳转的回调
         * @param  {[type]} cur   可选，当前页
         */
        show: function(pages, fn, cur) {
            cur = this.cur = 
                (parseInt(cur, 10) || this.cur || 1);

            this.pages = pages;

            if(pages < 2) {
                this.$cont.addClass('hide');
                return;
            } else {
                this.$cont.removeClass('hide');
            }

            if(cur == 1) {
                this.$pre.addClass('hidden');
                this.$next.removeClass('hidden');
            } else {
                cur == pages ?
                    this.$next.addClass('hidden') :
                    this.$next.removeClass('hidden');
                this.$pre.removeClass('hidden');
            }
            this.$pageNum.text(cur + ' / ' + pages);
            !this.hasBindEvent && this._bindEvent(fn);
        },

        jump: function(n, callback) {
            if(isNaN(n) || n < 1 || n > this.pages) return;
            callback && callback(this.cur = n, this.pages);
        },

        _bindEvent: function(fn) {
            var self = this;
            this.hasBindEvent = true;

            this.$pre.on('click', function() {
                self.jump(self.cur - 1, fn);
            });
            this.$next.on('click', function() {
                self.jump(self.cur + 1, fn);
            });
            this.$go.on('click', function() {
                self.jump(parseInt($.trim(self.$input.val()), 10), fn);
                self.$input.val('');
            });
            
            this.$input.on('keydown',function(event){
                var keycode = event.which;  
                if (keycode == 13) {  
                    self.$go.trigger('click');    
                }  
                event.stopPropagation();
            })
        },

        setCurpage: function(n) {
            this.cur = n;
        },

        getCurpage: function() {
            return this.cur;
        }
    };

    module.exports = window.Pagin = Pagin;
});
