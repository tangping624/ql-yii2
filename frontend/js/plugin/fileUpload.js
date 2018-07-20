;(function($) {
    //初始化参数
    var noop = function() { return true; },
    
        uploadDefault = {
            url: '',//cgi地址
            fileElementId: '',//file文件域的id或DOM元素
            fileElementName: '',//file文件域的name，可选，如果不传，默认和id名称相同
            // dataType: 'text',//返回的数据格式
            timeout: 20000,//可选
            params: {},//传递的参数，{name1:value1, name2:value2, ......}
            before: noop,//上传之前的检查，返回false就不会执行上传，最后总需要返回一个布尔值
            success: noop,//上传成功
            complete: noop,//上传结束
            error: noop,//上传出错
            isEncode: true // 是否对字段编码
        },

        idPrefix = {
            iframe: "uploadIframe",
            form: "uploadForm",
            file: "uploadFile"
        };

    //创建iframe
    var _createUploadIframe = function(iframeId) {
        var $iframe = $('<iframe src="about:blank" id="' + iframeId + '" name="' + iframeId + '" style="display:none;" />');

        $iframe.appendTo('body');

        return $iframe;            
    };

    //创建参数隐藏域   
    var _createUploadParams = function(params, isEncode) {
        var paramHtml = "", key

        var encode = function(str) {
            return isEncode ? encodeURIComponent(str) : str;
        };

        for (key in params) {
            if(params.hasOwnProperty(key))
                paramHtml += '<input type="hidden" name="' + encode(key) + '" value="' + encode(params[key]) + '" />';
        }
        return paramHtml;
    };

    //创建上传表单 
    var _createUploadForm = function(id, opts, iframeId) {
        var $oldEl = typeof opts.fileElementId == 'string' ?
            $('#' + opts.fileElementId) : $(opts.fileElementId),
	    
            oldName = $oldEl.attr('name') || $oldEl.attr('id') || ''

        var formId = idPrefix.form + id,
            fileId = idPrefix.file + id,

            formHtml = '<form action="' + opts.url 
                + '" method="post" style="display:none;" name="' + formId 
                + '" id="' + formId + '" enctype="multipart/form-data" target="' + iframeId 
                + '">' + _createUploadParams(opts.params, opts.isEncode) + '</form>',

            $form = $(formHtml),    
            oldElement = $oldEl,
            newElement = oldElement.clone();

        oldElement.attr('id', fileId)
            .attr('name', opts.fileElementName || oldName)
            .before(newElement)//newElement插入到oldElement之前
            .appendTo($form);//再把oldElement移动到form中

        $form.appendTo('body');

        return $form;
    };

    //核心函数
    $.fileUpload = function(options) {
        var opts = $.extend(uploadDefault, options),
            canSend = opts.before();

        //检测参数
        if ('' === opts.url) {
            throw new Error("url为必传参数");
        }
        if('' === opts.fileElementId) {
            throw new Error("fileElementId为必传参数");
        }
        if (!canSend) {
            if(undefined === canSend) {
                throw new Error("before最后总需要返回一个布尔值");
            }
            return;
        }

        //文件上传，核心函数
        return function(opts) {
            var id = +new Date(), //时间戳作为id后缀
                iframeId = idPrefix.iframe + id,
                $form = _createUploadForm(id, opts, iframeId),
                $iframe = _createUploadIframe(iframeId);       
            
            var requestDone = false, //请求是否已经完成，false未完成    
                xhr = {responseXML: '', responseText: '', contentType: ''}, // 创建一个请求响应对象  
                /*interval,*/ error = {}; //保存错误对象

            // 请求响应后的回调
            var uploadCallback = function(isTimeout) {       
                var status = (isTimeout !== "timeout") ? "success" : "timeout"; //当前的状态

                try {
                    /*if(status == 'timeout') {
                        clearInterval(interval);

                    } else {
                        var iframe = $iframe[0],
                            iframeDoc = iframe.contentDocument || iframe.contentWindow.document

                        if(!iframeDoc) return;

                        var iframeLoc = iframeDoc.location || iframe.location;
                        if(!iframeLoc || iframeLoc == 'about:blank') {
                            return
                        } else {
                            clearInterval(interval);
                            xhr.responseText = iframeDoc.body ? iframeDoc.body.innerHTML : iframeDoc.documentElement.textContent;
                            xhr.responseXML = iframeDoc.XMLDocument || iframeDoc;
                            xhr.contentType = iframeDoc.contentType;
                            status = "success";
                        }
                    }*/

                    var iframe = $iframe[0],
                        iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

                    xhr.responseText = iframeDoc.body ? iframeDoc.body.innerHTML : iframeDoc.documentElement.textContent;
                    xhr.responseXML = iframeDoc.XMLDocument || iframeDoc;
                    xhr.contentType = iframeDoc.contentType;
                    status = "success";
                }catch(e) {
                    error = e;
                    status = "error";
                }

                if (xhr || isTimeout) {
                    //请求结束
                    requestDone = true;
                    
                    if (status == "success") { // 成功
                        opts.success && opts.success(xhr, status);
                    } else { //错误或超时
                        opts.error && opts.error(xhr, status, error);
                    }

                    // 上传结束的处理，包括成功和失败
                    opts.complete && opts.complete(xhr, status);

                    //移除所有事件
                    $iframe.off();
                    //移除节点
                    setTimeout(function() { 
                        $iframe.remove();
                        $form.remove(); 
                    }, 200);

                    xhr = null;
                }
            };

            // 如果iframe的load事件失效什么的，可确保会解析一次cgi返回或iframe页面的数据
            if (opts.timeout > 0) {
                setTimeout(function(){
                    // 请求未完成，确保会解析一次cgi返回或iframe页面的数据
                    if(!requestDone) uploadCallback("timeout");
                }, opts.timeout);
            }

            try {
                /*
                // 兼容了不同浏览器上传文件的编码方式
                if(form.encoding) {//IE
                    $(form).attr('encoding', 'multipart/form-data');               
                } else {  
                    $(form).attr('enctype', 'multipart/form-data');            
                }
                interval = setInterval(function() {
                    uploadCallback();
                }, 200)*/

                $iframe.on('load', uploadCallback);//uploadCallback第一个参数是事件对象
                $form.submit();
                
            } catch(e) {            
                throw new Error("提交文件时出现问题");
            }

        }(opts);
    }

})(jQuery);