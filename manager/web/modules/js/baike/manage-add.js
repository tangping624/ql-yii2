/**
 * Created by tx-04 on 2017/3/28.
 */
define(function(require, exports, module) {
    require('form');
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    window.Template = require('/frontend/js/lib/template');
    window.DataValid= require('/frontend/js/lib/validate');
    var _imgTempl = $('#img_templ').html(),
        _deleteTempl = $('#de_templ').html(),
        _editTempl = $('#edit_templ').html();
//模板内容

    var  $imgWraps = $('#img_wraps'),//上传图片后图片外层的div
        $imgupWrap = $('#imgup_wrap'),//上传图片最外层DIV
        $coverBox = $('#cover_box'),//封面图片最外层DIV
        $upNum = $('#up_num'),//已上传图片数量
        $form = $('#form'),//右侧最外层div
        $title = $('#title');//左侧广告名称id

    var _uploader = null, _validate, _initDate = null,
        $deleteImgWrap,   $editName,    $deleteUeWrap, _UEArr = [],
        _isEdit = $('#is_edit').val(),
        _coverSrc = $coverBox.find('img').attr('src') || '',//得到封面图片的路径
        title = $title.html(),//左侧广告名称内容输出
        _id;
        var ue,_UEArr=[];
        var ue = UE.getEditor('uedesc_0');
        _UEArr.push([ue, 'uedesc_0', 'uewrap_0']);

        ue.ready($.proxy(function() {

            _isEdit && this.ue.setContent($('<div />').html($('.js-detail0').html()).text());

        }, {ue: ue}));


    //上传图片
    // 设置封面
    var _setCover = function(el) {
        var src = el.attr('src');
        _coverSrc = src;//得到的封面图片的路径
        $coverBox.html('<img width="100%" height="100%" src="' + src + '" />');
    };
    var _uploadImg = function() {
        _uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : 'upload_btn',
            container : $imgupWrap[0],
            url: O.path('/pub/image/up-image&sub_folder=shop&isthumbnail=true'),
            flash_swf_url : '/components/plupload/Moxie.swf',
            silverlight_xap_url : '/components/plupload/Moxie.xap',
            init: {
                FilesAdded: function(up, files) {
                    var flag = false, limit = up.files.length > 12;
                    plupload.each(files, function(file) {
                        if(limit) {
                            flag = 'limit';
                            return false;
                        }

                        if(file.size / 1024 > 500) {
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

                    // if(flag == 'limit' || $('.img-wrap').length >500) {
                    //     _showTips('只能上传一张图片');
                    //     return false;
                    // }

                    if(flag == 'size') {
                        _showTips('每张最大支持500k');
                        return false;
                    }

                    if(flag == 'type') {
                        _showTips('只支持jpg / gif / png格式');
                        return false;
                    }

                    plupload.each(files, function(file) {
                        var html = Template(_imgTempl, {id: file.id});
                        $(".img-wrap").remove();
                        $imgWraps.html(html);

                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.result+' />');
                    _setCover($('.img-wrap').find('.js-img'))
                    $(".img-wraps img").length&&$("#imgup_wrap").find(".color-red1").hide();
                    _uploader.refresh();//刷新按钮的位置
                },

                UploadProgress: function(up, file) {
                    var target = $('#' + file.id);
                    target.find('.wait').text(file.percent + '%');
                    target.find('.pct').css('width', file.percent + '%');
                    $('.upload_wrapper').css('margin-top','0')
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
        $('body').on('mouseenter mouseleave', '.opt-btn', function(e) {//点击上传之后的设为封面图标和删除图标span
            if(e.type == 'mouseenter') {
                var content =
                    $(this).hasClass('f-btn') ?
                        '设为封面' : '删除';

                $.pt({//删除的弹出框
                    target: this,
                    width: 'auto',
                    position: 't',
                    align: 'c',
                    autoClose: false,
                    leaveClose: false,
                    content: content,
                    skin: 'pt-black'
                });
            } else {
                var pt = $('.pt');
                pt.hasClass('pt-black') && pt.hide()//找到弹出框并隐藏
            }
        });

        $('body').on('mouseenter mouseleave', '.text-wrap .icon-edit,.text-wrap .icon-delete', function(e) {
            if(e.type == 'mouseenter') {
                var content =
                    $(this).hasClass('icon-edit') ?
                        '编辑名称' : '删除';

                $.pt({//同上
                    target: this,
                    width: 'auto',
                    position: 't',
                    align: 'c',
                    autoClose: false,
                    leaveClose: false,
                    content: content,
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
                position: 'b',//一种定位的封装
                align: 'c',//一种对齐方式的封装
                autoClose: false,
                leaveClose: false,
                content:_deleteTempl
            });
            $deleteImgWrap = $(this).closest('.img-wrap');//删除图片的确认弹出框
        });

        // 删除图片
        $('body').on('click', '.delete-btn', function(e) {//删除图片的确认按钮class
            if($deleteImgWrap.attr('id')){//如果ID存在
                _uploader.removeFile(_uploader.getFile($deleteImgWrap.attr('id')));//获得图片并移除
            }
            $deleteImgWrap.remove();
            $('.upload_wrapper').css('margin-top','-27px');
            $('.pt').hide();
            _uploader && _uploader.refresh();//更新上传界面

            if($('.img-wrap').length === 0) {//img-wrap图片模板的class
                $coverBox.html('<span>封面图片</span>');
            } else {
                _setCover($('.js-img').eq(0));
            }
        });

        $('body').on('click', '.cancel-btn', function(e) {
            $('.pt').hide();
        });

        $('body').on('change','#baike_type',function(){
            if($(this).find('option:selected').html()){
                $(this).attr('selected','true')
                $(this).next().hide();
            }else{
                $(this).next().show()
            }
        })
        $('body').on('keyup','input',function(){
            if($(this).val().trim()){
                $(this).parent().find('.color-red1').hide();
            }else{
                $(this).parent().find('.color-red1').show();
            }
        })
        ue.addListener("keyup",function(type,event){
            if(ue.getContent()){
                $("#uewrap_0").find(".color-red1").hide()
            }else{
                $("#uewrap_0").find(".color-red1").show()
            }

        })
        //发布按钮
        $('#submit_btn').on('click', function() {
            if(_doCheck()) {
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                var curEle = $(this);
                var data = _getData();//获得数据
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/baike/manage/save' + (_isEdit ? '&id=' + _id : '')),
                    success: function(data) {
                        if(data.result == true){
                            showMessage("保存成功","isNormal")
                            $(window).off('beforeunload');//$(window)当前浏览器的窗口  关闭beforeunload事件
                            setTimeout("location.href = '/index.php?r=baike/manage/index'",500)
                        }else{
                            curEle.removeAttr("disabled").removeClass("color-gray").addClass("bg-green");
                            showMessage(data.msg)
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
        var seri = '', ueCon = '',  content = [],image_urls = [];//图片地址的上传
        seri = O.serialize($form, 2);//整个右侧DIV表单序列化
        //百科编辑框内容
        content = _UEArr[0][0].getContent()||'';
        //对表单序列化的东西在加上一些参数 对象的模式  都可以
        return {//一些input，section
            'id': _id,
            'title':  $('#baike_title').val(),
            'wiki_category_id':  $('#baike_type option:selected').val(),
            'logo': $('.img-box img').attr('src'),
            'content':content
        };
    }
    ue.ready($.proxy(function() {
        _isEdit && this.ue.setContent($('<div />').html($('#js-detail').html()).text());
        _initDate = _getData();
    }, {ue: ue}));
    var _showTips = function(tip) {
        var d = $.tips(tip);//输出信息
        setTimeout(function() {
            d.close().remove();
        }, 2000);
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
        config: function() {//通过config接口注入权限验证配置
            return [
                {
                    id: 'baike_title',
                    msg: {empty:'请输入百科标题'},
                    fun: function() {
                        if (!$('#baike_title').val()) {
                            return 'empty';
                        }
                    }
                },
                {
                    id: 'baike_type',
                    msg: {empty: '请选择百科分类'},
                    fun: function(el) {
                        if(!el.value) {
                            $('#baike_type').next().show();
                            return 'empty';

                        }
                    }
                },
                {
                    id: 'imgup_wrap',
                    msg: {empty:'请上传至少一张图片'},
                    fun: function() {
                        if($('.img-wrap').length === 0)
                            return 'empty';
                    },
                    appendFn: function(newEl, el) {
                        $imgupWrap.append(newEl);
                    }
                },
                {
                    id: 'uedesc_0',
                    msg: {empty:'百科介绍不能为空'},
                    fun: function() {
                        var ueCon = ue.getContent();
                        if(ueCon.length === 0) {
                            return 'empty';
                        }
                    }
                }
            ]
        }
    };
    var _doCheck = function() {//校验函数
        if (_validate.fieldList.length === 0) {//
            _validate.addFields(_checkCfg.config());
        }
        if (!_validate.process(false)) {//
            var id = _validate.errorField.split(',')[1];//?
            // $('.color-red1').show();
            // $('#' + id)[0].scrollIntoView();//之后添加效果  解决抛锚定位时页面整体往上跳的问题
            $('.color-red1').css({'color':'#e15f63','margin':'5px 82px'});
            $('#uewrap_0').find('.color-red1').css('float','left');
            return false;
        }
        
        return true;
    };
    var _init = {
        init : function() {//初始化函数
            _bindEvent();//绑定事件调用
            _uploadImg();//上传图片调用
            _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
                _id = $("#advertid").val();

        }
    };
    return _init;

});
function setShowLength(obj, maxlength, id){
    var rem =obj.value.length;
    var wid = id;
    if (rem > maxlength){
        rem = maxlength;
    }
    document.getElementById(wid).innerHTML = rem + "/"+maxlength;
};