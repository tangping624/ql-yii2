seajs.use(['utils','dialog','template','grid','form'],function(utils){
    var _deletedTemp = $('#de_templ').html();
    var curid;

    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    };

    var _bindEvent=function(){
        //删除
        $('body').on('click', '.opt-deleted', function() {
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
        //删除确认
        $('body').on('click','.tips-wrap .deleted-oper',function(){
            var $this=$(this);
            var url='',classN=this.className,data={},flag;
            if(classN.indexOf('deleted-oper')){
                url=flag='deleted';
                data={
                    id:curid
                };
            }else{
                return;
            }

            if($this.hasClass('bg-green')){
                O.ajaxEx({
                    data:data,
                    type:'get',
                    url:O.path('/basic/advert/del-advert-type'),
                    success:function(){
                        if(flag=='deleted'){
                            location.reload();
                        }
                        $('.pt').hide();
                    },
                    error:function(){
                        $('.pt').hide();
                        _errorCallback();
                    }
                })
            }else{
                $('.pt').hide();
            }
        })

        //错误回调函数
        var _errorCallback=function(){
            var d=$.tips('网络错误');
            setTimeout(function () {
                d.close().remove();
            },2000)
        }


        //拖动
        //sortable('enable')启用拖曳功能
        $('body').on('click','.opt-btn',function(){
            var $this=$(this);
            if($this.hasClass('opt-dosort')){
                $(this).hide();
                $('.opt-sort').show();
                $('.opt-finish').show();
                $('.opt-docancle').show();
                $('.opt-edit').hide();
                $('.opt-deleted').hide();
                $('.sort_list').sortable({item:"tr",placeholder:'sortable-placeholder'}).sortable('enable')
            }else if($this.hasClass('opt-docancle')){
                $('.sort_list').sortable('disable');
                $(this).hide();
                $('.opt-finish').hide();
                $('.opt-edit').show();
                $('.opt-deleted').show();
                $('.opt-dosort').show();
                $('.opt-sort').hide();
            }else if($this.hasClass('opt-finish')){
                var sortlist=[];
                $.each($('.sort_list tr'),function(){
                    sortlist.push({
                        id:$(this).find('a.opt-sort').data('id'),
                        sort:$(this).index()+1
                    });
                });
                O.ajaxEx({
                    url:O.path('/basic/advert/sort'),
                    data:{ids:JSON.stringify(sortlist)},
                    type:'post',
                    success:function(res){
                        if(res.result){
                            window.location.reload();
                        }
                    },
                    error:function(res){
                        var d = $.tips(res.msg,'tips');
                        setTimeout(function() {
                            d.close().remove();
                        }, 2000);
                        $('.opt-hide').css('display', 'none');
                        $('.opt-dosort').show();
                        $('.opt-edit').show();
                        $('.opt-deleted').show();
                        $('.sort_list').sortable('disable');
                    },
                })
            }
        })

    }
    _bindEvent();
})
