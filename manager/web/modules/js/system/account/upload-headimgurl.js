 
seajs.use(['utils','/frontend/js/widgets/weixinEdition/weixinEdition.js','/frontend/js/lib/dialog','/frontend/js/plugin/tab'],function(utils){
     var _uploader = null, 
        $deleteImgWrap,    
        _upNum =  0 ;
          var _imgTempl = $('#img_templhead').html(),
        _deleteTempl = $('#de_templhead').html() ;
         

    var $imgWraps = $('#headimg_urlconver') ;
     //上传图片
    var _uploadImg = function() { 
        _uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : 'headimg_url', 
//            container : $imgupWrap[0],
            url: O.path('/pub/image/up-image&sub_folder=account'),
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
                        if(['image/jpg','image/jpeg'].indexOf(file.type) < 0 ) {
                            flag = 'type';
                            return false;
                        }
                    });

                    if(flag) {
                        plupload.each(files, function(file) {
                            up.removeFile(file);
                        });
                    }

                    if(flag == 'limit' || $imgWraps.find('.img-wrap').length >= 1) {
                        _showTips('选择的图片数量超过1张了');
                        return false;
                    }

                    if(flag == 'size') {
                        _showTips('每张最大支持10M');
                        return false;
                    }

                    if(flag == 'type') {
                        _showTips('只支持jpg,jpeg格式');
                        return false;
                    }
                    
                    plupload.each(files, function(file) {
                        $("#headimg_url").hide();
                        var html = Template(_imgTempl, {id: file.id});
                        $imgWraps.append(html);
                    });

                    _uploader.start();
                },
                FileUploaded: function(up, file, info) { 
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    _upNum++; 
                    $('#' + file.id).find('.img-box').html('<img src="' + data.original_return + '" class="js-img" _src='+data.original_return+' />');  
                     var accountId= O.getQueryStr('id'); 
                    if(accountId && accountId.length>0){
                        updateAccount(accountId,'headimg_url',data.original_return); 
                    }
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
  var updateAccount = function(id,column,value){
        var data={
            id : id,
            column : column,
            value : value
        }
        O.ajaxEx({
            url: O.path('system/account/update-account'),
            data: data,
            type: 'get',
            success: function(json) {
                if(!json.result) {
                    $.topTips({tip_text:json.msg});
                }else{    
                    $.topTips({tip_text:'修改成功！'}); 
                }
            },
            error: errorCallback
        })
    }  
    var errorCallback = function() {
        $.topTips({mode:'warning',tip_text:'出现异常'});
    }
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
        $('body').on('click', '.cancel-btn', function(e) {
            $('.pt').hide();
        });
        // 删除图片确认
        $('body').on('click', '.opt-head', function(e) {
            $.pt({
                target: this,
                width: 286,
                position: 'b', 
                align: 'c',   
                autoClose: false,
                leaveClose: false,
                content:_deleteTempl
            });
            $deleteImgWrap = $(this).closest('.img-head');
        });

        // 删除图片
        $('body').on('click', '.delete-btnhead', function(e) { 
            _upNum--; _upNum = _upNum > 0 ? _upNum : 0;
            if($deleteImgWrap.attr('id')){
                _uploader.removeFile(_uploader.getFile($deleteImgWrap.attr('id')));
            } 
            $deleteImgWrap.remove();
            $('.pt').hide();
            _uploader && _uploader.refresh();
             var accountId= O.getQueryStr('id'); 
            if(accountId && accountId.length>0){
                updateAccount(accountId,'headimg_url',''); 
            }
            $("#headimg_url").show(); 
        }); 
    };
    
   _uploadImg();
   _bindEvent();
    var errorCallback = function() {
        $.topTips({mode:'tips',tip_text:'出现异常'});
    }
     var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    }
})
