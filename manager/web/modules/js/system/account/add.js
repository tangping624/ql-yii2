/**
 * Created by weizs on 2015/5/6.
 */
//'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    require('../../../../frontend/js/plugin/form');
    require('../../../../frontend/js/lib/dialog');


    var organization_form = $('#organization_form');

    organization_form.form({
        submitbtn : 'save_btn',
        formName : 'organization_form',
        rules :[ 
            {
                id: 'name',
                required : true,
                msg : {'required': '请输入公众号名称'}
            },
            {
                id:'original_id',
                required : true,
                msg : {'required': '请输入原始ID'}
            },
            {
                id:'wechat_number',
                required : true,
                msg : {'required': '请输入微信号'}
            },
            {
                id:'app_id',
                required : true,
                msg : {'required': '请输入AppID'}
            },
            {
                id:'app_secret',
                required : true,
                msg : {'required': '请输入AppSecret'}
            }/*,
            {
                id:'mch_id',
                required : true,
                msg : {'required': '请输入商户号'}
            },
            {
                id:'mch_key',
                required : true,
                msg : {'required': '请输入商户密钥'}
            }*/ 
        ],
        validate : function(){
            return true;
        },
        submit : function(dataStr,data){ 
            data['headimg_url'] ="";
            if($('#headimg_urlconver').find('.js-img')){
            data['headimg_url'] =$('#headimg_urlconver').find('.js-img').attr('_src') ;
            }
             data['qrcode_url'] ="";
            if($('#qrcode_urlconver').find('.js-img')){
            data['qrcode_url'] =$('#qrcode_urlconver').find('.js-img').attr('_src') ;
            }
            O.ajaxEx({
                url: O.path('system/account/save?id='+ $("#id").val()),
                data: data,
                type: 'post',
                success: function(json) {
                    if(!json.result) {
                        $.topTips({tip_text:json.msg});
                    } else {
                        $.topTips({tip_text:'添加公众号成功！'});
                        window.location.href= O.path('system/account/guide?id='+json.id);
                    }
                },
                error: errorCallback
            })
        }
    }); 
    var errorCallback = function() {
        $.topTips({mode:'warning',tip_text:'出现异常'});
    }

    //取消
    $('#cancel_btn').on('click',function(){
        window.location.href = O.path('/wechat/account/index');
    })
})