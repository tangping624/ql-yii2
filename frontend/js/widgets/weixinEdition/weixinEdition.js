define(function (require, exports, module) {
    //需要在页面引用<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
    require('./css/msg_sender.css');
    require('../../plugin/tab');
    require('../../lib/dialog');
    require('../../lib/template');
    
    var emotionArr=['微笑','撇嘴','色','发呆','得意','流泪','害羞','闭嘴','睡','大哭','尴尬','发怒','调皮','呲牙','惊讶','难过','酷','冷汗','抓狂','吐','偷笑','可爱','白眼','傲慢','饥饿','困','惊恐',
                    '流汗','憨笑','大兵','奋斗','咒骂','疑问','嘘','晕','折磨','衰','骷髅','敲打','再见','擦汗','抠鼻','鼓掌','糗大了','坏笑','左哼哼','右哼哼','哈欠','鄙视','委屈','快哭了','阴险','亲亲',
                    '吓','可怜','菜刀','西瓜','啤酒','篮球','乒乓','咖啡','饭','猪头','玫瑰','凋谢','示爱','爱心','心碎','蛋糕','闪电','炸弹','刀','足球','瓢虫','便便','月亮','太阳','礼物','拥抱','强','弱',
                    '握手','胜利','抱拳','勾引','拳头','差劲','爱你','NO','OK','爱情','飞吻','跳跳','发抖','怄火','转圈','磕头','回头','跳绳','挥手','激动','街舞','献吻','左太极','右太极'];
    
    function getTextShowContent(content){
        var showContent = content;
        for(var i=0;i<emotionArr.length;i++){
            var emotion = emotionArr[i];
            var regexp = new RegExp("\/"+emotion,"g");
            var imghtml = '<img alt="mo-'+emotion+'" src="https://res.wx.qq.com/mpres/htmledition/images/icon/emotion/'+i+'.gif">';
            showContent = showContent.replace(regexp,imghtml);
        }
        return showContent;
    }

    jQuery.fn.weixinEdition = function(options){
        var $this = $(this),
            maxTextLen = options.maxTextLen || 600,
            remainTextLen = maxTextLen,
            textHtmlCache = '',
            uploadUrl = options.uploadUrl || '',
            uploadVideoUrl = options.uploadVideoUrl || '',
            noborder = options.noborder || false,
            onlyAllowText = options.onlyAllowText || false,
            hideQQemot = options.hideQQemot || false,
            showAppmsg = options.showAppmsg || false,
            onlyShowAppmsg = options.onlyShowAppmsg || false,
            getAccountId = options.getAccountId || null,
            onEditorAreaChange = options.onEditorAreaChange || null;

        var replyContntType = 'text', // text-文本; image-图片; audio-语音; video-视频; appmsg-图文
            selectImageData = null,
            selectAudioData = null,
            selectAppmsgData = null;
        
        /*--------------------------------------------QQ表情--------------------------------------------------------------------------*/
        var emotionsTemplate='<% for (var i in emotions) { %>'
            +'<li class="emotions_item">'
            +'  <i class="js_emotion_i" data-gifurl="<%-emotions[i][\"gifurl\"] %>" data-title="<%-emotions[i][\"title\"] %>" style="<%-emotions[i][\"position\"] %>"></i>'
            +'</li>'
            +'<% } %>';
        
        var emotions = [];
        for(var i=0;i<emotionArr.length;i++){
            emotions.push({
                gifurl   : 'https://res.wx.qq.com/mpres/htmledition/images/icon/emotion/'+i+'.gif',
                title    : emotionArr[i],
                position : 'background-position:-'+(i*24)+'px 0;'
            })
        }
        
        var editionTemplate ='<div class="msg_sender">'
            +'  <div class="msg_tab">'
            +'    <ul class="tab_navs">'
            +'        <li class="tab_nav tab_text width4 selected" data-type="1" data-tab=".js_textArea" data-tooltip="文字"><a href="javascript:void(0);">&nbsp;<i class="icon_msg_sender"></i><span class="title">文字</span></a></li>'
            +'        <li class="tab_nav tab_img width4 js_msgSender_tabimg" data-type="2" data-tab=".js_imgArea" data-tooltip="图片"><a href="javascript:void(0);">&nbsp;<i class="icon_msg_sender"></i><span class="title">图片</span></a></li>'
            +'        <li class="tab_nav tab_audio width4 js_msgSender_tabaudio" data-type="3" data-tab=".js_audioArea" data-tooltip="语音"><a href="javascript:void(0);">&nbsp;<i class="icon_msg_sender"></i><span class="title">语音</span></a></li>'
            +'        <li class="tab_nav tab_video width4 js_msgSender_tabvideo hide" data-type="4" data-tab=".js_videoArea" data-tooltip="视频"><a href="javascript:void(0);">&nbsp;<i class="icon_msg_sender"></i><span class="title">视频</span></a></li>'
            +'        <li class="tab_nav tab_appmsg width4 js_msgSender_tabappmsg" data-type="5" data-tab=".js_appmsgArea" data-tooltip="图文" style="display:none;"><a href="javascript:void(0);">&nbsp;<i class="icon_msg_sender"></i><span class="title">图文消息</span></a></li>'
            +'    </ul>'
            +'    <div class="tab_cont_cover"><div class="tab_cont_inner clearfix">'
            +'        <div class="media_cover"><a href="javascript:;" class="add_wrap add-media"><span class="media_add"></span><p class="color-gray add-media-tips">上传图片</p></a><div class="msg_processbar"><span class="upload-processbar-width-wrap"><span class="upload-processbar-width"></span></span></div></div>'
            +'        <div class="media_cover"><a href="javascript:;" class="add_wrap select-media"><span class="media_add"></span><p class="color-gray">从素材库中选择</p></a></div>'
            +'    </div></div>'
            +'    <div class="tab_panel">'
            +'        <div class="tab_content tab_text_content">'
            +'            <div class="js_textArea inner no_extra">'
            +'                <div class="emotion_editor">'
            +'                    <div class="edit_area js_editorArea" contenteditable="true" style="display:none;"></div>'
            +'                    <div class="edit_area js_editorArea" contenteditable="true" style="overflow-y: auto; overflow-x: hidden;"></div>'
            +'                    <div class="editor_toolbar">'
            +'                        <a href="javascript:void(0);" class="icon_emotion emotion_switch js_switch">表情</a>'
            +'                        <p class="editor_tip js_editorTip">还可以输入<em></em>字</p>'
            +'                        <div class="emotion_wrp js_emotionArea" style="display: none;">'
            +'                            <span class="hook">'
            +'                                <span class="hook_dec hook_top"></span>'
            +'                                <span class="hook_dec hook_btm"></span>'
            +'                            </span>'
            +'                            <ul class="emotions" onselectstart="return false;">'
            +'                            </ul>'
            +'                            <span class="emotions_preview js_emotionPreviewArea"></span>'
            +'                        </div>'
            +'                    </div>'
            +'                </div>'
            +'            </div>'
            +'        </div>'
            +'        <div class="tab_content tab_image_content" style="display: none;">'
            +'            <div class="js_imgArea inner"></div>'
            +'        </div>'
            +'        <div class="tab_content tab_audio_content" style="display: none;">'
            +'            <div class="js_audioArea inner"></div>'
            +'        </div>'
            +'        <div class="tab_content tab_video_content" style="display: none;">'
            +'            <div class="js_videoArea inner"></div>'
            +'        </div>'
            +'        <div class="tab_content tab_appmsg_content" style="display: none;">'
            +'            <div class="js_appmsgArea inner"></div>'
            +'        </div>'
            +'    </div>'
            +'  </div> '
            +'</div>';
        $this.html(editionTemplate);
        if(noborder){
            $this.find('.msg_sender').css('border','none');
        }
        
        if(onlyAllowText){
            $this.find('.tab_navs').remove();
            $this.find('.tab_image_content,.tab_audio_content,.tab_video_content,.tab_appmsg_content').remove();
        }
        
        if(hideQQemot){
            $this.find('.editor_toolbar').remove();
        }
        
        if(onlyShowAppmsg){
            showAppmsg=true;
            $this.find('.tab_text,.tab_img,.tab_audio,.tab_video,.tab_image_content,.tab_audio_content,.tab_video_content,.tab_text_content').remove();
        }
        
        if(!showAppmsg){
            $this.find('.js_msgSender_tabappmsg').remove();
            $this.find('.tab_appmsg_content').remove();
        }else{
            $this.find('.js_msgSender_tabappmsg').show();
        }

        $this.find('.emotions').html(Template(emotionsTemplate,{emotions:emotions}));                   
        
        var tab_navs = $this.find('.tab_navs'),
            editorValArea = $this.find('.js_editorArea').eq(0),
            editorArea = $this.find('.js_editorArea').eq(1),
            emotion_wrp = $this.find('.emotion_wrp'),
            emotionPreviewArea = $this.find('.js_emotionPreviewArea'),
            editorTip = $(this).find('.js_editorTip em'),
            imgArea = $this.find('.js_imgArea'),
            audioArea = $this.find('.js_audioArea'),
            videoArea = $this.find('.js_videoArea'),
            appmsgArea = $this.find('.js_appmsgArea'),
            image_content = $this.find('.tab_image_content'),
            text_content = $this.find('.tab_text_content'),
            audio_content = $this.find('.tab_audio_content'),
            video_content = $this.find('.tab_video_content'),
            appmsg_content = $this.find('.tab_appmsg_content'),
            tab_cont_cover = $this.find('.tab_cont_cover');
    
        tab_navs.tab({
            tabIndex:1, 
            tabCon:[], 
            change:function(index){
                if(index==0){ 
                    if(onlyShowAppmsg){
                        showAppmsgArea();
                    }else{
                        showTextArea();
                    }
                }else if(index==1){
                    showImageArea();
                }else if(index==2){
                    showAudioArea();
                }else if(index==3){
                    showVideoArea();
                }else if(index==4){
                    showAppmsgArea();
                }
                
                initUploader(replyContntType);
            }
        })
        
        /*----------------------------------------文字回复----------------------------------------------------------------*/
        editorTip.html(maxTextLen);
        editorArea.on('input',function(){
            editorAreaChange();
        })

        //兼容IE浏览器
        if(!!window.ActiveXObject || "ActiveXObject" in window){
            var editorAreaOld = editorArea.html();
            var interval=null;
            editorArea.focus(function(){
                if(interval) return;
                interval = setInterval(function(){
                    var editorAreaNow = editorArea.html();
                    if(editorAreaNow!=editorAreaOld){
                        editorAreaOld = editorAreaNow;
                        editorAreaChange();
                    }
                },100);
            })
            
            editorArea.blur(function(){
                clearInterval(interval);
                interval=null;
            })
        }
        
        function editorAreaChange(){
            var imgReg = new RegExp("<img alt=\"mo-([^\"]+)\" src=\"https://res.wx.qq.com/mpres/htmledition/images/icon/emotion/\\d+.gif\">","g");
            var editorAreaHtml=editorArea.html();//.replace(/<div><br><\/div>/g,'<br><br>').replace(/<p><br><\/br><\/p>/g,'').replace(/<p><br><\/p>/g,'').replace(/<p>&nbsp;<\/p>/g,'<p><\/p>').replace(/<p><\/p>/g,'');
            editorValArea.html(editorAreaHtml.replace(imgReg,function($0,$1){
                return '/'+$1;
            }));
            
            //过滤粘贴html标签
            if(navigator.userAgent.indexOf("MSIE 8.0")<0){
                var editorValAreaHtml = editorValArea.html();
                editorValAreaHtml=editorValAreaHtml.replace(/<(\/)?span[^>]*>/g,''); //chrome浏览器复制会带上span标签
                editorValArea.html(editorValAreaHtml);
            }
            
            editorValArea.html(htmlFilter(editorValArea.html()));
            computeTextLen();
        }
        
        function computeTextLen(){
            var textLen = editorValArea.html().length;
            remainTextLen = maxTextLen-textLen;
            if(remainTextLen<0){
                remainTextLen = 0;
                editorValArea.html(textHtmlCache);           
                editorArea.html(getTextShowContent(textHtmlCache));
                editorArea.blur();
            }else{
                textHtmlCache = editorValArea.html();
            }
            editorTip.html(remainTextLen);
            onEditorAreaChange&&onEditorAreaChange();
        }
        
        //输入QQ表情
        $this.find('.emotion_switch').on('click',function(e){
            emotion_wrp.fadeIn();  
            e.stopPropagation();
        })
        
        $this.find('.emotions .emotions_item').on('click',function(e){
            var js_emotion_i = $(this).find('.js_emotion_i');
            var title = js_emotion_i.attr('data-title');
            var $imgHtml = $('<img alt="mo-'+title+'" src="'+js_emotion_i.attr('data-gifurl')+'">');
            editorValArea.html(editorValArea.html()+'/'+title);
            editorArea.append($imgHtml);
            computeTextLen();
            e.stopPropagation();
        })
        
        $this.find('.emotions .emotions_item').hover(function(){
            var js_emotion_i = $(this).find('.js_emotion_i');
            emotionPreviewArea.html('<img alt="mo-'+js_emotion_i.attr('data-title')+'" src="'+js_emotion_i.attr('data-gifurl')+'">');
        },function(){emotionPreviewArea.html('')})
        
        $('body').on('click',function(){
            emotion_wrp.fadeOut();
        })
        
        /*----------------------------------------图片/语音/视频 回复----------------------------------------------------------------*/
        var imgTemplate = '<div>'
                         +'  <div class="appmsgSendedItem simple_img">'
                         +'    <a class="title_wrp" href="<%-img_url %>" target="_blank">'
                         +'      <img class="icon" src="<%-img_url %>">'
                         +'      <span class="title">[图片]</span>'
                         +'    </a>'
                         +'  </div>'
                         +'  <a href="javascript:;" class="link_dele" data-type="image">删除</a>'
                         +'</div>';
                 
        var audioTemplate='<div>'
                         +'  <div class="appmsgSendedItem simple_audiomsg">'
                         +'    <a class="title_wrp" href="javascript:;">'
                         +'       <span class="icon icon_lh" src="<%-voice_url %>"></span>'
                         +'    </a>'
                         +'  </div>'
                         +'  <a href="javascript:;" class="jsmsgSenderDelBt link_dele" data-type="audio">删除</a>'
                         +'</div>';
        
        var appmsgTemplate ='<div class="clearfix" style="position:relative;"><div class="appmsg_wrap pull-left"><div class="m-item<%- (articles=articles||[]).length>1?\' multi\':\'\' %>"  data-id="<%- id %>" data-mediaid="<%- media_id %>">'
                           +'  <div class="m-content-wrap">'
                           +'    <% for(var idx=0;idx < articles.length;idx++){ %>'
                           +'        <div class="m-content">'
                           +'          <h4><a href="javascript:;"><%- articles[idx]["title"] %></a></h4>'
                           +'          <% if(!idx){ %><div class="m-date"><%- modified_on||\'\' %></div><% } %>'
                           +'          <div class="m-cover">'
                           +'            <img src="<%- articles[idx]["cover_url"] %>@160h.png" />'
                           +'          </div>'
                           +'          <% if(!idx){ %><p class="m-desc"><%- articles[idx]["summary"]||\'\' %></p><% } %>'
                           +'        </div>'
                           +'    <% }%>'
                           +'  </div>'
                           +'</div></div><a href="javascript:;" class="link_dele pull-left" style="position:absolute;bottom:0;left:300px;" data-type="appmsg">删除</a></div>';
        
        var _uploader;
        
        function initUploader(type){
            if(_uploader) _uploader.destroy();
            
            if(type=='image'){
                createUploader('image',{
                    filters:{
                        mime_types:[{title: '图片文件', extensions: 'jpg,jpeg,png,gif,bmp'}],
                        max_file_size : '2mb'
                    },
                    uploaded : function(data){
                        var imgname = encodeURIComponent(data.imgname);
                        var imgdata ={"name":imgname,"img_url":data.original,"img_name":imgname,"group_id":""};
                        var postdata = 'type=picture&account_id='+ (getAccountId ? getAccountId() : '') +'&data='+JSON.stringify(imgdata);
                        saveMaterial('image',postdata);
                    }
                })
            }else if(type=='audio'){
                createUploader('audio',{
                    filters:{
                        mime_types:[{title: '音频文件', extensions: 'mp3,wma,wav,amr'}],
                        max_file_size : '5m'
                    },
                    uploaded : function(data){
                        var audioname = encodeURIComponent(data.filename);
                        var audiodata = {'name':audioname,'voice_url':data.file,'voice_name':audioname};
                        var postdata = 'type=voice&account_id='+ (getAccountId ? getAccountId() : '') +'&data='+JSON.stringify(audiodata);
                        saveMaterial('audio',postdata);
                    }
                })
            }
        }
        
        function createUploader(type,options){
            _uploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : $this.find('.add-media')[0], 
                container : $this[0],
                url : (type=='image') ? uploadUrl : uploadVideoUrl,
                multi_selection : false,
                flash_swf_url : '/frontend/3rd/plupload/Moxie.swf',
                silverlight_xap_url : '/frontend/3rd/plupload/Moxie.xap',
                filters: options.filters,
                init: {
                    FilesAdded: function(up, files) {
                        _uploader.start();
                    },
                    FileUploaded: function(up, file, info) {
                        var data = JSON.parse(info.response) || {};
                        if(data){
                            options.uploaded(data);
                            _uploader.destroy();
                        }
                    },
                    UploadProgress: function(up, file) {
                        $('.msg_processbar').show().find('.upload-processbar-width').css('width', file.percent + '%');
                    },
                    Error: function(up, err) {
                        if(err.code == -601){
                            $.topTips({mode:'warning',tip_text:'亲，只支持'+options.filters.mime_types[0].extensions});
                        }else if(err.code == -600){
                            $.topTips({mode:'warning',tip_text:'亲，文件大小不能超过'+options.filters.max_file_size});
                        }else{
                            $.topTips({mode:'warning',tip_text:err.code+':'+err.message});
                        }
                    }
                }
            });
            _uploader.init();
        }
        
        function saveMaterial(type,data){
            O.ajaxEx({
                url: O.path('/wechat/material/save'),
                type: 'post',
                data : data,
                success: function(json) {
                    $('.msg_processbar').hide();
                    if(!json.result){
                        $.topTips({mode:'tips',tip_text:json.msg});
                        initUploader(replyContntType);
                    }else{
                        tab_cont_cover.hide();
                        if(type=='image'){
                            var data ={
                                id: json.data.id,
                                img_url: json.data.img_url,
                                media_id: json.data.media_id,
                                modified_on: json.data.modified_on,
                                name: json.data.name,
                                selected: 0,
                                wechat_url:json.data.wechat_url
                            }
                            selectImageData = data;
                            imgArea.html(Template(imgTemplate,data));
                        }else if(type=='audio'){
                            var data={
                                id:json.data.id,
                                name:json.data.name,
                                voice_url:json.data.voice_url,
                                modified_on:json.data.modified_on,
                                media_id:json.data.media_id
                            }
                            selectAudioData = data;
                            audioArea.html(Template(audioTemplate,data));
                        }
                    }
                },
                error: function(){
                    $.topTips({mode:'tips',tip_text:'出现异常'});
                }
            })
        }
        
        //新增素材
        $this.on('click','.add-media',function(){
            if(replyContntType=='video'){
                window.open(O.path('/wechat/material/add-video?account='+ (getAccountId ? getAccountId() : '')));
            }else if(replyContntType=='appmsg'){
                window.open(O.path('/wechat/material/mpnews?view=card&account='+ (getAccountId ? getAccountId() : '')));
            }
        })
        
        //选择素材
        $this.on('click','.select-media',function(){
            switch(replyContntType){
                case 'image':
                    selectImage();
                    break;
                case 'audio':
                    selectAudio();
                    break;
                case 'appmsg':
                    selectAppmsg();
                    break;
            }
        })
        
        function selectImage(){
            window.selectImageBox = $.dialog({
                url: O.path('/wechat/material/picture-select?var=selectImageBox&account='+ (getAccountId ? getAccountId() : '')),
                title: '选择图片',
                id: 'js_selectimg',
                width: 850,
                height: 516,
                skin:'art-box',
                onshow: function () {},
                onclose: function () {}
            }).showModal();
            
            window.selectImageBox.onSelect = function(imageData){
                tab_cont_cover.hide();
                if(_uploader) _uploader.destroy();
                selectImageData = imageData[0];
                imgArea.html(Template(imgTemplate,imageData[0]));
            }
        }
        
        function selectAudio(){
            window.selectAudioBox = $.dialog({
                url: O.path('/wechat/material/voice-select?var=selectAudioBox&account='+ (getAccountId ? getAccountId() : '')),
                title: '选择语音',
                id: 'js_selectaudio',
                width: 840,
                height: 550,
                skin:'art-box',
                onshow: function () {},
                onclose: function () {}
            }).showModal();
            
            window.selectAudioBox.onSelect = function(audioData){
                tab_cont_cover.hide();
                if(_uploader) _uploader.destroy();
                selectAudioData = audioData;
                audioArea.html(Template(audioTemplate,audioData));
            }
        }
        
        function selectAppmsg(){
            window.selectAppmsgBox = $.dialog({
                url: O.path('/wechat/material/appmsg-select?account='+ (getAccountId ? getAccountId() : '')),
                title: '选择素材',
                id: 'js_selectappmsg',
                width: 992,
                height: 591,
                skin:'art-box',
                onshow: function () {},
                onclose: function () {}
            }).showModal();
            
            window.selectAppmsgBox.onSelect = function(appmsgData){
                tab_cont_cover.hide();
                if(_uploader) _uploader.destroy();
                selectAppmsgData = appmsgData;
                appmsgArea.html(Template(appmsgTemplate,appmsgData));
            }
        }
        
        $this.on('click','.link_dele',function(){          
            $(this).parent().remove();
            tab_cont_cover.show();
            initUploader(replyContntType);
            
            var type= $(this).attr('data-type');
            if(type=='image'){
                selectImageData=null;  
            }else if(type=='audio'){
                selectAudioData = null;
            }else if(type=='appmsg'){
                selectAppmsgData=null;
            }
        })
        
        $this.find('.cancel_upload').on('click',function(){
            if(_uploader){
                _uploader.stop();
            }
        })
        
        /*----------------------------------------public API--------------------------------------------------------------*/
        function getContent(){
            switch(replyContntType){
                case 'text':
                    return editorValArea.html().replace(/&gt;/g,'>')
                        .replace(/&lt;/g,'<')
                        .replace(/&amp;/g,'&')
                        .replace(/<BR(\/)?>/g,'<br>')
                        .replace(/^<br>/,'')
                        .replace(/<[^>]+>/g,function(a){
                            var match = a.match(/<a( (.*?)>|>)|<\/a>/g);
                            if(match){
                                return match[0].replace(/</g,'&lt;').replace(/>/g,'&gt;')
                            }
                            return '';
                        })
                        .replace(/&lt;/g,'<').replace(/&gt;/g,'>');
                    break;
                case 'image':
                    return selectImageData;
                    break;
                case 'audio':
                    return selectAudioData;
                    break;
                case 'appmsg':
                    return selectAppmsgData;
                    break;
                default:
                    return '';
                    break;
            }
        }
        
        function htmlFilter(content){
            var content = content.replace(/&nbsp;/g,'').replace(/<div>([^<]+)(?:<br(?:\/)?>)?<\/div>/g,function($0,$1){
                return '<br>'+$1;
            }).replace(/<DIV>([^<]+)(?:<BR(?:\/)?>)?<\/DIV>/g,function($0,$1){
                return '<br>'+$1;
            }).replace(/<p>([^<]+)(?:<br(?:\/)?>)?<\/p>/g,function($0,$1){
                return '<br>'+$1;
            }).replace(/<P>([^<]+)(?:<BR(?:\/)?>)?<\/P>/g,function($0,$1){
                return '<br>'+$1;
            });
            content = content.replace(/<div><br><\/div>/g,'<br>');
            content = content.replace(/<(\/)?p>/g,'');
            content = content.replace(/<(\/)?P>/g,'');
            return content;
        }
        
        function clearContent(isAll){
            if(!isAll){
                switch(replyContntType){
                    case 'text':
                        editorArea.html('');
                        editorValArea.html('');
                        break;
                    case 'image':
                        imgArea.html('');
                        selectImageData=null;
                        break;
                    case 'audio':
                        audioArea.html('');
                        selectAudioData=null;
                        break;
                    case 'appmsg':
                        appmsgArea.html('');
                        selectAppmsgData=null;
                        break;
                }
            }else{
                editorArea.html('');
                editorValArea.html('');
                imgArea.html('');
                audioArea.html('');
                appmsgArea.html('');
                selectImageData=null;
                selectAudioData=null;
                selectAppmsgData=null;
            }
        }
        
        function setContent(type,content){
            switch(type){
                case '文字':
                    editorValArea.html(content);
                    editorArea.html(getTextShowContent(content));
                    computeTextLen();
                    break;
                case '图片':
                    imgArea.html(Template(imgTemplate,JSON.parse(content)));
                    selectImageData = JSON.parse(content);
                    tab_cont_cover.hide();
                    break;
                case '语音':
                    audioArea.html(Template(audioTemplate,JSON.parse(content)));
                    selectAudioData = JSON.parse(content);
                    tab_cont_cover.hide();
                    break;
                case '图文':
                    appmsgArea.html(Template(appmsgTemplate,JSON.parse(content))); //content为图文对象数据appmsgData
                    selectAppmsgData = JSON.parse(content);
                    tab_cont_cover.hide();
                    break;
            }
            
        }
        
        function getType(){
            var type='';
            switch(replyContntType){
                case 'text':
                    type='文字';
                    break;
                case 'image':
                    type='图片';
                    break;
                case 'audio':
                    type='语音';
                    break;
                case 'video':
                    type='视频';
                    break;
                case 'appmsg':
                    type='图文';
                    break;
            }

            return type;
        }
        
        function showTextArea(){
            text_content.show();
            tab_cont_cover.hide();
            image_content.hide();
            audio_content.hide();
            video_content.hide();
            appmsg_content.hide();
            tab_navs.find('li').removeClass('selected on');
            $this.find('.tab_text').addClass('selected on');
            replyContntType='text';
        }
        
        function showImageArea(){
            image_content.show();
            tab_cont_cover.hide().find('.add-media .add-media-tips').html('上传图片');
            if(imgArea.html()==''){
                tab_cont_cover.show();
            }
            text_content.hide();
            audio_content.hide();
            video_content.hide();
            appmsg_content.hide();
            tab_navs.find('li').removeClass('selected on');
            $this.find('.tab_img').addClass('selected on');
            replyContntType='image';
        }
        
        function showAudioArea(){
            audio_content.show();
            tab_cont_cover.hide().find('.add-media .add-media-tips').html('上传语音');
            if(audioArea.html()==''){
                tab_cont_cover.show();
            }
            text_content.hide();
            image_content.hide();
            video_content.hide();
            appmsg_content.hide();
            tab_navs.find('li').removeClass('selected on');
            $this.find('.tab_audio').addClass('selected on');
            replyContntType='audio';
        }
        
        function showVideoArea(){
            video_content.show();
            tab_cont_cover.hide().find('.add-media .add-media-tips').html('新建视频');
            if(videoArea.html()==''){
                tab_cont_cover.show();
            }
            text_content.hide();
            image_content.hide();
            audio_content.hide();
            appmsg_content.hide();
            tab_navs.find('li').removeClass('selected on');
            $this.find('.tab_video').addClass('selected on');
            replyContntType='video';
        }
        
        function showAppmsgArea(){
            appmsg_content.show();
            tab_cont_cover.hide().find('.add-media .add-media-tips').html('新建图文消息');
            if(appmsgArea.html()==''){
                tab_cont_cover.show();
            }
            text_content.hide();
            image_content.hide();
            audio_content.hide();
            video_content.hide();
            tab_navs.find('li').removeClass('selected on');
            $this.find('.tab_appmsg').addClass('selected on');
            replyContntType='appmsg';
        }
        
        function showArea(type){
            switch(type){
                case '文字':
                    showTextArea();
                    break;
                case '图片':
                    showImageArea();
                    break;
                case '语音':
                    showAudioArea();
                    break;
                case '视频':
                    showVideoArea();
                    break;
                case '图文':
                    showAppmsgArea();
                    break;
            }
        }
        
        function refresh(){
            clearContent(true);
            if(onlyShowAppmsg){
               showArea('图文');
            }else{
               showArea('文字');
               editorTip.html(maxTextLen); 
            }
            if(_uploader) _uploader.destroy();
        }
        
        function focus($obj){
            if(window.getSelection){
                var range = document.createRange(); 
                var len = $obj[0].childNodes.length;
                if(len){
                    range.setStart($obj[0], len);  
                    range.setEnd($obj[0], len);  
                    var selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);  
                }
            }else{
                /*var textRange = document.selection.createRange();
                textRange.moveToElementText($obj[0]);
                var textLen = $obj.text().length;
                if(textLen){
                    textRange.moveStart("character",textLen);
                    textRange.collapse(true);   
                    textRange.select();
                }*/
            }
        }
        
        return {
            getContent : getContent,
            setContent : setContent,
            clearContent : clearContent,
            showArea : showArea,
            getType : getType,
            refresh : refresh,
            uploader :_uploader
        }
    }
    
    return {
        getTextShowContent:getTextShowContent
    }
})