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
        $form = $('#form');

    var _uploader = null, _validate, _initDate = null,
        $deleteImgWrap,
        _isEdit = $('#is_edit').val(),
        _id = _isEdit&&$('#id').val(),
        _orderby = _isEdit&&$('#orderby').val(),
        _isdeleted = _isEdit&&$('#is_deleted').val();

    //上传图片
    var _uploadImg = function() {
        _uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : 'upload_btn',
            container : $imgupWrap[0],
            url: O.path('/pub/image/up-image&sub_folder=adverttype'),
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

                    if(flag == 'limit' || $('.img-wrap').length >= 1) {
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
                        $imgWraps.append(html);
                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.original_return+' />');
                    _uploader.refresh();//刷新按钮的位置
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
                _uploader.removeFile(_uploader.getFile($deleteImgWrap.attr('id')));
            }
            $deleteImgWrap.remove();

            $('.pt').hide();
            _uploader && _uploader.refresh();
            $("#upload_btn").show();
        });

        $('body').on('click', '.cancel-btn', function(e) {
            $('.pt').hide();
        });


        //发布按钮
        $('#submit_btn').on('click', function() {
            if(_doCheck()) {
                var curEle = $(this);
                $(this).removeClass("bg-green").addClass("color-gray").attr("disabled","true");
                var data = _getData();
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/basic/advert/save-advert-type' + (_isEdit ? '&id=' + _id : '')),
                    success: function(data) {
                        if(data.result == true){
                            $(window).off('beforeunload');
                            location.href = '/basic/advert/advert-type'
                        }else{
                            _showTips(data.msg);
                            curEle.removeClass("color-gray").addClass("bg-green").attr("disabled","false");
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

        $(window).on('beforeunload', function(e) {
            if(!O.compare(_initDate, _getData())) {
                return '离开后，刚刚填写数据会丢失';
            }
        });
    };


    //获取得到的数据  提交数据用
    var _getData = function() {
        var seri = '', image_urls = [];//图片地址的上传
        seri = O.serialize($form, 2);
        //图片的信息收集
        $('.js-img').each(function(i, v) {
            image_urls.push({
                url : $(v).attr('src'),
                original_url : $(v).attr('_src') || ''
            });
        });
        //image_urls = JSON.stringify(image_urls);
        $url="";
        if(image_urls.length>0){
            $url = image_urls[0]["url"];
        }

        //对表单序列化的东西在加上一些参数 对象的模式  都可以
        return {
            'id': _id,
            'name':  $('#typename').val(),
            'logo': $url,
            'orderby': _orderby,
            'is_deleted':_isdeleted
        };
    }
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    }

    //离开页面判断是否有内容输入
    var _count=0;
    $(document).ready(function(){
        if(_count<=0){
            _initDate=_getData();
        }
    })

    //验证配置、规则
    var _checkCfg = {
        config: function() {
            return [{
                id: 'typename',
                msg: {
                    empty: '请输入商品名称'
                },
                fun: function(el) {
                    if($('#typename').val().length === 0)
                        return 'empty';
                }
            },
                {
                    id: 'imgup_wrap',
                    msg: {empty:'<br>请上传至少一张图片'},
                    fun: function() {
                        if($('.img-wrap').length === 0)
                            return 'empty';
                    },
                }]
        }
    };

    var _doCheck = function() {
        if (_validate.fieldList.length === 0) {
            _validate.addFields(_checkCfg.config());
        }

        if (!_validate.process(false)) {
            var id = _validate.errorField.split(',')[1];
            $('#' + id)[0].scrollIntoView();//之后添加效果
            return false;
        }
        return true;
    };
    if(_isEdit){
        $('#upload_btn').hide();
    }
    var _init = {
        init : function() {
            _bindEvent();
            _uploadImg();
            _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
        },
    };
    return _init;
});