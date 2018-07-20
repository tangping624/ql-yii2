seajs.use(['utils','/frontend/js/widgets/weixinEdition/weixinEdition.js','/frontend/js/lib/dialog','/frontend/js/plugin/tab'],function(utils){
    var account=O.getQueryStr('id');

    var _uploader = utils.upload({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : 'import_cert',
        container : $('.grid-toolbar').get(0),
        url : O.path('/system/account/import?type=mch_ssl_cert&accountId='+account),
        multi_selection : false,
        flash_swf_url : '/frontend/3rd/plupload/Moxie.swf',
        silverlight_xap_url : '/frontend/3rd/plupload/Moxie.xap',
        filters: {
            mime_types:[{title: 'Pem文件', extensions: 'pem'}],
            max_file_size : '10m'
        },
        init: {
            FilesAdded: function(up, files) {
                var file = files[0];
                //msg_processbar.show();
                _uploader.start();
            },
            FileUploaded: function(up, file, info) {
                //msg_processbar.hide();
                var data = JSON.parse(info.response) || {};
                if(data){
                    if (data.result) {
                        $.topTips({tip_text:data.msg});
                        location.reload();
                    }
                    if( !data.result ){
                        $.topTips({mode:'warning',tip_text:data.msg});
                    }
                    _uploader.refresh();
                }
            },
            UploadProgress: function(up, file) {
                //msg_processbar.find('.upload-processbar-width').css('width', file.percent + '%');
            },
            Error: function(up, err) {
                //showTextArea();
                if(err.code == -601){
                    $.topTips({mode:'warning',tip_text:'亲，只支持pem格式'});
                }else if(err.code == -600){
                    $.topTips({mode:'warning',tip_text:'亲，文件大小不能超过10m'});
                }else{
                    $.topTips({mode:'warning',tip_text:err.code+':'+err.message});
                }
            }
        }
    });
    _uploader.init();


    var errorCallback = function() {
        $.topTips({mode:'tips',tip_text:'出现异常'});
    }
})
