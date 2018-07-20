  
define(function (require, exports, module) {
    require('/modules/js/public/plugin/topTips.js');
    require('/modules/js/public/plugin/confirm.js');
    require('/frontend/js/plugin/grid.js');
    require('/frontend/js/lib/template.js');
    require('/frontend/js/lib/overall.js');
    require('dialog.js');
    require('/frontend/js/lib/dialog');
    var dialogPlus = require('/frontend/js/lib/artDialog/src/dialog-plus.js'); 
   var memberGrid=$('#member_grid'),  btnSearch=$('#btn_search'),searchCon=$('#search_con'); 
      var cache={
         
    };
    var id,level,box,self;
    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    }; 
      memberGrid.show();
    var load=function(queryType){
        memberGrid.find('tbody').html('<tr><td colspan="' + memberGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
       if(cache.view){
            cache.view.search();
        }else{ 
         cache.view=memberGrid.grid({
            idField : 'id',
            templateid : 'grid_template',
            pagesize :10,
            emptyText : '无数据', 
            method:'get',
            queryParams : function(queryType){
                cache.params={};
                // cache.params['Keywords']= $('input[name=keywords]').val()||'';
                // cache.params['id']= cache.queryType||'';
                cache.params={
                    "Keywords":$('input[name=keywords]').val()||'',
                    // "id":$(":selected").val()||''
                }
                return $.param(cache.params);
            },
            getReadURL : function(){
                var strurl = "/member/member/ajax-member-list";
                return O.path(strurl);
            },
            sortEvent : function(){
                //detailGrid.hide();
            },
            filter : function(model){
               model.set('type',cache.type);
//             model.set('enabled', model.get('enabled') == 1 ? '' : '禁用');
            },
            loaded:function(data){
                // console.log(data);
                cache.total = data.total; 
//                $(".opt-redisabled").filter("[data-isdisabled='1']").show();
//                $(".opt-disabled").filter("[data-isdisabled='0']").show();
 
            },  
        }); 
    }
    }; 

    /*绑定事件*/
    function initBindEvents() {
        btnSearch.off('click').on('click',function(){
            // queryType=$(":selected").val();
            // cache['queryType']=queryType;
            load();
        });
        searchCon.off('keyup').on('keyup','.form-control',function(e){
            if(e.keyCode==13){
                load();
            }
        });
        // $("body").on("click",".opt-adjust",function(){
        //     adjust();
        //     id=$(this).parent().prevAll(".id").data("id");
        //     self=$(this).parent().prevAll(".level");
        //     O.ajaxEx({
        //         // data: data,
        //         type: 'get',
        //         url: O.path('/member/member/member-level'),
        //         success: function (data) {
        //            $.each(data.model,function(item,val){
        //                $(".adjustLevel").append($("<option class='level' data-id="+val.id+">"+val.name+"</option>"))

        //            })
        //         },
        //         error: function() {
        //             showMessage('网络错误');
        //         }
        //     });

        // })
        // //保存信息
        // $("body").on("click", "#btn_ok", function () {
        //     if($(".adjustLevel>option:selected").val()=="choose"){
        //         showMessage('请选择会员等级');
        //     }
        //     else {
        //         O.ajaxEx({
        //             data: {
        //                 "id": id,
        //                 "level_id": $(".adjustLevel>option:selected").data("id")
        //             },
        //             type: 'get',
        //             url: O.path('/member/member/adjust-member-level'),
        //             success: function (data) {
        //                 if (data.result) {
        //                     showMessage("修改成功", "isNormal");
        //                     level = $(".adjustLevel>option:selected").html();
        //                     box.close();
        //                     box.remove();
        //                     self.html(level);
        //                 } else {
        //                     box.close();
        //                     box.remove();
        //                 }
        //             },
        //             error: function () {
        //                 showMessage('网络错误');
        //             }
        //         });
        //     }
        // })
        // $("body").on("click","#btn_cancel",function(){
        //     box.close();
        //     box.remove();
        // })

    }
    //弹出对话框
    function adjust(){
        box = $.box({
            content : Template($("#pop").html()),
            title :  '调整会员等级' ,
            height : '230px',
            width : 499
        });
    }
    function showMessage(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    /**
     * 消息提示
     * @param message
     * @param isNormal
     */
    function showMessage(message, isNormal) {
        $.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    } 
    
    

//      memberGrid.off('click').on('click','tbody tr:visible,.opt-add',function(){
//        var $this=$(this),
//            id=$this.find('.id').data('id')||$this.data('id'),
//            model=cache.view.grid.get(id);
//        if(model){
//            var pId=model.get('id');
//            if($this.hasClass('opt-add')){
//                dialog([{id:pId}],id,pId,function(point){
//                    var val=(model.get('integral_total')-'')+point;
//                    model.set('integral_total',val%1?val.toFixed(2):val);
//                });
//            }
//            $this.addClass('on').siblings().removeClass('on');
//            id&&detail(id,pId);
//        }
//    });
 


    
    /**
     * 初始化
     */
    function init() { 
        //绑定页面事件
         initBindEvents();  
        load(); 
    }

    init();
})
;