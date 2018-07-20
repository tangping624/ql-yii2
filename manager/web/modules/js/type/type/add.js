define(function(require, exports, module) {
    require('form');
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    window.Template = require('/frontend/js/lib/template');
    window.DataValid = require('/frontend/js/lib/validate');
    
    var _imgTempl = $('#img_templ').html(),
        _deleteTempl = $('#de_templ').html() ;
         

    var $imgWraps = $('#media_cover'), 
        $imgupWrap = $('#imgup_wrap'),
        $form = $('#user_form');

    var _uploader = null, _validate, _initDate = null,
        $deleteImgWrap,
         _isEdit = $('#isEdit').val(),
        _id = _isEdit&&$('#id').val(),
        _orderby = _isEdit&&$('#orderby').val(),
        _isdeleted = _isEdit&&$('#is_deleted').val();
    var urlkeys,curFile,curUp;
    //上传图片
    var _uploadImg = function() { 
        _uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : ['upload_btn','upload_btn1'], 
            container : $imgupWrap[0],
            url: O.path('/pub/image/up-image&sub_folder=shopgoodstype'),
            flash_swf_url : '/components/plupload/Moxie.swf',
            silverlight_xap_url : '/components/plupload/Moxie.xap', 
            init: {
                FilesAdded: function(up, files) {
                    var flag = false, limit = up.files.length > 1;
                    plupload.each(files, function(file) {
                        if(limit) {
                            flag = 'limit';
                            return false;
                        }

                        if(file.size / 1024 > 10240) {
                            flag = 'size';
                            return false;
                        }
                        if(['image/jpeg','image/png','image/gif'].indexOf(file.type) < 0 ) {
                            flag = 'type';
                            return false;
                        }
                    });

                    if(flag) {
                        plupload.each(files, function(file) {
                            up.removeFile(file);
                        });
                    }

                    if(flag == 'limit') {
                        _showTips('选择的图片数量超过1张了');
                        return false;
                    }

                    if(flag == 'size') {
                        _showTips('每张最大支持10M');
                        return false;
                    }

                    if(flag == 'type') {
                        _showTips('只支持jpg / gif / png格式');
                        return false;
                    }
                    
                    plupload.each(files, function(file) {
                        $("#upload_btn").hide();
                        var html = Template(_imgTempl, {id: file.id});
                        $imgWraps.html(html);
                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) { 
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.original_return+' />');  
                    _uploader.refresh();//刷新按钮的位置
                    curFile = _uploader.getFile($('.img-wrap').attr('id'));
                    curUp = _uploader;
                    // console.log($('#upload_btn1').length);
                    _uploadImg();
                },

                UploadProgress: function(up, file) {
                    var target = $('#' + file.id);
                    target.find('.wait').text(file.percent + '%');
                    target.find('.pct').css('width', file.percent + '%');
                },

                Error: function(up, err) {
                    $('#' + err.file.id).find('.img-box').html('<p>' + err.code + "，" + err.message + '</p>');
                }
            }
        });

        _uploader.init();
    };
    //事件绑定
    var _bindEvent = function() {
        $('body').on('mouseenter mouseleave', '.opt-btn .d-btn', function(e) {
            if(e.type == 'mouseenter') {
                $.pt({
                    target: this,
                    width: 'auto',
                    position: 't', 
                    align: 'c',   
                    autoClose: false,
                    leaveClose: false,
                    content: 删除,
                    skin: 'pt-black'
                });
            } else {
                var pt = $('.pt');
                pt.hasClass('pt-black') && pt.hide()
            }
        }); 
        // 删除图片确认
        $('body').on('click', '.d-btn', function(e) {
            $.pt({
                target: this,
                width: 286,
                position: 'b', 
                align: 'c',   
                autoClose: false,
                leaveClose: false,
                content:_deleteTempl
            });
            $deleteImgWrap = $(this).closest('.img-wrap');
        });

        // 删除图片
        $('body').on('click', '.delete-btn', function(e) { 
            if($deleteImgWrap.attr('id')){
                curUp.removeFile(curFile);
            } 
            $deleteImgWrap.remove();
            $('.pt').hide();
            $("#upload_btn").show();
            // curUp.remove
            $('.moxie-shim').remove();
            _uploadImg();
            _uploader && _uploader.refresh();
        });

        $('body').on('click', '.cancel-btn', function(e) {
            $('.pt').hide();
        });
        //更换图片
        $('body').on('click', '.t-btn', function(e) {
            $.pt({
                target: this,
                width: 286,
                position: 'b', 
                align: 'c',   
                autoClose: false,
                leaveClose: false,
                content:_deleteTempl
            });
            $deleteImgWrap = $(this).closest('.img-wrap');
        });
        //发布按钮
        $('#submit_btn').on('click', function() {
            if(_doCheck()) {
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                var curEle = $(this);
                var datas = _getData();//获得数据
                 // console.log(status);
                _isEdit && (datas.id = $(this).data('id'));
                O.ajaxEx({
                    type: 'get',
                    data: datas,
                    url: O.path('/type/type/save'),
                    success: function(data) {
                        if(data.result == true){
                            showMessage(data.msg,'isNormal');
                            datas.id = data.id;
                            $(window).off('beforeunload');//$(window)当前浏览器的窗口  关闭beforeunload事件

                            parent && parent.DialogEditUser && parent.DialogEditUser.ok(datas);
                        }else{
                            curEle.removeAttr("disabled").removeClass("color-gray").addClass("bg-green");
                            _showTips(data.msg);//_showTips（）显示信息的方法
                        }
                    },
                    error: function() {
                        _showTips('网络错误');
                    }
                });
            }else{
                return false;
            }
        });
        $('#cancel').on('click',function(){
            if(top && top.DialogEditUser) {
                top.DialogEditUser.dialog.close().remove();
            }
        });
        $(window).on('beforeunload', function(e) {
            if(!O.compare(_initDate, _getData())) {
                return '离开后，刚刚填写数据会丢失';
            }
        });
    };


    //获取得到的数据  提交数据用
    var _getData = function() {
        var parent_id = getQueryString('parent_id')=='null'?'':getQueryString('parent_id');
        $('#parent_id').val(parent_id);
        var seri = '';//图片地址的上传 
        seri = O.serialize($form, 2);
        seri.icon = $('.js-img').attr('src');
        //对表单序列化的东西在加上一些参数 对象的模式  都可以 
        return seri;
    }
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    }
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }
    function showMessage(message, isNormal) {
        var parent = window.parent || window;

        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    //验证配置、规则
    var _checkCfg = {
        config: function() {
            return [{
                id: 'name',
                msg: {
                        empty: '请输入分类名称'
                },
                fun: function(el) {
                       if($('#name').val().length === 0)
                        return 'empty';
                }
            }]
        }
    };
    
    var _doCheck = function() {        
        // if (_validate.fieldList.length === 0) {
        //     _validate.addFields(_checkCfg.config());
        // }
        //
        // if (!_validate.process(false)) {
        //     var id = _validate.errorField.split(',')[1];
        //     $('#' + id)[0].scrollIntoView();//之后添加效果
        //     return false;
        // }
        if(!$('#name').val().trim()){
            $('.color-red1').show();
            return false;
        }
        return true;
    };
     if(_isEdit){
            $('#upload_btn').hide();
       }
    var _count=0;
    $(document).ready(function(){
    if(_count<=0){
        _initDate = _getData();
         _count++;
    }
});
    

    var _init = {
        init : function() {  
            _bindEvent();
            _uploadImg(); 
            // _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
        },
    };
    return _init;
});