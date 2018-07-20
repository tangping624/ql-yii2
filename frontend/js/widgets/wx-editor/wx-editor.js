/**
 * Created by weizs on 2015/6/17.
 */
'use strict';
/*global define,UE*/
define(function (require, exports, module) {
    //需要在页面引用
    //<script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.config.js"></script>
    //<script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.all.min.js"></script>
    //<script type="text/javascript" src="/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js"></script>
    require('./css/wx-editor.css');
    
    'more,wx-pic,wx-sound,wx-video,wx-link'.replace(/[^, ]+/g,function(cmd){
        var lang={
            'more':'更多',
            'wx-pic':'图片'
        };

        UE.ui[cmd]=function (editor) {
            var ui = new UE.ui.Button({
                className:'edui-for-' + cmd,
                title:lang[cmd],
                name:lang[cmd],
                onclick:function () {
                    editor.execCommand(cmd);
                },
                theme:editor.options.theme,
                showText:false
            });
            UE.ui.buttons[cmd] = ui;
            return ui;
        };
    });

    return {
        getEditor:function(domId,options){
            var setting={
                initialFrameHeight:440,
                maximumWords:20000,
                autoFloatEnabled:true,
                toolbarTopOffset:100,
                serverUrl: '/file/upfile?'+$.param(options&&options.params||{}),
                toolbars:[
                    ['more','|','fontsize','|','blockquote','|','horizontal','|','removeformat','|','link','unlink','|','wx-pic','fullscreen'],
                    ['bold','italic','underline','forecolor','backcolor','|','justifyleft','justifycenter','justifyright','|','rowspacingtop','rowspacingbottom','lineheight','|','insertorderedlist','insertunorderedlist','|','imagenone','imageleft','imageright','imagecenter']
                ],
                insertunorderedlist:{
                    'circle' : '○ 大圆圈',
                    'disc' : '● 小黑点',
                    'square' : '■ 小方块'
                },
                insertorderedlist:{
                    'decimal' : '1,2,3...',
                    'lower-alpha' : 'a,b,c...',
                    'lower-roman' : 'i,ii,iii...',
                    'upper-alpha' : 'A,B,C...',
                    'upper-roman' : 'I,II,III...'
                }
            };
            var _editor=UE.getEditor(domId,$.extend(true,{},setting,options));
            _editor.registerCommand('more', {
                execCommand: function() {
                    $(this.ui.getDom('toolbarbox')).toggleClass('not-show-more');
                }
            });

            return _editor;
        }
    };
});