//表单验证 最好重写。。
define(function(require, exports, module) {
    var supportPlace = ('placeholder' in document.createElement('input'));
    var 
        $ = function(id, formName) {
            if(formName) {
                return formName[id];
            }

            return "string" === typeof id ?
                document.getElementById(id) : id;
        },

        getAttr = function(ele, attrName) {
            var i;

            if ((ele.length > 0) && (ele[0].type === 'radio' 
                || ele[0].type === 'checkbox')) {
                for (i = 0; i < ele.length; i++) {
                    if (ele[i].checked) {
                        return ele[i][attrName];
                    }
                }

                return;
            }

            if(!supportPlace && ele.getAttribute('placeholder') == ele[attrName]){
                return '';
            }

            return ele[attrName];
        },

        trim = function(str) {
            var core_trim = String.prototype.trim;
            return core_trim && !core_trim.call("\uFEFF\xA0") ?
                function(str) {
                    return str == null ? "" : core_trim.call(str);
                }(str) :
                function(str) {
                    return str == null ? "" : (str + "").replace(/^\s+|\s+$/g, "");
                }(str);
        },

        eleTrim = function(ele, attr, val) {
            val= trim(val);
            /*if(ele[attr]!=val){
                ele[attr] = val;
            }*/
            return val;
        },

        templ = '<div class="form-error"><span class="ico-error-s"><!-- 错误图标 --></span>{errorHtml}</div>',

        message = {
            'required': '亲，该项不能为空哟！',
            'email': '邮箱填写错误',
            'long': '输入的内容太长',
            'test': '格式不匹配',
            'mobile': '手机号码填写错误',
            'zip': '邮政编码填写错误'
        };


    var ruleRegex = /^(.+?)\[(.+)\]$/,
        emailRegex = /^[a-zA-Z0-9.!#$%&amp;'*+\-\/=?\^_`{|}~\-]+@[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*$/,
        mobileRegex = /^[1][34578]\d{9}$/, zipRegex = /^\d{6}$/;

    /**
     * 表单验证提示
     * @param {object} errorHtml 提示信息html模板，例如：
     *                           '<div class="form-error"><span class="ico-error-s"><!-- 错误图标 --></span>{errorHtml}</div>'
     *                           
     * @param {object} context   上下文环境
     */
    var DataValid = function (errorHtml, context) {
        this.fieldList = []; //存放表单元素的数组
        this.context = context; //绑定回调中的上下文环境，如果不传入该参数，回调中的this指向绑定该错误提示的元素
        this.flag = true; //执行process函数最后返回的标志，true表示所有的验证通过
        this.errorField = ','; //已显示错误信息的节点id拼接成的字符串，首尾为逗号
        this.errorHtml = errorHtml || templ;//提示信息html模板

        var error = this._setErrorHtml(this.errorHtml);
        this.form = null;
        this.errorInfo = {};
        this.fieldsData = {}; //存储字段值数据 
        this.errorTag =  error.errorTag || ''; //错误信息外层tag标签----> span
        this.errorTagAttrs = error.errorTagAttrs || {};//错误信息外层tag标签的属性----> {'className': 'validspan'}
        this.errorInnerHtml = error.errorInnerHtml || '';//错误信息的html----> <div class=err-msg>{errorHtml}</div>
    };

    DataValid.prototype = {
        /**
         * 添加表单元素，绑定错误信息
         * (String) formName： form元素的name，可选
         * (Array) fields参数如下：
         *     @param {String}   id        表单元素或其id(如果传入了formName参数，这里就表示表单元素的name)
         *     @param {String}   rules     规则
         *     @param {Json}     ruleMsg   规则的提示信息
         *     @param {Function} fun       回调处理函数
         *     @param {JSON}     msg       错误提示信息
         *     @param {String}   errorHtml 可选，提示信息html模板
         *     @param {Function} appendFn  可选，将提示信息节点添加到哪里，默认添加为同级节点的最后一个
         *     @param {Array}    params    可选，fun回调中传入的额外参数
         */
        addFields: function(formName, fields) {
            var type = Object.prototype.toString.call(formName);

            if(type === "[object String]" && formName.length > 0) {
                this.form = document.forms[formName];
            } else if(type === "[object Array]"){
                fields = formName;
            } else { return; }

            var self = this, i = 0,
                field = null, ele = null,
                fieldLength = fields.length;

            for (; i < fieldLength; i++) {
                field = fields[i];
                /*ele = $(field.id);

                // 如果传入的参数不正确或表单元素不存在，跳过
                if (!field.id || !ele) {
                    continue;
                }*/
                if(!field.id) continue;

                self.fieldList[self.fieldList.length] = {//添加一个表单元素到fieldList最后
                    id: field.id, //绑定的元素id或name
                    required : field.required || false,
                    rules: field.rules || '',//已有的规则
                    ruleMsg: field.ruleMsg || {}, //|| message,//已有的规则的提示信息
                    fun: field.fun, //回调函数
                    notAutoCheck : field.notAutoCheck || false,
                    msg: field.msg || {}, //错误提示信息
                    errorHtml: field.errorHtml || '', 
                    appendFn: field.appendFn || null, 
                    desc : field.desc || '',
                    event : field.event || null,
                    params: field.params || [] //回调函数中需要传入的额外参数
                };
            }
        },

        /**
         * 执行验证
         * @param  {Boolean} flag  标记每次执行后是否清空列表，true时清空，默认true
         * @return {Boolean} 是否通过了验证，true表示通过了
         */
        process: function(flag) {
            var self = this, field = null, id,
                i = 0, len = self.fieldList.length;

            //重置为初始值
            self.flag = true;
            self.errorField = ',';
            self.eleInfo = {};
            self.fieldsData = {};

            if (undefined === flag) {
                flag = true;
            }

            for(; i < len; i++) {
                field = self.fieldList[i];

                id = field.id;
                 //如果该节点已经有错误信息显示，则跳过
                if (self.errorField.indexOf(',' + field.id + ',') > -1) {
                    continue;
                }

                self.errorInfo[id] = id;

                if (ele = $(field.id, this.form)) {
                    field.type = (ele.length > 0) ? ele[0].type : ele.type;
                    field.value = eleTrim(ele, 'value', getAttr(ele, 'value'));
                    field.checked = getAttr(ele, 'checked');
                    field.disabled = getAttr(ele, 'disabled');

                    self.fieldsData[field.id] = field;
                    /*field.required = field.required || getAttr(ele, 'require') !== null;
                    if(field.required) field.msg.required = field.msg.required || getAttr(ele, 'data-empty')*/
                }

                self._validate(field, ele);
            }
            flag && self._clearEle();
            return self.flag;
        },
        
        //单个执行验证
        processEle:function(field){
            var ele;
            if (ele = $(field.id, this.form)) {
                field.type = (ele.length > 0) ? ele[0].type : ele.type;
                field.value = eleTrim(ele, 'value', getAttr(ele, 'value'));
                field.checked = getAttr(ele, 'checked');
                field.disabled = getAttr(ele, 'disabled');
            }
            this._validate(field, ele);
        },

        setTempl: function(templ) {
            var error = this._setErrorHtml(templ);
            this.errorTag =  error.errorTag || ''; 
            this.errorTagAttrs = error.errorTagAttrs || {};
            this.errorInnerHtml = error.errorInnerHtml || '';
        },

        // 设置上下文环境
        setContext: function(context) {
            this.context = context;
        },

        //清除之前添加的所有表单元素
        _clearEle: function() {
            var self = this;
            self.fieldList = [];
        },

        //具体的验证
        _validate: function(field, ele) {
            var self = this,
                rules = field.rules.split('|'),//规则
                notRequired = field.rules.indexOf('required') === -1 &&  //不需要
                    (!field.value || field.value === '' || field.value == undefined);

            /*
             * 如果表单元素的值不是必需的，我们不需要验证
             */
            if (notRequired && !field.fun && !field.required) {
                return;
            }

            if(field.disabled) {
                self._hideError(ele, '');
                return;
            }

            var msg = '', result = '',
                losed = false, failed = false;

            /*if (!notRequired) {
                // * 分解规则，执行需要的验证方法
                for (var i = 0, ruleLength = rules.length; i < ruleLength; i++) {
                    var method = rules[i],
                        param = null,
                        failed = false,
                        parts = ruleRegex.exec(method);

                    // * 如果规则有参数 (例如 matches[param]) 分离出来
                    if (parts) {
                        method = parts[1];//matches
                        param = parts[2];//param
                    }

                    //如果是预定义的规则，执行预定义验证
                    if (typeof self._hooks[method] === 'function') {
                        if (!self._hooks[method].apply(self, [field, ele, param])) {
                            failed = true;
                        }
                    }

                    if (failed) {//没有通过预定义的规则
                        msg = field.ruleMsg[method] || message[method] || '';
                        self._showError(ele, msg, field.errorHtml, field.appendFn);
                        return;
                    }
                }
            }*/

            if (typeof field.fun === 'function' || field.required) {
                //如果在new DataValid实例时传入了context参数，回调中this指向该上下文，
                //否则this指向绑定该错误提示的元素
                if(field.required){
                    if ((field.type === 'checkbox') || (field.type === 'radio')) {
                        if(field.checked !== true){
                            result = 'required';
                        }
                    }else if(field.value == ''){
                        result = 'required';
                    }
                }
                
                if((typeof field.fun === 'function') && result != 'required'){
                    result = field.fun.apply(self.context || ele, [field, ele, self]);
                }
                
                if(result){
                    msg = field.msg[result] || message[result] || '';
                    self._showError(ele, msg, field.errorHtml, field.appendFn);

                    //没有通过自定义函数的规则
                    if (msg || msg.length !== 0) {
                        losed = true;
                        return;
                    }
                }
            }

            //通过了预定义和自定义函数的规则
            if (!failed && !losed) {
                self._hideError(ele, msg);
                if(field.desc!=''){
                    self._showError(ele, field.desc, field.errorHtml,null,true);
                }
                return;
            }
        },

        hideError: function(id) {
            var errorID = this._errorID(id),
                errorNode = $(errorID);
                
            if (errorNode) {
                errorNode.style.display = 'none';
            }
        },

        hideAllError: function() {
            for (var i in this.errorInfo) {
                this.hideError(i);
            }
        },

        removeError: function(id) {
            var infoID = this.errorInfo[id],
                errorNode = $(this._errorID(id));

            infoID && delete this.errorInfo[id];
            errorNode && this._removeNode(errorNode);
        },

        removeAllError: function() {
            // todo
        },

        _removeNode: function(node) {
            node.parentNode.removeChild(node);
        },

        //错误提示的id
        _errorID: function(id) {
            // if(!id) return;
            
            if(typeof id === 'string') {
                id = this.form ? this.form.getAttribute('name') + '_' + id : id;
            } else {
                id = id.length > 0 ? id[0] : id;
                id = this.form ? this.form.getAttribute('name') + '_' + id.name : id.id;
            }
            
            return 'datavalid_' + id + '_error';
        },

        //隐藏错误信息
        _hideError: function(ele, msg) {
            var errorID = this._errorID(ele),
                errorNode = $(errorID);

            //错误信息不存在(表示通过了校验)
            if (!msg || msg.length === 0) {
                //如果之前有错误提示，隐藏提示信息节点
                if (errorNode) {
                    errorNode.style.display = 'none';
                }
            }
        },

        //添加显示错误信息
        _showError: function(ele, msg, errorHtml, appendFn,isDesc) {
            ele = ele.length > 0 ? ele[0] : ele;

            var self = this,
                errorID = self._errorID(ele),
                errorNode = $(errorID),
                errorTag = self.errorTag,
                errorTagAttrs = self.errorTagAttrs,
                errorInnerHtml = self.errorInnerHtml,
                color = isDesc ? '#999':'#f00';

            //错误信息不存在(表示通过了校验)
            if (!msg || msg.length === 0) {
                //如果之前有错误提示，隐藏提示信息节点
                if (errorNode) {
                    errorNode.style.display = 'none';
                }
                return;//返回，不再执行下面步骤
            }
            
            if(!isDesc){
                self.flag = false; //验证失败标识
            }
            
            self.errorField += (ele.id || ele.name) + ',';

            if (errorHtml) {//如果传入了html模板，重置
                var error = self._setErrorHtml(errorHtml);
                errorTag = error.errorTag;
                errorTagAttrs = error.errorTagAttrs;
                errorInnerHtml = error.errorInnerHtml;
            }
            
            if (errorNode) { //之前有错误提示，更新提示的内容即可
                errorNode.innerHTML = errorInnerHtml.replace('{errorHtml}', msg);
                errorNode.style.display = '';
                errorNode.style.color=color;
            } else { //否则创建错误提示节点和内容
                //创建包裹html的元素
                var newErrorNode = document.createElement(errorTag);
                //给新创建的节点赋值属性
                for(var i in errorTagAttrs) {
                    newErrorNode[i] = errorTagAttrs[i];
                }
                newErrorNode.id = errorID;
                newErrorNode.innerHTML = errorInnerHtml.replace('{errorHtml}', msg);
                newErrorNode.style.color=color;
                //将生成的提示节点添加到哪里
                if (appendFn) {
                    appendFn.call(newErrorNode, newErrorNode, ele);
                } else {
                    ele.parentNode.appendChild(newErrorNode, ele); 
                }
            }
        },

        //设置显示错误的html
        _setErrorHtml: function (html) {
            var errorTag = html.replace(/^.*?<([^\s]+)\s.*$/, '$1'),
                errorTagAttrs = this._getTagAttributes(html),
                errorInnerHtml = html.replace(/^.*?<[^>]+>(.*?)(?:<[^>]+?>[^<>]*)$/, '$1');

            return {
                'errorTag': errorTag,
                'errorTagAttrs': errorTagAttrs,
                'errorInnerHtml': errorInnerHtml
            }
        },

        //获取tag中的属性
        _getTagAttributes: function(html) {
            var attrs = null, tagAttrs = {}, 
                arr = [], attr = '';

            html = html.replace(/^.*?<([^\s]+?\s+.+?)>.*$/, '$1').toLowerCase();
            attrs = html.match(/(\w+?=['"].+?['"])/g);

            if (attrs) {
                for(var i = 0, n = attrs.length; i < n; ++i) {
                    arr = attrs[i].split('=');
                    attr = arr[0] === 'class' ? 'className' : arr[0];
                    tagAttrs[attr] = arr[1].replace(/^['"]/, '').replace(/['"]+$/, '');
                }
            }
            return tagAttrs;
        }
    };

/*    //预定义的验证函数，可继续添加
    DataValid.prototype._hooks = {
        //required 当前元素的值是否必需的
        'required': function(field, ele, param) {
            var value = field.value;

            if ((field.type === 'checkbox') || (field.type === 'radio')) {
                return (field.checked === true);
            }

            return (value !== null && value !== '' && value !== param);
        },

        // matches[xxx] 当前元素和id为xxx的元素 值是否相等
        'matches': function(field, ele, match) {
            var el = $(match);

            if (el) {
                return field.value === el.value;
            }

            return false;
        },

        // test[reg] 当前元素的值是否符合某个正则
        'test': function(field, ele, reg) {
            reg = reg ? 
                new RegExp(reg) : field.params[0];

            return reg.test(field.value);
        },

        //long[utf8_12+] 当前元素的值(包括中文)转化为gbk或utf8编码的长度 大于/小于/等于 某个长度值
        'long': function(field, ele, match) {
            var reg = /^(gbk|utf8)\_(\d+)([\+\-\=])?$/,
                arr = match.match(reg);

            if (arr) {
                var m1 = arr[1], 
                    m2 = parseInt(arr[2], 10),
                    m3 = arr[3],
                    charSet = {'gbk': '**', 'utf8': '***'},
                    charLen = field.value.replace(/[^\x00-\xff]/g, charSet[m1]).length;

                switch(m3) {
                    case '+':
                        if (charLen < m2) 
                            return false;
                        break;
                    case '-':
                        if (charLen > m2) 
                            return false;
                        break;
                    case '=':
                        if (charLen !== m2) 
                            return false;
                        break;
                    default:
                        if (charLen > m2) 
                            return false;
                        break;
                }
            }

            return true;
        },

        'email': function(field) {
            return emailRegex.test(field.value);
        },

        // 手机
        'mobile': function(field) {
            return mobileRegex.test(field.value);
        },

        // 邮政编码
        zip: function(field) {
            return zipRegex.test(field.value);
        }
    };*/

    module.exports = DataValid;
});