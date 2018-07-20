/**
 * Created by weizs on 2015/7/2.
 */
define(function (require, exports, module) {
    // tips : 必须是2.0.2或以下，以上版本不支持ie8，过低版本不支持ie11
    require('../../3rd/zeroclipboard/2/ZeroClipboard.js');
    ZeroClipboard.config({ swfPath: "/frontend/3rd/zeroclipboard/2/ZeroClipboard.swf" });

    var CopyUtil={
        init:function(nodes,options){
            ZeroClipboard.destroy();
            var client = new ZeroClipboard(nodes),
                type = options.type||'text/plain',
                beforeCopy = options.beforeCopy||function(){return '';},
                onCopy = options.onCopy,
                onError = options.onError;
            client.on( 'ready', function() {
                client.on( 'copy', function(event) {
                    event.clipboardData.setData(type, beforeCopy.call(client,event.target));
                } );
                client.on( 'aftercopy', function() {
                    onCopy&&onCopy.call(client);
                } );
            } );

            client.on( 'error', function() {
                ZeroClipboard.destroy();
                onError&&onError.call(client);
            } );
        }
    };

    //存在jQuery则绑定同时绑定到jQuery上
    jQuery&& jQuery.fn.extend({
        copy:function(options){
            CopyUtil.init($(this),options);
        }
    });

    return {
        init:function(nodes,options){
            CopyUtil.init(nodes||undefined,options);
        }
    }
});