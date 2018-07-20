define(function(require, exports, module) {
    var F = window.Form = require('../lib/form');
    window.DataValid = require('../lib/validate');
   
    jQuery.fn.form=function(options){
        var $form = $(this),
            $radios = $form.find('.form-radio'),
            $checkboxs = $form.find('.form-checkbox'),
            submitbtn = options.submitbtn || '',
	        submitbtns = options.submitbtns  || null,
            formName = options.formName /*|| $form.attr('id')*/,
            submit = options.submit || null,
            validate = options.validate || null,
            is_scrollIntoView = options.is_scrollIntoView || false,     //是否定位到第一个报错的表单项
            rules = options.rules || [], _data = null,
            process = options.process, // 当一个页面根据不同的条件有不同的验证规则时
            realtime = options.realtime == undefined ? true : options.realtime,
            errorHtml = options.errorHtml || '<p class="form-error">{errorHtml}</p>';
            
        var _validate = new DataValid(errorHtml);
        
        var _initFormElement = function(){
            $form
                .off('click', '.form-radio')
                .off('click', '.form-checkbox');
            
            $form.on('click', '.form-radio', function(e){
                e.preventDefault();// 防止事件冒泡，触发2次

                var $this = $(this);
                if($this.hasClass('readonly')
                    || $this.hasClass('disabled')) return;

                var $radio = $this.find('input[type="radio"]'); 
                var name = $radio.attr('name');

                if(!$this.hasClass('selected')) {
                    $('input[name="'+name+'"]')
                        .prop('checked', false)
                        .attr('checked', false)
                        .closest('.form-radio')
                        .removeClass('selected');

                    $this.addClass('selected');
                }

                $radio.prop('checked', true).attr('checked', true)

                O.emit('radio', $radio, $this)
            });

            $form.on('click','.form-checkbox',function(e){
                e.preventDefault();// 防止事件冒泡，触发2次

                var $this = $(this);
                if($this.hasClass('readonly')
                    || $this.hasClass('disabled')) return;

                var $checkbox = $this.find('input[type="checkbox"]'); 
                var flag = false;

                if(!$this.hasClass('selected')) {
                    flag = true;
                    $this.addClass('selected');
                    $checkbox.prop('checked', flag).attr('checked', flag)
                } else {
                    flag = false;
                    $this.removeClass('selected');
                    $checkbox.prop('checked', flag).attr('checked', flag)
                }

                O.emit('checkbox', $checkbox, $this, flag)
            }) 
        };
        
        var _initForm = function(){
            if(submitbtn){
                $('#'+submitbtn).off('click').on('click',function(){
                    _formSubmit($(this));
                })
            }
            //多个按钮
            if(submitbtns){
                $(submitbtns).each(function(i,v){
                    v.off('click').on('click',function(){
                        _formSubmit.call(this,$(this));
                    });
                });
            }
        };
        
        var eleCheck = function(field){
            var ele = $form[0][field.id];
            if(!ele)return;

            var type = ele.type;
            switch(type){
                case 'text':
                case 'password':
                case 'textarea':
                case 'hidden':
                    $(ele).off(field.event||'keyup').on(field.event||'keyup',function(){
                        if(!field.notAutoCheck){
                            _validate.processEle(field);
                        }
                    });
                    break;
                case 'select-one':
                case 'select-multiple':
                    $(ele).off('change').on('change',function(){
                        _validate.processEle(field);
                    });
                    break;
            }
        };
        
        var _doCheck = function(){
            _data = F.serialize($form, 2);

            if(validate){
                var result = validate(_data);
                if(result===false){
                    return false;
                }
            }
            
            /*if (_validate.fieldList.length === 0) {
                if(rules&&rules.length>0){
                    _validate.addFields(formName, rules);
                }
            }*/

            var processResult = process ? process(_data) : 
                _validate.process(false);

            if (!processResult) {
                var id = _validate.errorField.split(',')[1];
                if(is_scrollIntoView){
                    var temp;
                    // radio或checkbox时比较特殊
                    id && (_validate.form ? 
                        ((temp = _validate.form[id]).length > 0 ? 
                            temp[0] : temp)
                        : $('#' + id)[0]).scrollIntoView();
                }
                return false;
            }
            
            return true;
        };
        
        var _formSubmit = function (button){
            if(_doCheck()){
                if(submit){
                    var data = F.serialize($form);
                    submit(data, _data, button);
                }
            }
        };
        
        var init = function(){ 
            //添加验证规则
            if (_validate.fieldList.length === 0) {
                if(rules&&rules.length>0){
                    _validate.addFields(formName ? formName: rules, rules);
                    
                    //输入校验
                    var fieldList = _validate.fieldList;
                    for(var i=0; i<fieldList.length; i++){
                        var field = fieldList[i];
                        var ele = $form[0][field.id];
                        if(field.desc!=''){
                            _validate._showError(ele, field.desc, field.errorHtml,null,true);
                        }
                        realtime && eleCheck(field);
                    }
                }
            }
            
            _initFormElement();
            _initForm();
        };
        
        init();

        return _validate;
    };
    module.exports = new DataValid();
});