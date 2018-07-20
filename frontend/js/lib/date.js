/**
 *  日期操作
 *********************************************************************************************/
define(function(require, exports, module) {
    module.exports = {
        reg: /^(\d{4}).(\d{1,2}).(\d{1,2})(\s+(\d{1,2}).(\d{1,2}).(\d{1,2}))?$/,

        /**
         * 日期字符串为指定的格式
         *
         * @param {String} str 日期字符串
         * @param {String} p 输出格式, %Y/%M/%D/%h/%m/%s的组合
         * @param {Boolean} [isFill:true] 不足两位是否补0
         * @param {Regex}  reg 正则, str的匹配或部分匹配
         * @return {String}
         */
        str2Str: function(str, p, isFill, reg) {
            var match = null, zero = '0';

            if (isFill || null == isFill) {
                zero = '00';
                str = str.replace(/\b(\w)\b/g, '0$1');
            }

            reg = reg || this.reg;

            if ((match = str.match(reg))) {
                var Y = match[1],
                    M = match[2],
                    d = match[3],
                    h = match[5] || zero,
                    m = match[6] || zero,
                    s = match[7] || zero;

                p = p || '%Y-%M-%D %h:%m:%s';
                str = p.replace(/%Y/g, Y).replace(/%M/g, M).replace(/%D/g, d)
                    .replace(/%h/g, h).replace(/%m/g, m).replace(/%s/g, s);
            }
           
            return str;
        },

        // 月和日不足两位补0
        fillZero: function(str) {
            return str.replace(/\b(\w)\b/g, '0$1');
        },

         /**
         * 格式化日期为指定的格式
         *
         * @method date2Str
         * @param {Date} date
         * @param {String} p 输出格式, %Y/%M/%D/%h/%m/%s的组合
         * @param {Boolean} [isFill:false] 不足两位是否补0
         * @return {String}
         */
        date2Str: function(date, p, isFill) {
            var Y = date.getFullYear(),
                M = date.getMonth() + 1,
                d = date.getDate(),
                h = date.getHours(),
                m = date.getMinutes(),
                s = date.getSeconds();
                
            if (isFill) {
                M = (M < 10) ? ('0' + M) : M;
                d = (d < 10) ? ('0' + d) : d;
                h = (h < 10) ? ('0' + h) : h;
                m = (m < 10) ? ('0' + m) : m;
                s = (s < 10) ? ('0' + s) : s;
            }
            p = p || '%Y-%M-%D %h:%m:%s';
            p = p.replace(/%Y/g, Y).replace(/%M/g, M).replace(/%D/g, d).
                replace(/%h/g, h).replace(/%m/g, m).replace(/%s/g, s);
            return p;
        },

        /**
         * 字符串转为日期
         * 
         * @param  {String} str 日期字符串
         * @return {Date}
         */
        str2Date: function(str, reg) {
            if(!this.reg.test(str)) {
                throw new Error('日期格式不正确');
            }

            str = this.str2Str(str, '%Y/%M/%D %h:%m:%s', true, reg);
            return new Date(str);
        },

        /**
        * 字符串日期比较
        * 
        * @param  {String} d1    开始时间
        * @param  {String} d2    结束时间
        * @param  {String} type  比较类型, 可选值: Y/M/D/h/m/s/ms -> 年/月/日/时/分/妙/毫秒
        * @return {Float} 
        */
        strDateDiff: function(d1, d2, type) {
            return this.dateDiff(this.str2Date(d1), this.str2Date(d2), type);
        },

        /**
         * 日期比较(d1 - d2)
         *
         * @param {Date} d1
         * @param {Date} d2
         * @param {String} type 比较类型, 可选值: Y/M/d/h/m/s/ms -> 年/月/日/时/分/妙/毫秒
         * @return {Float}
         */
        dateDiff: function(d1, d2, type) {
            var diff = 0;
            switch(type) {
                case 'Y':
                    diff = d1.getFullYear() - d2.getFullYear();
                    break;
                case 'M':
                    diff = (d1.getFullYear() - d2.getFullYear()) * 12 + (d1.getMonth() - d2.getMonth());
                    break;
                case 'D':
                    diff = (d1 - d2) / 86400000;
                    break;
                case 'h':
                    diff = (d1 - d2) / 3600000;
                    break;
                case 'm':
                    diff = (d1 - d2) / 60000;
                    break;
                case 's':
                    diff = (d1 - d2) / 1000;
                    break;
                default:
                    diff = d1 - d2;
                    break;
            }
            return diff;
        },

        /**
         * n天前/后的日期字符串
         * 
         * @param  {String}  str        日期字符串，格式："2012-03-02"或"2012-3-2"或"2012/3/2"
         * @param  {Number}  n          n天前/后
         * @param  {String}  separator  分割符号，默认'-'
         * @return {String}  
         */
        showDay: function (str, n, separator, isFill) {
            var date = this.str2Date(str);
            date.setDate(date.getDate() + (n || 0));
            separator = undefined === separator ? '-' : separator;
            date = date.getFullYear() + separator +  (date.getMonth()+1) + separator + date.getDate();

            return isFill ? this.fillZero(date) : date;
        },

        /**
         * 取得xxxx年xx月的天数
         * @param  {Number} year  年份
         * @param  {Number} month 月份
         * @return {Number}
         */
        getDaysInMonth: function(year, month) {
            var d = new Date(year,month,0);
            return d.getDate();
        }
    }  
});

