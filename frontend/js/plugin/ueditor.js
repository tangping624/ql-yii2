define(function(require, exports, module) {
    require('../lib/tooltips/tooltips');
    window.Template = require('../lib/template');
    
    jQuery.fn.setEditor = function(options){
        var $this = $(this),
            ueditors = options.ueditors || [],
            dynamic = options.dynamic || false,
            _ueNum = 0, _left = 0,
            _ueArr =[];
        
        var ueditorTempl ='<div class="form-group">'
            +'  <div class="clearfix">'
            +'    <label for=""><span class="name"><%- name %></span> <span class="icon-merge icon-edit"  style="display:none;"></span> <span class="label-desc">( <%- desc %> )</span></label>'
            +'    <span class="pull-right icon-merge icon-delete" style="display:none;"></span>'
            +'  </div>'
            +'  <div style="width:100%;height:200px;" id="uedetail_<%- i %>" class="ueedit-box"></div>'
            +'</div>';
    
        var addTempl ='<div class="ps-add-wrap">'
            +'  <div class="ps-add">'
            +'    <span class="line-bg"><span class="line h-line"></span></span>'
            +'    <span class="line-bg"><span class="line v-line"></span></span>'
            +'  </div>'
            +'  <div class="tips">添加服务</div>'
            +'</div>';
    
        var _deleteUeTempl = '<div class="tips-wrap delete-tips">'
            + '<div class="content"><div class="delete-info">确定删除？</div></div>'
            +'<button type="button" class="btn-pr ok-btn js-delete-ue">确定</button>'
            +'<button type="button" class="btn-pr cancel-btn">取消</button>'
            +'</div>';
    
        var _editTempl = '<div class="form tips-wrap">'
            + ' <div class="content">编辑名称</div>'
            + ' <input type="text" class="form-control edit-inp" value="{name}">'
            + ' <p class="color-red edit-error hide">不能为空</p>'
            + ' <div style="margin-top:10px;">'
            + '    <button class="btn-pr ok-btn edit-btn">确定</button>'
            + '    <button class="btn-pr cancel-btn">取消</button>'
            + ' </div>'
            + '</div>';
        
        if(ueditors && ueditors.length>0){
            _left = ueditors.length;
            $this.html('');
            $.each(ueditors,function(i,ueditor){
                _ueNum++;
                var html = Template(ueditorTempl, {name: ueditor.name, desc:ueditor.desc, i:_ueNum });
                $this.append(html);
                var ue = UE.getEditor('uedetail_'+_ueNum);
                _ueArr.push({
                    id : 'uedetail_'+_ueNum,
                    name : ueditor.name,
                    ue : ue
                });
                ue.ready(function() {
                    _left--;
                    ueditor.detail && 
                        ue.setContent(ueditor.detail);
                });
            })
        }
        
        if(dynamic){
            $this.append(addTempl);
            $('.icon-delete,.icon-edit').show();
        }
        
        var _bindEvent = function(){
            $this.on('click','.ps-add-wrap',function() {
                _ueNum++;
                var html = Template(ueditorTempl,{name: '其他服务', desc:'选填', i:_ueNum });
                $(html).insertBefore($(this));
                
                if(dynamic){
                    $('.icon-delete,.icon-edit').show();
                }
                var ue = UE.getEditor('uedetail_'+_ueNum);
                _ueArr.push({
                    id : 'uedetail_'+_ueNum,
                    name : '其他服务',
                    ue : ue
                });
            });
            
            //删除
            $this.on('click', '.icon-delete', function(e) {
                if(!dynamic) return;
                
                var _this = $(this);
                $.pt({
                    target: this,
                    width: 286,
                    position: 'b', 
                    align: 'c',   
                    autoClose: false,
                    leaveClose: false,
                    content:_deleteUeTempl
                });
                $.pt.delcall = function(){
                    _this.closest('.form-group').remove();
                    $('.pt').hide();
                    
                    var ueid = _this.closest('.form-group').find('.ueedit-box').attr('id');
                    for(var i=0; i<_ueArr.length;i++){
                        if(ueid == _ueArr[i].id){
                            _ueArr.splice(i,1);
                            break;
                        }
                    }
                }
            });
            
            $('body').on('click', '.js-delete-ue', function(e) {
                $.pt.delcall&&$.pt.delcall();
            });
            
            //编辑
            $this.on('click', '.icon-edit', function() {
                var _this = $(this);
                $editName = _this.closest('label').find('.name');

                $.pt({
                    target: this,
                    width: 286,
                    position: 'b', 
                    align: 'c',   
                    autoClose: false,
                    leaveClose: false,
                    content: _editTempl.replace('{name}', $editName.text())
                });
                
                $.pt.editcall = function(){
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
                    
                    var ueid = _this.closest('.form-group').find('.ueedit-box').attr('id');
                    for(var k=0;k<_ueArr.length;k++){
                        if(ueid == _ueArr[k].id){
                            _ueArr[k]['name']=val;
                            break;
                        }
                    }
                }
            });

            $('body').on('click', '.edit-btn', function(e) {
                $.pt.editcall&&$.pt.editcall();
            });
            
            // 取消
            $('body').on('click', '.cancel-btn', function(e) {
                $('.pt').hide();
            });
        }
        
        var init = function(){
            _bindEvent();
        }
        init();
        
        return {
            /*getUeditor : function(ueid){
                for(var j=0;j<_ueArr.length;j++){
                    if(ueid == _ueArr[j].id){
                        return _ueArr[j];
                    }
                }
                return '';
            },*/
            // 取得所有的编辑器
            getAll: function() {
                return _ueArr;
            },

            // 只取第一个编辑器
            get: function() {
                return _ueArr[0];
            },

            // 是否全部初始化完成
            isReady: function() {
                return _left === 0;
            },

            // 取得内容
            getContent: function() {
                var arr = [], ueCon = '';

                for(var i = 0, len = _ueArr.length; i < len; i++) {
                    ueCon = _ueArr[i].ue.getContent();
                    if(ueCon == '') continue;
                    
                    arr.push({
                        name: _ueArr[i].name,
                        detail: ueCon
                    });
                }

                return JSON.stringify(arr);
            }
        }
    }
})