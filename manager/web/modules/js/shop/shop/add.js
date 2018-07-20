define(function(require,exports,module){
    require('form');
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    require('../../../../frontend/js/plugin/grid.js');
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
        addTpl=$("#grid_shop").html();

    var $imgWraps = $('#img_wraps'),
        $imgupWrap = $('#imgup_wrap'),
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
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var appcode=getQueryString('app_code');
        // 对子页面访问
    var _ue = UE.getEditor('summary');
    var _ue0 =UE.getEditor('uedesc_0'); 
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
      // console.log(_isEdit)
      // if(_isEdit){
      //   $(".next").show();
      // }else{
      //   $(".next").hide();
      // }
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

                    // console.log(flag);
                    // if(flag == 'limit' || $('.img-wrap').length >= 1) {
                    //     _showTips('选择的图片数量超过1张了');
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
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    _upNum++;
                    $upNum.text(_upNum);
                    
                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.original_return+' />');

                   // if(_upNum == 1){_setCover($('.js-img').eq(0));}

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
       var cache={
                type:""
            }
        var loaded=function(container,url,id,appcode){
            console.log(appcode);
            container.find('tbody').html('<tr><td colspan="'+container.find('thead tr.on th').length+'"class="align-c" style="height:70px;">正在加载数据...</td></tr>');
       
       container.grid({
            idField: 'id',
            templateid: 'holiday_templ',
            pagesize: '10',
            emptyText: '无数据',
            method: 'get',
            queryParams: function () {
                // var paramEls = searchCon.find('.form-control:visible');
                cache.params={};
                cache.params['keywords']=$('input[name='+id+']').val();
                cache.params['app_code']=appcode;
                return $.param(cache.params);

            },
            getReadURL : function(){
                var strurl = url;
                return O.path(strurl);
            },
            filter : function(model){
                model.set('type',cache.type);
            },
            loaded:function(data){
            
               // console.log(data);
                cache.total = data.total;
            }
        });

     };
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
          $(document).on('click',function(){
             var e = e || window.event; //浏览器兼容性   
                var elem = e.target ;
                // console.log($(elem).closest(".shop_grid").length);
                 if ($(elem).closest(".shop_grid").length||$(elem).closest(".type_grid").length) {  

                        return;  
                 }    
                      
                $(".shop_grid").slideUp(300);
                $(".type_grid").slideUp(300);
            })
          var page=0,paged=0;
         $(".shop").click(function(e){
            e.stopPropagation()
            page++
            var shopGrid=$("#shop_grid");
            if(_isEdit&&page==1){
              $("#seller_id").val("");  
            }
            $(".shop_grid").slideDown(300);
            // $(".products").show();
             
            
    
         loaded(shopGrid,"/shop/shop/ajax-seller-list","seller_id",appcode);

         });
         $(".type").click(function(e){
            e.stopPropagation()
            paged++;
            var typeGrid=$("#type_grid");
            if(_isEdit&&paged==1){
              $("#type_id").val("");  
            }
            // $("input[name='type_id']").val("")
            $(".type_grid").slideDown(300);
            // $(".products").show();
            loaded(typeGrid,"/shop/type/ajax-type-list","type_id",appcode);
             
         })
        $("body").on("click",".sure",function(e){
            e.stopPropagation()
              $(this).closest(".shop_grid").length?$(".shop_grid").slideUp(300):$(".type_grid").slideUp(300);
        })
        $("body").on("click","#shop_grid .checkout",function(e){
            e.stopPropagation()
                $(this).attr("checked","checked");
                $(this).closest("tr").siblings("tr").find(".checkout").attr("checked",false);
                // $(".shop_grid").slideToggle(300);
                if($(this).attr("checked")){
                  $("input[name='seller_id']").val($(this).closest("tr").find(".name_check").html());
                  $("input[name='seller_id']").attr("data-seller",$(this).attr("data-id"));
                  $("input[name='seller_id']").attr("data-pid",$(this).attr("data-pid"));
                  // var id=$("[type='checkbox'][checked]").attr("data-id");console.log(id);
                  
               }
                // $("input[name=type]").val($(this).closest("tr").find(".name_check").html());
        })
        $("body").on("click","#type_grid .checkout",function(e){
            e.stopPropagation()
                $(this).attr("checked","checked");
                $(this).closest("tr").siblings("tr").find(".checkout").attr("checked",false);
                // $("#type_grid").slideToggle(300);
                if($(this).attr("checked")){
                  $("input[name='type_id']").val($(this).closest("tr").find(".name_check").html());
                  $("input[name='type_id']").attr("data-type",$(this).attr("data-id"));
                  
                  // var id=$("[type='checkbox'][checked]").attr("data-id");console.log(id);
                  
               }
                // $("input[name=type]").val($(this).closest("tr").find(".name_check").html());
            })
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
        
        // $('#type_id').change(function(){
        //     var typeid = $(this).val();
        //     if(typeid){
        //         $('#type').val(typeid);  
        //     }else{
        //         $('#type').val('');
        //     }
        // });

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
        // $('#add_desc').on('click', function() {
        //     var html = Template(_addTempl, {id: _ueNum});
        //     // $form.append(html);
        //     $(html).insertBefore(this);
        //     _UEArr.push([UE.getEditor('uedesc_' + _ueNum), 'uedesc_' + _ueNum, 'uewrap_' + _ueNum]);
        //     _ueNum++;
        // });
        //发布
         $('.submit_btn').on('click', function() {
            if(_doCheck()&&$(this).hasClass("next")) {
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                var curEle = $(this);
                var data = _getData();
                data.seller_id=$("#seller_id").attr("data-seller");
                data.type_id=$("#type_id").attr("data-type");
                data.type_pid=$("#seller_id").attr("data-pid");
                // var url;
                // url = _isEdit ? '/news/news/add?id=' + $('#newsId').val() : '/news/news/save';
                // / console.log(url);
                 // console.log(data);
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/shop/shop/save'+ (_isEdit ? '&id=' + $('#newsId').val() : '')),
                    success: function(data) {
                        if(data.result == true){
                            showMessage('保存成功','isNormal');
                            
                            // $(window).off('beforeunload');
                            setTimeout("location.reload()",1000);
                            // $(window).off('beforeunload');
                            // location.href = '/index.php?r=shop/shop/index'
                        }else{
                            _showTips(data.msg);
                            curEle.attr("disabled","false").removeClass("color-gray").addClass("bg-green");
                        }
                    },
                    error: function() {
                        _showTips('网络错误');
                    }
                });
            }else if(_doCheck()&&!$(this).hasClass("next")){
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                var curEle = $(this);
                var data = _getData();
                data.seller_id=$("#seller_id").attr("data-seller");
                data.type_id=$("#type_id").attr("data-type");
                data.type_pid=$("#seller_id").attr("data-pid");
                // var url;
                // url = _isEdit ? '/news/news/add?id=' + $('#newsId').val() : '/news/news/save';
                // / console.log(url);
                 // console.log(data);
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/shop/shop/save'+ (_isEdit ? '&id=' + $('#newsId').val() : '')),
                    success: function(data) {
                        if(data.result == true){
                            showMessage('保存成功','isNormal');
                            
                            $(window).off('beforeunload');
                            // setTimeout("location.reload()",1000);
                            // $(window).off('beforeunload');
                            location.href =$('#url').val();
                        }else{
                            _showTips(data.msg);
                            curEle.attr("disabled","false").removeClass("color-gray").addClass("bg-green");
                        }
                    },
                    error: function() {
                        _showTips('网络错误');
                    }
                });
                $(window).on('beforeunload', function(e) {
                    if(!O.compare(_initDate, _getData())) {
                    return '离开后，刚刚填写数据会丢失';
                   }
                });
            }else {
               return false; 
            }
        }); 
 
        
    };
     var showMessage=function(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
     //获取得到的数据  提交数据用
    var _getData = function() {
        var seri = '',ueCon = '';//图片地址的上传 
        seri = O.serialize($form, 2);
        //图片的信息收集
        //商品介绍的内容
        var ueConProduce = _ue.getContent();//getContent()获得内容的方法;
        for(var i = 0, len = _UEArr.length; i < len; i++) {
            if(_UEArr[i].length === 0) continue; 
            ueCon = _UEArr[i][0].getContent();
            if(ueCon == '') continue; 
            // goods_exts.push({
            //     title: $('#' + _UEArr[i][2]).find('.name').text(),
            //     content: ueCon
            // });
        }
         // goods_exts = JSON.stringify(goods_exts);
        seri.content=ueCon;
        seri.summary = ueConProduce;
        seri.logo = $('.js-img').attr('src');
        delete seri.type;
        seri.app_code=appcode;
        return seri;
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
                id: 'name', 
                msg: {
                    empty: '商品名称不能为空' 
                },
                fun: function(){
                    var title = $('#name').val();
                    if(title.length === 0)
                    return 'empty';
                } 
            },{
                id: 'seller_id',
                msg:{
                    empty: '请选择商家',
                    
                },
                fun: function(){
                    var type = $('#seller_id').attr("data-seller");
                    if(type.length === 0){
                       return 'empty'; 
                    }
                    
                }
            },
            {
                id: 'type_id',
                msg:{
                    empty: '请选择商品分类',
                    
                },
                fun: function(){
                    var type = $('#type_id').attr("data-type");
                    if(type.length === 0){
                       return 'empty'; 
                    }
                    
                }
            },
            {
                id: 'summary', 
                msg: {
                    empty: '基本信息不能为空' 
                },
                fun: function(el) {
                    var ueCon = _ue.getContent(); 
                    if(ueCon.length === 0) 
                    return 'empty';
                }   
            },
            {
                id: 'uedesc_0', 
                msg: {
                    empty: '商品简介不能为空' 
                },
                fun: function(el) {
                    var ueCon = _ue0.getContent(); 
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
        if(!$('.js-img').attr('src')) {
            _showTips('还有未上传成功的图片，不能立即保存');
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
        $("#title").html($(this).val());
    });
    