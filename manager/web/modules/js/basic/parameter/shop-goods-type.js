seajs.use(['utils','dialog','template','grid','form'], function (utils) {
    var _deletedTemp = $('#de_templ').html();  
    var curid;
	var sortList = [];
	var this1;
    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    };
    var _ac=getQueryString("_ac");
    if(_ac == 'merchant'){
        urlkeys='?_ac=merchant';
    }else{
        urlkeys='?_ac=group'
    }
     var _bindEvent = function() {
         //后退/返回上一层
         $("body").on("click",".backwards",function(){
             window.location.href='/basic/parameter/index'+urlkeys;
         })
         //点击编辑/新增
         $("body").on("click",".opt-edit",function(){
             curid=$(this).attr("data-id");
             window.location.href='/basic/merchant/merchant-goods-type-add'+(curid?'&id='+curid:'')+urlkeys;
         })
          // 删除 
         $('body').on('click', '.opt-deleted', function() {  
              this1 = $(this);
              curid= $(this).attr("data-id");
              $.pt({
                target: this,
                width: 286,
                position: 'b', 
                align: 'c',   
                autoClose: false,
                leaveClose: false,
                content: Template(_deletedTemp)
            }); 
         }); 
         
         //拖动
         $('body').on('click', '.opt-btn', function() {
         	var $this = $(this);
         	if ($this.hasClass('opt-dosort')) {
         	$(this).hide();
            $('.opt-sort').show();
            $('.opt-finish').show();
            $('.opt-docancle').show();
            $('.opt-edit').hide();
            $('.opt-deleted').hide();
            $('.sort_list').sortable({item: 'tr', placeholder: 'sortable-placeholder'}).sortable('enable');
         }else if($this.hasClass('opt-docancle')) {
         	$('.sort_list').sortable('disable');
         	$('.opt-hide').css('display', 'none');
            $('.opt-dosort').show();
            $('.opt-edit').show();
            $('.opt-deleted').show();
         }else if($this.hasClass('opt-finish')){         	 
            $.each($('.sort_list tr'), function () {
                sortList.push({
                    id: $(this).find('a.opt-sort').data('id'),
                    sort: $(this).index() + 1
                });
            });
            O.ajaxEx({            	
                url: O.path('/basic/merchant/sort'+urlkeys),
                data: {ids: JSON.stringify(sortList)},
                type: 'post'
            }).then(function (res) {
                if (res.result) {
                    window.location.reload();
                } else {
                    $.tips(res.msg);
                    $('.opt-hide').css('display', 'none');
                    $('.opt-dosort').show();
                    $('.opt-edit').show();
                    $('.opt-deleted').show();
                    $('.sort_list').sortable('disable');
                }
            });
        }});
          // 编辑 
        $('body').on('click', '.tips-wrap .deleted-oper', function() {
            var $this = $(this); 
            var url = '',classN = this.className,data = {}, type = 'get',flag,valArr;  
            if(classN.indexOf('deleted-oper')){ 
                 url = flag = 'deleted'; 
                 data = { 
                     id: curid
                 };
            }else{return;
             } 
            if($this.hasClass('bg-green')){
                O.ajaxEx({
                    data: data,
                    type: type,
                    url: O.path('/basic/merchant/del-goods-type'+urlkeys),
                    success: function() { 
                        if(flag == 'deleted') { 
                        	this1.parent().parent().parent().remove();
                        	 $.each($('.sort_list tr'), function () {
				                sortList.push({
				                    id: $(this).find('a.opt-sort').data('id'),
				                    sort: $(this).index() + 1
				                });
			           		 });
                        	 O.ajaxEx({
				                url: O.path('/basic/merchant/sort'+urlkeys),
				                data: {ids: JSON.stringify(sortList)},
				                type: 'post',
				                success: function(data) { 
							    if (data.result) {
			                    	window.location.reload();
			                	} else {
			                    	$.tips(data.msg);
				                }
			                }
				            })           			 		
                        }
                        $('.pt').hide();
                    },
                    error: function() {
                        $('.pt').hide();
                        _errorCallback();
                    }
                });                
            } else {
                $('.pt').hide();
            }
        }); 
     };
     var _errorCallback = function() {
        var d = $.tips('网络错误');
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    };
    function getQueryString (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
        var context = "";
        if (r != null)
            context = r[2];
        reg = null;
        r = null;
        return context == null || context == "" || context == "undefined" ? "" : decodeURIComponent(context);
    }
    _bindEvent();
});
