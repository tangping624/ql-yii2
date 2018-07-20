//'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var copy = require('../../../../frontend/js/lib/copy');
    require('../../../../frontend/js/plugin/form');
    require('../../../../frontend/js/lib/dialog.js');


    var config_form = $('#config_form'),
        aid = O.getQueryStr('id');
    
    config_form.form({
        submitbtn : '',
        formName : 'config_form',
        rules :[],
        validate : function(){ 
            return true;
        },
        submit : function(data){ }
    });
    
    
    
    //点击修改
    config_form.on('click','.edit-btn',function(e){
        var _this = $(this);
        var tr = _this.closest('tr');
        tr.find('.text').hide();
        tr.find('.edit').removeClass('hide').find('.form-control').focus(); 
        e.stopPropagation(); 
    })
   
    config_form.on('blur','.edit .form-text-control',function(){
        var control = $(this);
        if(control.attr('type')!='text') return;
        
        var oldval = control.attr('data-value');
        var currval = $.trim(control.val());
        if(currval!=oldval){
            updateAccount(aid,control.attr('data-column'),currval,function(){
                cancelEdit(control);
                control.val(currval);
                control.attr('data-value',currval);
                control.closest('tr').find('.text').html(currval);
                $.topTips({tip_text:'修改成功！'});
            })
        }else{
            cancelEdit(control);
        }
    })

    config_form.on('click','.edit .form-select-control',function(e){
        e.stopPropagation();
    })
    config_form.on('change','.edit .form-select-control',function(){
        var control = $(this);
        var currval = $.trim(control.val());
        var currtext =control.find("option:selected").text();
        updateAccount(aid,control.attr('data-column'),currval,function(){
            cancelEdit(control);
            control.closest('tr').find('.text').html(currtext);
            $.topTips({tip_text:'修改成功！'});
        })
    })
    
    $('body').on('click',function(){
        var editcontrol = config_form.find('.edit');
        editcontrol.each(function(){
            if(!$(this).hasClass('hide')){
                cancelEdit($(this).find('.form-select-control'));
            }
        })
    })
    
    var updateAccount = function(id,column,value,callback){
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
                } else {
                    callback&&callback();
                }
            },
            error: errorCallback
        })
    } 
    var cancelEdit = function(control){
        var tr = control.closest('tr');
        tr.find('.text').show();
        tr.find('.edit').addClass('hide');
    }
    
    var errorCallback = function() {
        $.topTips({mode:'warning',tip_text:'出现异常'});
    }

    $('.copy-button').copy({
        beforeCopy:function(dom){
            return $(dom).closest('tr').find('.copytext').html();
        },
        onCopy:function(){
            $.topTips({tip_text:'复制成功'});
        }
    });

})
