define(function(require, exports, module) {
    require('../../3rd/plupload/plupload.full.min');
    require('../lib/dialog');
    require('../lib/tooltips/tooltips');
    window.Template = require('../lib/template');

    jQuery.fn.uploadimg = function(options){
        var $this = $(this),
            beforeUpload = options.beforeUpload,
            browse_button = options.uploadbutton || 'upload_btn',
            url = options.url,
            imgNumLimit = options.imgNumLimit || 12,
            imgSizeLimit = options.imgSizeLimit || 10240,
            $imgWraps = $this.find('.img-wraps').eq(0),
            $imgupWrap = $this.find('.uploadimg-wrap'),
            $upNum = $this.find('.up_num').eq(0),
            _upNum = parseInt($upNum.text(), 10) || 0,
            $coverBox = $('.img-cover'),
            _coverSrc = $coverBox.find('img').attr('src') || '',
            defaultImages = options.defaultImages || null,
            $deleteImgWrap = $('.img-wrap');
        
        var _uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : browse_button, 
            container : $imgupWrap[0],
            url : url,
            flash_swf_url : '/frontend/3rd/plupload/Moxie.swf',
            silverlight_xap_url : '/frontend/3rd/plupload/Moxie.xap',

            /*filters : {
                max_file_size : '10mb',
                mime_types: [
                    {title : "图片类型", extensions : "jpg,gif,png"}
                ]
            },*/

            init: {
                FilesAdded: function(up, files) {
                    var flag = false, 
                        limit = up.files.length > imgNumLimit,
                        limit2 = $('.img-wrap').length >= imgNumLimit;

                    plupload.each(files, function(file) {
                        if(limit || limit2) {
                            flag = 'limit';
                            return false;
                        }

                        if(file.size / 1024 > imgSizeLimit) {
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
                        _showTips('亲，选择的图片数量超过'+ imgNumLimit + '张了哟！');
                        return false;
                    }

                    if(flag == 'size') {
                        _showTips('亲，每张最大支持' + imgSizeLimit / 1024 + 'M哟！');
                        return false;
                    }

                    if(flag == 'type') {
                        _showTips('亲，只支持jpg / gif / png格式哟！');
                        return false;
                    }

                    plupload.each(files, function(file) {
                        var html = Template(_imgTempl, {id: file.id});
                        $imgWraps.append(html);
                    });

                    if(!beforeUpload || beforeUpload.apply(this,arguments)!=false){
                        _uploader.start();
                    }
                },
                FileUploaded: function(up, file, info) {
                    var data = JSON.parse(info.response) || {};
                    // var r = Math.random() > 0.5 ? '1' : '0';
                    _upNum++;
                    $upNum.text(_upNum);

                    $('#' + file.id).find('.img-box')
                        .html('<img src="' + data.result + '" class="js-img"/>');
                        // .html('<img src="/images/modules/renting/default/test/bg-' + r +'.jpg" class="js-img"/>');

                    _upNum == 1 &&  _setCover($this.find('.js-img').eq(0));

                    _uploader.refresh();//刷新按钮的位置
                    O.emit('img:uploaded', {num: _upNum});//触发事件
                },

                UploadProgress: function(up, file) {
                    var target = $('#' + file.id);
                    target.find('.wait').text(file.percent + '%');
                    target.find('.pct').css('width', file.percent + '%');
                },

                Error: function(up, err) {
                    $('#' + err.file.id).find('.img-box')
                        .html('<p>' + err.code + "，" + err.message + '</p>');
                    up.removeFile(err.file.id);
                    /*_upNum--;
                    $upNum.text(_upNum);*/
                }
            }
        });
        
        var _showTips = function(tip) {
            var d = $.tips(tip);
            setTimeout(function() {
                d.close().remove();
            }, 2000);
        }
        
        var _setCover = function(el) {
            var src = el.attr('src');
            _coverSrc = src;//封面
            $coverBox.html('<img width="100%" height="100%" src="' + src + '" />');
        };
        
        var _imgTempl='<div class="img-wrap" id="<%-id%>">'
            +'  <div class="img-box">'
            +'      <p class="wait">等待中...</p>'
            +'      <div class="per"><span class="pct"></span></div>'
            +'  </div>'
            +'  <div class="opeate">'
            +'      <span class="opt-btn f-btn">'
            +'          <span class="icon-merge icon"></span>'
            +'      </span>'
            +'      <span class="opt-btn d-btn">'
            +'          <span class="icon-merge icon"></span>'
            +'      </span>'
            +'  </div>'
            +'</div>';
    
        var _deleteTempl = '<div class="tips-wrap delete-tips js-upimg-wrap">'
            +'<div class="content"><div class="delete-info">确定删除？</div></div>'
            +'<button type="button" class="btn-pr ok-btn">确定</button>'
            +'<button type="button" class="btn-pr cancel-btn">取消</button'
            +'</div>';
        
        var _bindEvent = function() {
            $this.on('mouseenter mouseleave', '.opt-btn', function(e) {
                if(e.type == 'mouseenter') {
                    var content = $(this).hasClass('f-btn') ?'设为封面' : '删除';

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

            $this.on('click', '.f-btn', function(e) {
                _setCover($(this).closest('.img-wrap').find('.js-img'));
            });

            // 删除图片确认
            $this.on('click', '.d-btn', function(e) {
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
            $('body').on('click', '.js-upimg-wrap .ok-btn', function(e) {
                _upNum--; _upNum = _upNum > 0 ? _upNum : 0;
                if($deleteImgWrap.attr('id')){
                    var file = _uploader.getFile($deleteImgWrap.attr('id'));
                    if(file){
                        _uploader.removeFile(file);
                    }
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
        };
        
        var init= function(){
            _bindEvent();
            _uploader.init();
            
            if(defaultImages){
                if(defaultImages.images&&defaultImages.images.length>0){
                    $.each(defaultImages.images, function(index,file) {
                        file.id = file.id || 'img_wrap_' + index;
                        var html = Template(_imgTempl, {id: file.id});
                        $imgWraps.append(html);
                        _upNum++;
                        $upNum.text(_upNum);
                        // _uploader.addFile(file.id)

                        $('#' + file.id).find('.img-box').html('<img src="' + file.url + '" class="js-img"/>');

                        _upNum == 1 && defaultImages.images.length == 1 &&
                             _setCover($this.find('.js-img').eq(0));

                        _uploader.refresh();//刷新按钮的位置
                    });
                }
                
                if(defaultImages.cover){
                    _coverSrc = defaultImages.cover;
                    $coverBox.html('<img width="100%" height="100%" src="' + _coverSrc + '" />');
                }
            }
        }
        init();
        
        return {
            getImages : function(){
                var image_urls=[];
                $this.find('.js-img').each(function(i, v) {
                    image_urls.push($(v).attr('src'));
                });
                return image_urls;
            },
            getCover : function(){
                return _coverSrc;
            }
        }
    }
})

