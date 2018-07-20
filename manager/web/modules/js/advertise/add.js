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
        $deleteImgWrap,
        _isEdit = $('#is_edit').val(),
        _coverSrc = $coverBox.find('img').attr('src') || '',//得到封面图片的路径
        title = $title.html(),//左侧广告名称内容输出
        _upNum = parseInt($upNum.text(), 10) || 0,//已上传图片数量并取整
        _id= $("#advertid").val();
    var selected=$(':selected').html();

    //同步更新广告名称
    var update=(function(){
        $('body').on('blur','#advert_title',function(){
            var title=$('#advert_title').val();
            if(title!=''){
                $('#title').html(title);
            }
        })
        $(document).ready(function(){
            var title=$('#advert_title').val();
            if(title!='') {
                $('#title').html(title);
            }
        })
    })();

    //无忧推荐
        var recommend= (function () {
            if($('#isrecommend').val()==0){
                $('#advert_subtitle').val('');
            }
        })();


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
            url: O.path('/pub/image/up-image&sub_folder=merchant&isthumbnail=true'),
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

                    if(flag == 'limit' || $('.img-wrap').length >= 12) {
                        _showTips('选择的图片数量超过12张了');
                        return false;
                    }
                    if($(':selected').html()=='首页广告位（1）'&&$('.img-wrap').length>=4){
                        _showTips('首页广告位（1）最多只能上传4张图片');
                        return false;
                    }
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
                        if(checkaddType(selected)){
                            $(".img-wrap").remove();
                            $imgWraps.html(html);
                        }else{
                            $imgWraps.append(html);
                        }
                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    _upNum++;
                    $upNum.text(_upNum);

                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.result+' />');

                    _upNum == 1 &&  _setCover($('.js-img').eq(0));
                    // if($("#imgup_wrap").find('img').length){
                    //     $("#imgup_wrap").find(".color-red1").hide();
                    //     $('.upload-btn').text('继续添加')
                    // }
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
                        '设置链接地址' : '删除';

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

        // 设置链接地址
        $('body').on('click', '.f-btn', function(e) {//绑定class为f-btn的span
            me=$(this);
            var link_url=$(this).closest('.img-wrap').find('input').val();
            // console.log(link_url);
            box=$.box({
                content: Template($("#link_templ").html()),
                title:  '设置链接地址' ,
                height: 'auto',
                width:'500'
            });
            $('.tips-wrap').find('input').val(link_url);
        });

        $('body').on('click','.js-confirm',function(){
            var link_url=$(this).closest('.tips-wrap').find('input').val();
            me.closest('.img-wrap').find('input').val(link_url);
            box.close();
        })
        $('body').on('click','.btn-close',function(){
            box.close();
        })
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
            _upNum==0&&$('.upload-btn').text('上传')
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

        $('body').on('change','#advert_adsense',function(){
            selected=$(':selected').html();
            if($(this).find('option:selected').html()){
                $(this).next().hide();
            }else{
                $(this).next().show()
            }
            validateTips();
        })

        $('body').on('keyup','.inp-short',function(){
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
                    url: O.path('/advertise/advert/save' + (_isEdit ? '&id=' + _id : '')),
                    success: function(data) {
                        if(data.result == true){
                            showMessage("保存成功","isNormal")
                            $(window).off('beforeunload');//$(window)当前浏览器的窗口  关闭beforeunload事件
                            setTimeout("location.href = '/index.php?r=advertise/advert/index'",500)
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
        var seri = '',image_urls = [];//图片地址的上传
        seri = O.serialize($form, 2);//整个右侧DIV表单序列化
        //图片的信息收集
        $('.img-wrap').each(function(i, v) {//遍历所有上传的图片
            image_urls.push({
                thumb_url : $(v).find('img').attr('_src'),
                original_url : $(v).find('img').attr('src') || '',
                link_url:$(v).find('input').val()
            });
        });
        image_urls = JSON.stringify(image_urls);//stringify()用于从一个对象解析出字符串

        //对表单序列化的东西在加上一些参数 对象的模式  都可以
        return {//一些input，section
            'id': _id,
            'title':  $('#advert_title').val(),
            'adsenseid':  $('#advert_adsense').val(),
            'logo': _coverSrc,
            'advert_images': image_urls,
            'link_url':$('#advert_link').val()||''
        };
    }
    _initDate=_getData();

    var checkaddType=function(selected){
        if(selected=='首页广告位（2）'||selected=='首页广告位（2）'||selected=='首页广告位（3）'||selected=='首页广告位（4）'||selected=='首页广告位（5）'||selected=='首页广告位（6）'||selected=='首页广告位（7）'){
            return true;
        }else{
            return false;
        }
    }
    var validateTips=function(){
        if(selected=='首页广告位（1）'){
            $('.tips').html('只能上传4张图片');
            $('.recommend').show();
        }else if(checkaddType(selected)){
            $('.tips').html('只能上传1张图片');
            $('.recommend').hide();
        }
        else{
            $('.tips').html('每次只能上传一张图片');
            $('.recommend').show();
        }
        $('.color-gray').show();
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
                id: 'advert_title',
                msg: {empty:'请输入广告标题'},
                fun: function() {
                    if (!$('#advert_title').val()) {
                        return 'empty';
                    }
                }
            },
            {
                id: 'advert_adsense',
                msg: {empty: '请选择广告分类'},
                fun: function(el) {
                    if(!el.value) {
                        $('#advert_adsense').next().show();
                        return 'empty';

                    }
                }
            },
            {
                id: 'imgup_wrap',
                msg: {empty:'请至少上传一张图片'},
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
        if(_upNum<$('.img-wrap').length) {//如果已上传的图片数量小于上传成功的图片数量
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
            return false;
        }
        if($(':selected').html()=='首页广告位（1）'&&$('.img-wrap').length>4){
            _showTips('首页广告位最多只能上传4张图片');
            return false;
        }
        if(checkaddType(selected)&&$('.img-wrap').length>1){
            _showTips('首页广告位2~7最只能上传1张图片');
            return false;
        }
        return true;
    };
    var _init = {
        init : function() {//初始化函数
            validateTips();
            _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
            _bindEvent();//绑定事件调用
            _uploadImg();//上传图片调用
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