(function (global, $) {
    $.extend($, {
        /**
         * 将字符串转换为JSON格式，如果参数为对象则直接返回
         *
         * @param {String|Object} data 需要进行格式转换的数据
         * @return {Object} 转换后的JSON数据
         */
        parseJSON: function (data) {

            if (!data || typeof(data) != "string") {
                return data;
            }
            data = $.trim(data);
            if(!data){
                return data;
            }
            try {
                data = JSON.parse(data);
            } catch (e) {
                data = (new Function("return " + data))();
            }

            return data;
        }
    });
    // =======================================存储支持==========================================
    var checkStorage = function (s) {
        var key = "CHECK_STOARGE_TEST",
            value;

        try {
            s.setItem(key, 1);
            value = s.getItem(key);
            s.removeItem(key);

            return value == 1;
        } catch (e) {
            return false;
        }
    };

    // 存储支持情况
    try {
        $.isSessionAble = checkStorage(sessionStorage);
        $.isLocalAble = checkStorage(localStorage);
    } catch (e) {
        $.isSessionAble = false;
        $.isLocalAble = false;
    }

    // window.name缓存，和localStorage及sessionStorage行为保持一致
    var nameStore = {
        // 刷新数据
        _flush: function (data) {
            data && (window.name = JSON.stringify(data));
        },
        getAll: function () {
            try {
                return this.data = $.parseJSON(window.name || '{}');
            } catch (e) {
                return this.data = {};
            }
        },
        setItem: function (key, value) {
            var data = this.data || this.getAll();
            if (!$.isPlainObject(data)) {
                data = {};
            }
            data[key] = value;
            this._flush(data);
        },
        getItem: function (key) {
            var data = this.data || this.getAll();
            if ($.isPlainObject(data)) {
                return data[key];
            }
        },
        removeItem: function (key) {
            var data = this.data || this.getAll();
            if ($.isPlainObject(data)) {
                delete data[key];
                this._flush(data);
            }
        }
    };

    /**
     * 存储支持
     * @param  {String} type      可选，存储类型：local、session、name、storage，默认session
     * @param  {String} nameSpace 可选，命名空间，默认使用'STORAGE_NAMESPACE'命名空间
     */
    var _Storage = function (type, nameSpace) {
        type = type || 'session';
        nameSpace = nameSpace || 'STORAGE_NAMESPACE';

        var
            MAX = 40, // 最大尝试次数
            COUNT = 0, // 计数
            TIME = 1000 * 60 * 60 * 24, // 一天时间
            storageTpye = {
                local: function (key) {
                    return $.isLocalAble ? // localStorage存储，如果不支持该存储方式，设置无效果，所以需要先判断是否支持local存储
                        [$.parseJSON(localStorage.getItem(key) || "{}"), localStorage] : [{}, {
                        setItem: function () {
                        }
                    }];
                },
                session: function (key) { // session级缓存，sessionStorage -> window.name 逐步兼容
                    return $.isSessionAble ?
                        [$.parseJSON(sessionStorage.getItem(key) || "{}"), sessionStorage] : this.name(key);
                },
                name: function (key) { // 也是session级缓存，但是只用window.name存储
                    return [nameStore.getItem(key), nameStore];
                },
                storage: function (key) { // localStorage -> sessionStorage -> window.name 逐步兼容
                    return $.isLocalAble ?
                        [$.parseJSON(localStorage.getItem(key) || "{}"), localStorage] : this.session(key);
                }
            };

        var temp, storage, storageData;
        temp = storageTpye[type](nameSpace);
        storageData = temp[0]; // 存储数据
        storage = temp[1]; // 存储方式

        /**
         * 设置存储数据
         * @param {[type]} key   键名
         * @param {[type]} value 键值
         */
        var setItem = function (key, value) {
            COUNT = MAX;  //重置
            storageData[key] = {"v": value, "t": +new Date()};
            _flush();
        };

        // 获取存储数据
        var getItem = function (key) {
            var value = storageData[key],
                vv = value && value.v;

            return $.isPlainObject(vv) ? $.extend(true, {}, vv) :
                $.isArray(vv) ? $.extend(true, [], vv) : vv;
        };

        // 移除存储数据
        var removeItem = function (key) {
            COUNT = MAX;  //重置
            delete storageData[key];
            _flush();
        };

        /**
         * 取得整段数据
         * @param  {Boolean} extend ，最好传入该参数为true，防止对返回的数据更改
         */
        var getAll = function (extend) {
            return extend ? $.extend(true, {}, storageData) : storageData;
        };

        // 刷入缓存数据
        var _flush = function () {
            var dataStr;

            try {
                dataStr = JSON.stringify(storageData);
            } catch (e) {
                throw new Error('JSON.stringify转化出错');
            }

            try {
                storage.setItem(nameSpace, dataStr);
            } catch (e) {
                COUNT--;
                if (COUNT >= 0) {
                    _deleteByTime();
                    _flush();
                } else {
                    throw new Error("写入存储报错");
                }
            }
        };

        // 按时间删除
        var _deleteByTime = function () {
            var old, key, now = +new Date();

            $.each(storageData, function (k, v) {
                if (old) {
                    if (now - old.t >= TIME) return false;
                    else if (old.t > v.t) {
                        old = v;
                        key = k;
                    }
                } else {
                    old = v;
                    key = k;
                }
            });

            old && delete storageData[key];
        };

        return {
            getAll: getAll,
            setItem: setItem,
            getItem: getItem,
            removeItem: removeItem
        };
    };

    $.storage = _Storage;
    $.localS = _Storage('local');
    $.sessionS = _Storage('session');

    //暂不开放，防止内存占用及可能的数据串扰
    // $.nameS = storage('name');
    // $.storageS = storage('storage');

    // =======================================全局函数处理==========================================

    var ajaxCache = {
        cancel: function () {
        },
        error: function () {
        }
    };

    var timestampReg = new RegExp("(^|&|\\?)(t=|_t=)([^&]*)(&|$)", "i"),
        timestampRegGen = function (array) {
            array.push('');
            return new RegExp("(^|&|\\?)(t=|_t=|" + array.join('=|') + ")([^&]*)(&|$)", "i");
        };

    var genKey = function (cfg) {
        var url = cfg.url,
            filter = cfg.filterProp || cfg.filter,
            key = url + (url.indexOf('?') > -1 ? '&' : '?') + (cfg.data || '');
        if (filter) {
            if ($.isArray(filter)) {
                return key.replace(timestampRegGen(filter), '');
            } else {
                return key.replace(timestampReg, '');
            }
        }
        return key;
    };

    var ajaxFail = function (xhr, statusText) {
        if (xhr) {
            if (xhr.status === 401 && xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response && response.login_url) {
                        top.window.location.href = response.login_url;
                    }
                } catch (e) {
                }
            }
            if (statusText !== 'canceled') {
                ajaxCache.error.apply(this, arguments);
            }
        }
    };

    var ajaxThen = function (response, statusText, xhr) {
        var data = ajaxCache.then.apply(this, arguments);
        if ($.type(data) === 'boolean' && !data) {
            return $.Deferred().reject(xhr, statusText, 'logic error');
        }
        return $.Deferred().resolve(response, statusText, xhr);
    };

    /**
     * ajax节流控制，默认memoize，传入abort则取消上一次
     * 新增传入node，可以控制process状态
     */

    $.ajaxDelay = 300;

    if (!$.ajaxPrefilterHook) {
        $.ajaxPrefilterHook = true;
        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            var userBeforeSend = options.beforeSend;

            options.beforeSend = function (xhr, cfg) {
                var send = true;

                if (userBeforeSend) {
                    send = userBeforeSend(xhr, cfg);
                    send = send || send === undefined;
                }

                if (send) {

                    if (cfg.async && !cfg.repeat) {
                        var key = genKey(cfg),
                            node = cfg.node ? $(cfg.node) : $(),
                            process = cfg.process || 'btn-loading';

                        if (node.length) {
                            if (!node.find('.icon-loading').length) {
                                node.prepend('<span class="icon-loading">');
                            }
                            if (node.hasClass(process)) {
                                ajaxCache.cancel.apply(this, arguments);
                                return false;
                            }
                        }

                        if (ajaxCache[key]) {
                            if (cfg.abort) {
                                ajaxCache[key].abort();
                            } else {
                                ajaxCache.cancel.apply(this, arguments);
                                return false;
                            }
                        }

                        ajaxCache[key] = xhr.always(function () {
                            node.removeClass(process);
                            setTimeout(function () {
                                delete ajaxCache[key];
                            }, cfg.abort ? 0 : (cfg.ajaxDelay === undefined ? $.ajaxDelay : cfg.ajaxDelay));
                        });

                        //异步请求如有node参数，则处理添加process类
                        node.addClass(process);
                    }
                }
                return send;
            };


        });
    }

    var Overall = {
        _events: {},    // 自定义事件

        // 触发自定义事件
        emit: function (type) {
            if (!this._events[type]) {
                return;
            }

            var i = 0,
                l = this._events[type].length;

            if (!l) {
                return;
            }

            for (; i < l; i++) {
                this._events[type][i].apply(this, [].slice.call(arguments, 1));
            }
        },

        // 删除自定义事件
        off: function (name, callback) {
            if (!(name || callback)) {
                this._events = {};
                return;
            }

            var list = this._events[name];
            if (list) {
                if (callback) {
                    for (var i = list.length - 1; i >= 0; i--) {
                        if (list[i] === callback) {
                            list.splice(i, 1);
                        }
                    }
                }
                else {
                    delete this._events[name];
                }
            }
        },

        // 添加自定义事件
        on: function (type, fn) {
            if (!this._events[type]) {
                this._events[type] = [];
            }

            this._events[type].push(fn);
        },

        // 函数节流
        throttle: function (method, context, time) {
            clearTimeout(method.tId);
            method.tId = setTimeout(function () {
                method.call(context);
            }, time || 100);
        },

        // 格式化值
        formatVal: function (data, len) {
            len = len || 3;
            data = $.trim(data + '');

            var arr = [];
            while (data.length > len) {
                arr.push(data.slice(-len));
                data = data.slice(0, -len);
            }

            if (data) arr.push(data);

            return arr.reverse().join(',');
        },
        // 依赖jQuery或zepto
        ajaxEx: function (args) {
            var o = this._o || (this._o = Overall.o);

            args = args || {};
            args.data = args.data || {};
            args.data.o = o;

            args = $.extend({
                async: true,
                cache: false,
                dataType: 'json'
            }, args);

            if (ajaxCache.then) {
                return $.ajax(args).then(ajaxThen).fail(ajaxFail);
            }

            return $.ajax(args).fail(ajaxFail);
        },
        ajaxSetup: function (arg1, arg2) {
            var cfg = {},
                type = $.type(arg1);
            if (type === 'string') {
                cfg[arg1] = arg2;
            } else if (type === 'object') {
                cfg = arg1;
            }
            $.extend(ajaxCache, cfg);
        },
        // 取得url单个参数
        getQueryStr: function (name, str) {
            var reg = new RegExp("(^|&|\\?)" + name + "=([^&]*)(&|$)", "i");
            var result = (str || location.search.substr(1)).match(reg);
            if (result != null) return decodeURIComponent(result[2]);
            return null;
        },

        /*  
         *  说明：过滤XSS
         *  @param  {String}    str 需要过滤的内容
         *  @return {String}    显示的内容
         */
        xss: function (str) {
            var div = document.createElement("div"),
                text = document.createTextNode(str), val = '';

            div.appendChild(text);
            val = div.innerHTML;
            text = null;
            div = null;

            return val;
        },

        // 简单的模板替换方法
        format: function (str, data) {
            var that = this;
            return str.replace(/\{([^{}]+)\}/g, function (match, key) {
                var value = data[key];
                return (value !== undefined) ? that.xss('' + value) : match;
            });
        },

        /**
         * 获取url或者自定义字符串中的参数
         *
         * @param {String} name 不传name则直接返回整个参数对象
         * @param {String} queryStr 自定义字符串
         * @param {Boolean} [unfilter:false] 不进行参数XSS安全过滤
         * @param {Boolean} [undecode:false]] 不进行自动解码
         * @return {String|Object} 获取到的参数值或者由所有参数组成完整对象
         */
        getQuery: function (name, queryStr, unxss, undecode) {
            var str = queryStr || location.search.replace("?", ""), tempArr,
                obj = {}, temp, arr = str.split("&"), len = arr.length;

            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    try {
                        if ((tempArr = arr[i].split('=')).length === 2) {
                            temp = undecode ? tempArr[1] : decodeURIComponent(tempArr[1]);
                            obj[tempArr[0]] = unxss ? temp : this.xss(temp);
                        }
                    } catch (e) {
                    }
                }
            }

            return name ? obj[name] : obj;
        },

        /**
         * 判断是否是ie及ie的版本，
         * @return {false|6|7|8|9}
         */
        ie: function () {
            var v = 4,
                div = document.createElement('div'),
                i = div.getElementsByTagName('i');
            do {
                div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->';
            } while (i[0]);
            return v > 5 ? v : false; //如果不是IE，返回false
        },

        /**
         * https://github.com/sindresorhus/multiline
         * 在JS中自然地写html，经测试(2000000次)：
         *     chrome为用'+'的10-15分之一，约2.7秒
         *     IE11为用'+'的5-6分之一，约11秒
         * @param  {Function} fn
         * @return {String}
         */
        multiline: function (fn) {
            if (typeof fn !== 'function') {
                throw new Error('multiline need a function');
            }
            // 用正则匹配比用字符串快
            return fn.toString().replace(/.*?\/\*!/, '').replace(/(!\*\/\})|(!\*\/;\})/, '');//.replace('!*/}', '').replace('!*/;}', '');
        },

        _getElesByForm: function (form) {
            var eles = form.elements || form.find('input, select, textarea');
            var map = {
                'file': false, 'submit': false,
                'reset': false, 'button': false, 'image': false
            };

            eles = $.grep(eles, function (v) {
                return map[$(v).attr('type') + ''] === false ? false : true;
            });

            return eles;
        },

        _getValue: function (el) {
            var type = el.attr("type");
            var val = $.trim(el.val());

            if (type == "checkbox" || type == 'radio') {
                val = el.prop("checked") ? val : undefined;
            }
            return val;
        },

        _readProperty: function (el) {
            el = $(el);

            var o = {
                "id": el.attr("id"),
                "value": this._getValue(el),
                "name": el.attr("name")
            };

            return o;
        },

        // 过时，不推荐适用，可以使用lib目录下的form.js，里面有相同的方法
        serialize: function (form, type) {
            type = (type == undefined ? 1 : type);

            var eles = this._getElesByForm(form);
            var prop, that = this,
                r = type == 1 ? [] : {};

            $.each(eles, function (k, v) {
                prop = that._readProperty(v);
                if (prop.value !== undefined) {
                    type == 1 ? (r.push(prop.name + "=" + prop.value)) : (r[prop.name] = prop.value);
                }
            });

            return type == 1 ? r.join("&") : r;
        },

        //JS两个对象或数组的比较，相同返回true，不同返回false
        compare: function (obj1, obj2) {
            var i,
                equal = arguments[2] || [true];

            obj1 = obj1 || {};
            obj2 = obj2 || {};

            for (i in obj1) {
                if (obj1.hasOwnProperty(i) && obj2.hasOwnProperty(i)) {
                    if (typeof obj1[i] === 'object') {
                        arguments.callee(obj1[i], obj2[i], equal)
                        && arguments.callee(obj2[i], obj1[i], equal);//防止这种情况：compare([1,2,{}],[1,2,{"1":""}])
                    } else {
                        if (obj1[i] !== obj2[i]) {
                            equal.push(false);
                            return false;
                        }
                    }
                } else {
                    equal.push(false);
                    return false;
                }
            }

            for (i in obj2) {//防止这种情况：compare([1,2],[1,2,{}])
                if (!(obj1.hasOwnProperty(i) && obj2.hasOwnProperty(i))) {
                    equal.push(false);
                    return false;
                }
            }

            return equal.join("").indexOf("false") === -1 ? true : false;
        },

        getToken: function () {
            var token = this.o;

            if (token && token.length > 0) {
                return token;
            } else {
              //  var result = location.pathname.split('/')[1];
                return  '';
            }
        },

        path: function (url, param) {
            var hash = '', index, 
                pre =  (url.charAt(0) === '/' ? '' : '/');

            index = url.indexOf('#');

            if (index !== -1) {
                hash = url.substring(index);
                url = url.substring(0, index);
            }

            if ($.isPlainObject(param)) {
                param = $.param(param);
            }

            var tempAppCode = this.getQueryStr('_ac'), temp = '';
            if (tempAppCode) {
                temp = param ? param + '&' : '';
                param = temp + '_ac=' + tempAppCode;
            }

            var tempFuncCode = this.getQueryStr('_fc');
            if (tempFuncCode) {
                temp = param ? param + '&' : '';
                param = temp + '_fc=' + tempFuncCode;
            }

            param = param ? (url.indexOf('?') !== -1 ? '&' : '?') + param : '';

            return pre + url + param + hash;
        },
        /**
         * 将参数形式字符串转为json格式
         * @param  {String} str 类似于:a=12&b=23&c=45
         * @param  {String} sep 分隔符
         * @return {JSON} JSON对象数据
         */
        unparam: function (str, sep) {
            if (typeof str !== 'string') return str;
            if ((str = $.trim(str)).length === 0) return {};

            var ret = {},
                pairs = str.split(sep || '&'),
                pair, key, val, m,
                i = 0, len = pairs.length;

            for (; i < len; i++) {
                pair = pairs[i].split('=');
                key = decodeURIComponent(pair[0]);


                // pair[1] 可能包含gbk编码中文, 而decodeURIComponent 仅能处理utf-8 编码中文
                try {
                    val = decodeURIComponent(pair[1]);
                } catch (e) {
                    val = pair[1] || '';
                }

                if ((m = key.match(/^(\w+)\[\]$/)) && m[1]) {
                    ret[m[1]] = ret[m[1]] || [];
                    ret[m[1]].push(val);
                } else {
                    ret[key] = val;
                }
            }
            return ret;
        },

        image: function (url, width, height, q) {
            return [url, '@', width && width + 'w_' || '', height && height + 'h_' || '', q || 100, 'Q.png'].join('');
        },

        Rebuild: {
            _lastSheet: function () {
                var sheets = document.styleSheets,
                    lastSheet = sheets[sheets.length - 1].ownerNode;

                return lastSheet;
            }(),

            // 将css嵌入到页面中执行
            css: function (data, id) {
                var $el = $("<style type='text/css' " + (id ? "id='" + id + "'" : "" ) + ">" + data + "</style>");
                if (this._lastSheet) {
                    $el.insertBefore(this._lastSheet);
                }
                else {
                    var $head = $('head'),
                        $links = $head.find('link'),
                        len = $links.length, $style;

                    if (len > 0) {
                        $el.insertAfter($links.eq(0));
                    } else {
                        $style = $head.find('style');

                        $style.length > 0 ?
                            $el.insertBefore($style.eq(0)) : $head.append($el);
                    }
                }

                return this;
            }
        }

    };

    Overall.o = document.getElementsByName('prefix')[0] && document.getElementsByName('prefix')[0].content;
    global.Overall = global.O = global.Util = Overall;

    $.sessionOfTenant = _Storage('session', Overall.getToken());

    /**
     * 请求页面js文件，用于构建
     * @param path  modules目录js文件路径
     */
    function _createScript(path) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = path;
        document.getElementsByTagName('body')[0].appendChild(script);
    }

    global.__REQUIRE = function (path) {
        var runmode = Util.getQueryStr('run'); //debug,build
        function _debugMode() {
            seajs.use(path);
        }

        function _buildMode() {
            _createScript(path.replace('modules', 'modules/build'));
        }

        if (window.ENV == 'dev' || runmode == 'debug') {
            (runmode == 'build') ? _buildMode() : _debugMode();
        } else {
            _buildMode();
        }
    };

    // global.__SCRIPT = function(scripts,buildpath){
    //     var runmode = Util.getQueryStr('run'); //debug,build
    //     function _debugMode(){
    //         for(var i=0;i<scripts.length;i++){
    //              _createScript(scripts[i]);
    //         }
    //     }

    //     function _buildMode(){
    //         _createScript('/modules/build/script/'+buildpath);
    //     }

    //     if(window.ENV =='dev' || runmode =='debug'){
    //         (runmode == 'build'&&buildpath) ? _buildMode() : _debugMode();
    //     }else{
    //         buildpath&&_buildMode();
    //     }
    // };

})(this, window.Zepto || window.jQuery);