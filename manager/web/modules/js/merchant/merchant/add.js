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
        $upNum = $('#up_num'),  //图片数量id
        $form = $('#form'),//右侧最外层div
        $title = $('#title');//左侧广告名称id
     
     var _uploader = null, _validate,_city,_type,_initDate = null,
        $deleteImgWrap,   $editName,    $deleteUeWrap, _UEArr = [], 
         _isEdit = $('#is_edit').val(),
         _coverSrc = $coverBox.find('img').attr('src') || '',  //封面图片的路径不为真输出空
         title = $title.html(),  //广告名称内容
        _upNum = parseInt($upNum.text(), 10) || 0, //图片数量
        _id,_types = [],_typess,code;
        // 对子页面访问
    var _proxy = window.Proxy = {
        dialog: null,
        getPos: function() {
            return [
                $.trim($proLon.val()) || 0,//为真返回 为假返回0
                $.trim($prlLat.val()) || 0
            ]
        },
        setPos: function(pos) {
            if(pos[0]) {
                $proLon.val(pos[0]);//输出经纬度的值
                $prlLat.val(pos[1]);
            }
        },
        setAddr: function(addr) {
            addr && $proAddr.val(addr);//输出地址
        }
    };
    var _ue = UE.getEditor('goodsinfo'); 
    var _ueNum = $('.ueedit-box').filter("[sign='ext']").length, _count = _ueNum;
    //初始化文本编辑器
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
    //城市子级
    $('#region').change(function(){
        // console.log($('#region').val());
        var cityid = $('#region').val();
        O.ajaxEx({
            type: 'get',
            data: {id:cityid},
            url: O.path('/merchant/merchant/find-son'),
            success: function(data) {
                // console.log(data);
                $('#town').show();
                var html = Template(_town_tmp, {data: data});
                $('#town').html(html); 
            }
        });
    });
    //分类子级
    $('#type').change(function(){
        var typeid = $('#type').val();
        code = $('#type option:selected').text();
        if(code){
            O.ajaxEx({
                type: 'get',
                data: {id:typeid},
                url: O.path('/merchant/merchant/find-sellerson'),
                success: function(data) {
                    // console.log(data);
                    // $('#type_checkbox').hide();
                    $('#types').show();
                    var html = Template(_type_tmp, {data: data});
                    $('#types').html(html); 
                }
            });
        }
        // }else{
        //     $('#types').hide();
        //     $('#type_checkbox').show();
        // }
    });
    //上传图片
      // 设置封面
    var _setCover = function(el) {
        var src = el.attr('src');
        _coverSrc = src;//封面
        $coverBox.html('<img width="100%" height="100%" src="' + src + '" />');
    };
    var _uploadImg = function() { 
        _uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : 'upload_btn', 
            container : $imgupWrap[0],
            url: O.path('/pub/image/up-image&sub_folder=shop'),
            flash_swf_url : '/components/plupload/Moxie.swf',
            silverlight_xap_url : '/components/plupload/Moxie.xap', 
            init: {
                  FilesAdded: function(up, files) {
                    var flag = false, limit = up.files.length > 60;
                    plupload.each(files, function(file) {
                        if(limit) {
                            flag = 'limit';
                            return false;
                        }

                        if(file.size / 1024 > 2048) {
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

                    if(flag == 'limit' || $('.img-wrap').length >= 60) {
                        _showTips('选择的图片数量超过60张了');
                        return false;
                    }

                    if(flag == 'size') {
                        _showTips('每张最大支持2M');
                        return false;
                    }

                    if(flag == 'type') {
                        _showTips('只支持jpg / gif / png格式');
                        return false;
                    }
                    
                    plupload.each(files, function(file) {
                        var html = Template(_imgTempl, {id: file.id});
                        $imgWraps.append(html);
                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    _upNum++;
                    $upNum.text(_upNum);
                    
                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.original_return+' />');

                   if(_upNum == 1){_setCover($('.js-img').eq(0));}

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

        $('body').on('mouseenter mouseleave', '.text-wrap .icon-edit,.text-wrap .icon-delete', function(e) {
            if(e.type == 'mouseenter') {
                var content = 
                    $(this).hasClass('icon-edit') ?
                    '编辑名称' : '删除';

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
            var lat = $('#is_lat').val();
            var lng = $('#is_lng').val();
            _proxy.dialog = $.dialog({
                url: "/city/city/map?lat="+lat+"&lng="+lng,
                title: '设置坐标',
                id: 'js_map',
                width: 640,
                height: 450,
                onshow: function() {
                    //console.log(60)
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
            _upNum--; _upNum = _upNum > 0 ? _upNum : 0;
            if($deleteImgWrap.attr('id')){
                _uploader.removeFile(_uploader.getFile($deleteImgWrap.attr('id')));
            }
            $upNum.text(_upNum);

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
        
        
        //编辑文本编辑器
        $('body').on('click', '.icon-edit', function() {
            $editName = $(this).closest('p').find('.name');
            $.pt({
                target: this,
                width: 286,
                position: 'b', 
                align: 'c',   
                autoClose: false,
                leaveClose: false,
                content: _editTempl.replace('{name}', $editName.text())
            });
        });
        //确认编辑
        $('body').on('click', '.edit-btn', function(e) {
            var input = $('.edit-inp'),
                error = $('.edit-error'),
                val = $.trim(input.val());
            if(val === '') {
                error.removeClass('hide');
                return;
            } else {
                error.addClass('hide');
            }
            $editName.text(val);
            $('.pt').hide();
        });
       // 删除文本编辑器确认
        $('body').on('click', '.icon-delete', function(e) {
            $.pt({
                target: this,
                width: 286,
                position: 'b', 
                align: 'c',   
                autoClose: false,
                leaveClose: false,
                content:_deleteUeTempl
            });
            $deleteUeWrap = $(this).closest('.text-wrap');
        });
        // 删除文本编辑器
        $('body').on('click', '.js-delete-ue', function(e) {
            var box = $deleteUeWrap,
                num  = box.attr('id').split('_')[1];

            box.remove();
            _UEArr[num].length = 0;
            $('.pt').hide();
        });
          $('#add_desc').on('click', function() {
            var html = Template(_addTempl, {id: _ueNum});
            // $form.append(html);
            $(html).insertBefore(this);
            _UEArr.push([UE.getEditor('uedesc_' + _ueNum), 'uedesc_' + _ueNum, 'uewrap_' + _ueNum]);
            _ueNum++;
        });
        //发布
         $('#submit_btn').on('click', function() {
            if($("#type option:selected").val().length!=0){
                $("#type").next().hide();
            }
            var check=$("input[name='types']:checked");//选中的复选框  
            check.each(function(){  
                _types.push($(this).val());
            }); 
            _typess = _types.join(",");
            if(_doCheck()) {
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                  var curEle = $(this);
                var data = _getData();
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/merchant/merchant/save' + (_isEdit ? '&id=' + _id : '')),
                    success: function(data) {
                        // console.log(data);
                        if(data.result == true){
                            $(window).off('beforeunload');
                            location.href = '/index.php?r=merchant/merchant/index'
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
        var seri = '', ueCon = '',  goods_exts = [],image_urls = [];//图片地址的上传 
        seri = O.serialize($form, 2);
        //图片的信息收集
        $('.js-img').each(function(i, v) {
            image_urls.push({
                thumb_url : $(v).attr('src'),
                original_url : $(v).attr('_src') || ''
            });
        });
        image_urls = JSON.stringify(image_urls);
        //商品介绍的内容
        var ueConGoodsinfo = _ue.getContent();    
        //其他信息的内容
        for(var i = 0, len = _UEArr.length; i < len; i++) {
            if(_UEArr[i].length === 0) continue; 
            ueCon = _UEArr[i][0].getContent();
            if(ueCon == '') continue; 
            goods_exts.push({
                title: $('#' + _UEArr[i][2]).find('.name').text(),
                content: ueCon
            });
        }
         goods_exts = JSON.stringify(goods_exts);
        
        return {
            'id': _id,                  //id
            'name':  $('#goodsname').val(), //商家名称
            'linktel':$('#linktel').val(),   //电话
            'is_recommend':$('input[name="is_recommend"]:checked').val(),//是否上架
            'address':$('#advert_address').val(),//地址
            'linkman':$('#linkman').val(),//联系人
            'longitudes':$('#advert_longitudes').val(),//经度   
            'latitudes':$('#advert_latitudes').val(),//纬度
            'summary' :$('#summary').val(),//简介
            'remind' :$('#remind').val(),//特别提醒
            'type_pid' : $('#type').val(),
            'fax':$("#fax").val(),
            'mail':$("#mail").val(),
            'type_id' : code == '房产' ? '' : $('#types').val(),
            'tag_id' : code == '房产' ? _typess : '',
            'city_pid' :$('#region').val(),
            'city_id' :$('#town').val(),
            'logo': _coverSrc,//封面图片
            'content':ueConGoodsinfo,//商家详情 
            'goods_images': image_urls,//图片
            'is_deleted': $('#is_deleted').val(),
            'typename' : code == '房产' ? '房产' : '',
            'sort' : $('#sort').val()||''
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
                id: 'goodsname', 
                msg: {
                        empty: '商家名称不能为空' 
                },
                fun: function(){
                    var goodsname = $('#goodsname').val();
                    if(goodsname.length === 0)
                    return 'empty';
                } 
            },{
                id: 'goodsinfo', 
                msg: {
                        empty: '详情不能为空' 
                },
                fun: function(el) {
                       var ueCon = _ue.getContent(); 
                       if(ueCon.length === 0) 
                        return 'empty';
                } 
            }, 
            {
                id: 'linktel', 
                msg: {
                        empty: '电话不能为空' 
                },
                fun: function(){
                    var linktel = $('#linktel').val();
                    if(linktel.length === 0)
                    return 'empty';
                } 
            },
            {
                id: 'fax', 
                msg: {
                        empty: '传真不能为空' 
                },
                fun: function(){
                    var fax = $('#fax').val();
                    if(fax.length === 0)
                    return 'empty';
                } 
            },
            {
                id: 'mail', 
                msg: {
                        empty: '邮箱不能为空' 
                },
                fun: function(){
                    var mail = $('#mail').val();
                    if(mail.length === 0)
                    return 'empty';
                } 
            },
            {
                id: 'linkman', 
                msg: {
                        empty: '联系人不能为空' 
                },
                fun: function(){
                    var linkman = $('#linkman').val();
                    if(linkman.length === 0)
                    return 'empty';
                } 
            },
            {
                id: 'summary', 
                msg: {
                        empty: '简介不能为空' 
                },
                fun: function(){
                    var summary = $('#summary').val();
                    if(summary.length === 0)
                    return 'empty';
                } 
            },
            {
                id: 'remind', 
                msg: {
                        empty: '特别提醒不能为空' 
                },
                fun: function(){
                    var remind = $('#remind').val();
                    if(remind.length === 0)
                    return 'empty';
                } 
            },
            {
                id: 'type', 
                msg: {
                        empty: '请选择商家类型' 
                },
                fun: function(){
                    var type = $('#type option:selected').val();
                    if(type.length === 0){
                        $("#type").next().show();
                    }
                   
                } 
            }, 
            {
                id: 'regions',
                msg: {empty:'请选择城市'},
                fun: function() {
                    var region = $('#region').val();
                    if(region.length === 0)
                    return 'empty';
                }
            },
            {
                id: 'advert_address',
                msg: {empty:'请在地图选择商家位置'},
                fun: function() {
                    var advert_longitudes = $('#advert_longitudes').val();
                    if(advert_longitudes.length === 0)
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
        if(_upNum < $('.img-wrap').length) {
            _showTips('还有未上传成功的图片，不能立即保存');
            return false;
        }
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
        var interval = setInterval(function() {
            if(_count <= 0) {
                clearInterval(interval);
                _initDate = _getData();
                _count++;
            }
        }, 100);
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
                    // if($('#type option:selected').text()=='房产'){
                    //     $('#types').hide();
                    //     $('#type_checkbox').show();
                    //     var is_tsg = $('#is_tsg').val().split(',');
                    //         for(var i in is_tsg){
                    //          $("input[name='types'][value='"+is_tsg[i]+"']").attr("checked", 'checked');
                    //          code = '房产';
                    //     }
                    // }else{
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
                    // }
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

$("#goodsname").blur(function(){
  $("#title").html($("#goodsname").val());
});
    