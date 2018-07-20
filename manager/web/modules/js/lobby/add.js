define(function(require,exports,module){
    require('form');
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    require('/frontend/3rd/laydate/laydate');
    window.Template = require('/frontend/js/lib/template');
    window.DataValid = require('/frontend/js/lib/validate');
    
//  模板
    var _imgTempl = $('#img_templ').html(),    //图片上传模板
        _deleteTempl = $('#de_templ').html(),
        _editTempl = $('#edit_templ').html(),
        _addTempl = $('#add_templ').html(),
        _pretempl = $('#de_pre_templ').html(),
        _deleteUeTempl = $('#de_ue_templ').html();
        _town_tmp = $('#town_tmp').html();
        _type_tmp = $('#type_tmp').html();

    var $imgWraps = $('#img_wraps'),
        $imgupWrap = $('#imgup_wrap'),
        $selectMap = $('#select_map'),//选择地图ID 
        $proLon = $('#advert_longitudes'),//经度
        $prlLat = $('#advert_latitudes'),//纬度
        $proAddr = $('#advert_address'),//地图查询地址 
        $coverBox = $('#cover_box'),//封面图片
        $form = $('#form'),//右侧最外层div
        $title = $('#title');//左侧广告名称id
     
     var _uploader = null, _validate,_city,_type,_initDate = null,
        $deleteImgWrap,   $editName,image_urls,$deleteUeWrap, _UEArr = [], 
         _isEdit = $('#is_edit').val(),
         _coverSrc = $coverBox.find('img').attr('src') || '',  //封面图片的路径不为真输出空
         title = $title.html(),  //广告名称内容
        _id,_types = [],_typess,code;
    var _ue = UE.getEditor('goodsinfo'); 
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
          $('body').on('mouseenter mouseleave', '.opt-btn', function(e) {
            if(e.type == 'mouseenter') {
                var content = 
                    $(this).hasClass('f-btn') ?
                    '设为封面' : '删除';

                $.pt({
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
 
        // 设为封面
        $('body').on('click', '.f-btn', function(e) {
            _setCover($(this).closest('.img-wrap').find('.js-img'));
        });
        $selectMap.on('click', function() {//调用弹出框模板引入地图
            _proxy.dialog = $.dialog({
                url: '/index.php?r=city/city/map',
                title: '设置坐标',
                id: 'js_map',
                width: 640,
                height: 450,
                onshow: function() {
                    //console.log(1)
                }
            }).show();
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

            if($('.img-wrap').length === 0) {
                $coverBox.html('<span>封面图片</span>');
            } else {
                _setCover($('.js-img').eq(0));
            }
        });

        $('body').on('click', '.cancel-btn', function(e) {
            $('.pt').hide();
        });
        //发布
         $('#submit_btn').on('click', function() {
            if(_doCheck()) {
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                  var curEle = $(this);
                var data = _getData();
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/lobby/lobby/save' + (_isEdit ? '&id=' + _id : '')),
                    success: function(data) {
                        if(data.result == true){
                            $(window).off('beforeunload');
                            location.href = '/index.php?r=lobby/lobby/index'
                        }else{
                            _showTips(data.msg);
                            curEle.attr("disabled","false").removeClass("color-gray").addClass("bg-green");
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
        var seri = '', ueCon = '',  goods_exts = [];//图片地址的上传 
        seri = O.serialize($form, 2);
        image_urls = $('.js-img').attr('src');
        //商品介绍的内容
        var ueConGoodsinfo = _ue.getContent();
        return {
            'id': _id,                  //id
            'is_deleted': $('#is_deleted').val(),
            'title' : $('#title').val(),
            'photo' : image_urls,
            'content' : ueConGoodsinfo
        };
    };

    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    }

    //验证配置、规则
    var _checkCfg = {
        config: function() {
            return [{
                id: 'title', 
                msg: {
                        empty: '标题不能为空' 
                },
                fun: function(){
                    var title = $('#title').val();
                    if(title.length === 0)
                    return 'empty';
                } 
            },{
                id: 'goodsinfoname', 
                msg: {
                        empty: '游说介绍不能为空' 
                },
                fun: function(el) {
                       var ueCon = _ue.getContent(); 
                       if(ueCon.length === 0) 
                        return 'empty';
                } 
            },                         
            {
                id: 'imgup_wrap',
                msg: {empty:'必须插入一张图片'},
                fun: function() {
                    if($('.img-wrap').length === 0) 
                        return 'empty';
                },
                appendFn: function(newEl, el) {
                    $imgupWrap.append(newEl);
                }
            },
            ];
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
   _ue.ready($.proxy(function() {
        _isEdit && this.ue.setContent($('<div />').html($('#js-detail').html()).text());
   }, {ue: _ue}));
   var _count=0;
    var _init = {
        init : function() {  
            _bindEvent();
            _uploadImg(); 
              _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
              if (!_isEdit) { 
                //特点单选框
                $("input[name='is_recommend'][value='0']").attr("checked", 'checked');
            } else {
                $("input[name='is_recommend'][value='" + $('#is_recommend').val() + "']").attr("checked", 'checked');
                if($('#is_type').val()){
                    $('#type').val($('#is_type').val());
                    if($('#type option:selected').text()=='房产'){
                        $('#types').hide();
                        $('#type_checkbox').show();
                        var is_tsg = $('#is_tsg').val().split(',');
                            for(var i in is_tsg){
                             $("input[name='types'][value='"+is_tsg[i]+"']").attr("checked", 'checked');
                             code = '房产';
                        }
                    }else{
                        $('#types').show();
                        $('#type_checkbox').hide();
                        O.ajaxEx({
                            type: 'get',
                            data: {id:$('#is_type').val()},
                            url: O.path('/merchant/merchant/find-sellerson'),
                            success: function(data) {
                                $('#type_checkbox').hide();
                                $('#types').show();
                                var html = Template(_type_tmp, {data: data});
                                $('#types').html(html);
                                $('#types').val($('#is_types').val()); 
                            }
                        });
                    }
                }
                if($('#is_city').val()){
                    $('#region').val($('#is_city').val());
                    O.ajaxEx({
                        type: 'get',
                        data: {id:$('#is_city').val()},
                        url: O.path('/merchant/merchant/find-son'),
                        success: function(data) {
                            $('#town').show();
                            var html = Template(_town_tmp, {data: data});
                            $('#town').html(html);
                            if($('#is_citys').val()){
                                $('#town').val($('#is_citys').val()); 
                            }
                        }
                    });
                }
                _id = $("#id").val();        
            } 
        }
    };
    return _init;
});
    //输入字数显示
    function setShowLength(obj, maxlength, id){  
            var rem =obj.value.length; 
            var wid = id; 
            if (rem > maxlength){ 
            rem = maxlength; 
            } 
            document.getElementById(wid).innerHTML = rem + "/"+maxlength; 
        };

$("#title").blur(function(){
  $("#logo").html($("#title").val());
});
    