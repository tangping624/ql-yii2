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
        _deleteTempl = $('#de_templ').html();
        // _editTempl = $('#edit_templ').html();
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
        _upNum = parseInt($upNum.text(), 10) || 0,//已上传图片数量并取整
        _id;
    var _ueNum = $('.ueedit-box').filter("[sign='ext']").length, _count = _ueNum;
    for(var i = 0; i < _ueNum; i++) {
        var ue;
        if($('#uedesc_' + i).length > 0) {
            ue = UE.getEditor('uedesc_' + i);
            _UEArr.push([ue, 'uedesc_' + i, 'uewrap_' + i]);
        }
  
        // if(_isEdit) {

        ue.ready($.proxy(function() {

            _isEdit && this.ue.setContent($('<div />').html($('.js-detail'+this.i).html()).text());
            _count--;

        }, {ue: ue, i: i}));
        // }
    }
    // console.log(_UEArr);
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
                    var flag = false, limit = up.files.length > 1;
                    plupload.each(files, function(file) {
                        // if(limit) {
                        //     flag = 'limit';
                        //     return false;
                        // }

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

                    // if(flag == 'limit' ) {
                    //     _showTips('选择的图片数量超过1张了');
                    //     return false;
                    // }

                    if(flag == 'size') {
                        _showTips('每张最大支持200k');
                        return false;
                    }

                    if(flag == 'type') {
                        _showTips('只支持jpg / gif / png格式');
                        return false;
                    }

                    plupload.each(files, function(file) {
                         // console.log(files);
                        // var html = Template(_imgTempl, {id: file.id});
                        // $imgWraps.append(html);
                        // if($(".img-wraps").find("img").length==0) {
                            var html = Template(_imgTempl, {id: file.id});
                            $(".img-wrap").remove();
                            $imgWraps.html(html);
                            if($(".upload-btn").prev(".img-wraps").find(".img-wrap").length>0){
                               $(".upload-btn").css("margin-left","86px");
                            }else {
                               $(".upload-btn").css("margin-left","30px");
                            }
                        // }else{
                        //     $("#upload_btn").prev().replaceWith(html);
                        // }
                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                     _upNum=$(".img-wrap").length;
                    $upNum.text( $(".img-wrap").length);

                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.result+' />');

                    _upNum == 1 &&  _setCover($('.js-img').eq(0));
                    $(".img-wraps img").length&&$("#imgup_wrap").find(".color-red1").hide();
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

        // $('body').on('mouseenter mouseleave', '.text-wrap .icon-edit,.text-wrap .icon-delete', function(e) {
        //     if(e.type == 'mouseenter') {
        //         var content =
        //             $(this).hasClass('icon-edit') ?
        //                 '编辑名称' : '删除';

        //         $.pt({//同上
        //             target: this,
        //             width: 'auto',
        //             position: 't',
        //             align: 'c',
        //             autoClose: false,
        //             leaveClose: false,
        //             content: content,
        //             skin: 'pt-black'
        //         });
        //     } else {
        //         var pt = $('.pt');
        //         pt.hasClass('pt-black') && pt.hide()
        //     }
        // });

        // 设为封面
        // $('body').on('click', '.f-btn', function(e) {//绑定class为f-btn的span
        //     _setCover($(this).closest('.img-wrap').find('.js-img'));//找到上传成功的第一张图片_setCover()设为封面
        // });

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
        // console.log($(".upload-btn").prev(".img-wraps").find(".img-wrap"));
        if($(".upload-btn").prev(".img-wraps").find(".img-wrap").length>0){
            $(".upload-btn").css("margin-left","86px");
        }else {
            $(".upload-btn").css("margin-left","30px");
        }
        //编辑页面图片上传数量判断
        var imgCount=function(){
            if(_isEdit) {
                var successImg = $('.img-box img').length;
                _upNum = successImg;
                $('#up_num').html(_upNum);
            }
        }
        imgCount();
        // 删除图片
        $('body').on('click', '.delete-btn', function(e) {//删除图片的确认按钮class
            _upNum--; _upNum = _upNum > 0 ? _upNum : 0;
            if($deleteImgWrap.attr('id')){//如果ID存在
                _uploader.removeFile(_uploader.getFile($deleteImgWrap.attr('id')));//获得图片并移除
            }
            $upNum.text(_upNum);//更新已上传图片数量
            $deleteImgWrap.remove();//移除此节点
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

        //编辑文本编辑器
        // $('body').on('click', '.icon-edit', function() {
        //     $editName = $(this).closest('p').find('.name');
        //     $.pt({
        //         target: this,
        //         width: 286,
        //         position: 'b',
        //         align: 'c',
        //         autoClose: false,
        //         leaveClose: false,
        //         content: _editTempl.replace('{name}', $editName.text())
        //     });
        // });

        //确认编辑
        // $('body').on('click', '.edit-btn', function(e) {
        //     var input = $('.edit-inp'),
        //         error = $('.edit-error'),
        //         val = $.trim(input.val());
        //     if(val === '') {
        //         error.removeClass('hide');
        //         return;
        //     } else {
        //         error.addClass('hide');
        //     }
        //     $editName.text(val);
        //     $('.pt').hide();
        // });

        // $('body').on('change','#advert_adsense',function(){
        //     if($(this).find('option:selected').html()){
        //         $(this).next().hide();
        //     }else{
        //         $(this).next().show()
        //     }
        // })

        $('body').on('keyup','input',function(){
            if($(this).val().trim()){
                $(this).parent().find('.color-red1').hide();
            }else{
                $(this).parent().find('.color-red1').show();
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
                    url: O.path('/baike/emergency/save' + (_isEdit ? '&id=' + _id : '')),
                    success: function(data) {
                        if(data.result == true){
                            showMessage("保存成功","isNormal")
                            $(window).off('beforeunload');//$(window)当前浏览器的窗口  关闭beforeunload事件
                            setTimeout("location.href = '/index.php?r=baike/emergency/index'",500)
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

        // $(window).on('beforeunload', function(e) {
        //     if(!O.compare(_initDate, _getData())) {
        //         return '离开后，刚刚填写数据会丢失';
        //     }
        // });
    };

    //获取得到的数据  提交数据用
    var _getData = function() {
        var seri = '', ueCon = '',  advert_exts ="",image_urls = "";//图片地址的上传
        seri = O.serialize($form, 2);//整个右侧DIV表单序列化
        //图片的信息收集
        // $('.js-img').each(function(i, v) {//遍历所有上传的图片
        //     image_urls.push({
                
                image_urls= $('.js-img').attr('src');
        //     });
        // });
        // image_urls = JSON.stringify(image_urls);//stringify()用于从一个对象解析出字符串

        //其他信息的内容
        for(var i = 0, len = _UEArr.length; i < len; i++) {
            if(_UEArr[i].length === 0) continue;
            ueCon = _UEArr[i][0].getContent();

            if(ueCon === '') continue;
            advert_exts=ueCon;
                // name: $('#' + _UEArr[i][2]).find('.name').text(),
               
            
         }
        // advert_exts = JSON.stringify(advert_exts);
        // console.log(advert_exts);
        //对表单序列化的东西在加上一些参数 对象的模式  都可以
        return {//一些input，section
            'id': _id,
            'title':  $('#emergency_title').val(),
            'address':$('#emergency_address').val(),
            'tel':$('#emergency_tel').val(),
            'linkphone':  $('#advert_linkphone').val(),
            'logo':image_urls,
            'content':advert_exts	//其他信息
        };
    }
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
                    id: 'emergency_title',
                    msg: {empty:'请输入紧急标题'},
                    fun: function() {
                        if ($('#emergency_title').val().length===0) {
                            return 'empty';
                        }
                    }
                },
                {
                    id: 'emergency_address',
                    msg: {empty: '请填写紧急地址'},
                    fun: function() {
                        if($('#emergency_address').val().length===0) {
                            // $('#emergency_address').next().show();
                            return 'empty';

                        }
                    }
                },
                 {
                    id: 'emergency_tel', 
                    msg: {
                            empty: '电话不能为空' 
                    },
                    fun: function(){
                        var linktel = $('#emergency_tel').val();
                        if(linktel.length === 0)
                        return 'empty';
                    } 
                },
                {
                    id: 'uedesc_0', 
                    msg: {
                            empty: '紧急介绍不能为空' 
                    },
                    fun: function(el) {
                           var ueCon = ue.getContent(); 
                           if(ueCon.length === 0) 
                            return 'empty';
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
                }
            ]
        }
    };
    var _doCheck = function() {//校验函数
        if(_upNum>$('.img-wrap').length) {//如果已上传的图片数量小于上传成功的图片数量
            _showTips('还有未上传成功的图片，不能立即保存');
            return false;
        }
        if (_validate.fieldList.length === 0) {//
            _validate.addFields(_checkCfg.config());
        }

        if (!_validate.process(false)) {//
            var id = _validate.errorField.split(',')[1];//?
            // $('.color-red1').show();
            // $('#' + id)[0].scrollIntoView();//之后添加效果  解决抛锚定位时页面整体往上跳的问题
            // $('.color-red1').css('color','#e15f63')
            $('.text-wrap .color-red1').css('float','left');
            return false;
        }
        return true;
    };
    var _init = {
        init : function() {//初始化函数
            _bindEvent();//绑定事件调用
            _uploadImg();//上传图片调用
            _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
                _id = $("#emergencyid").val();

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