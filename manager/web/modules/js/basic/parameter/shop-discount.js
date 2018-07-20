/**
 * Created by tx-03 on 2016/8/5.
 */
seajs.use(['dialog','template'],function(){
    var _addTempl = $('#add_templ').html(),
        _deletedTemp = $('#deleted_info').html(),
        _isEdit = $('#is_edit').val();
    var  $editBox = $('.js-edit-box');//滑动按钮
    var curid,data=[],del_ele,_initDate,_count=0,_error;

    $('#add_yh').on('click',function(){
        _checkData();
        var html = Template(_addTempl);
        $("#main").append(html);
    });
    $('body').on('click', '.js-ioscheck', function () {
        var $this = $(this),
            td = $this.closest('td'),
            checked = $this.prop('checked');
        var toggle = function (checked) {
            if (checked) {
                td.attr('data-check','1');
            } else {
                td.attr('data-check','0');
            }
        };
        toggle(checked);
    });
    //删除
    $('body').on('click', '.opt-deleted', function () {
        del_ele = $(this);
        curid = del_ele.attr("data-id");
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
    //确认删除
    $('body').on('click', '.tips-wrap .deleted-oper', function () {
        var $this = $(this);
        if ($this.hasClass('bg-green')) {
            if(curid) {
                O.ajaxEx({
                    data: data,
                    type: 'get',
                    url: O.path('/basic/merchant/del-discount&id=' + curid),
                    success: function () {
                        $('#main').find('tr[data-id=' + curid + ']').remove();
                        $('.pt').hide();
                    },
                    error: function () {
                        $('.pt').hide();
                        _showTips('网络错误');
                    }
                });
            }else{
                del_ele.parent().parent().next().remove();
                del_ele.parent().parent().remove();
                $('.pt').hide();
            }
        }else {
            $('.pt').hide();
        }
    });
    $('#submit_btn').on('click',function(){
        var curEle = $(this);
        var data = _getData();
        _checkData();
        if(_error==0) {
            $(this).attr('disabled','true').removeClass('bg-green').addClass('color-gray');
            O.ajaxEx({
                type: 'post',
                data: {"discountList": JSON.stringify(data)},
                url: O.path('/basic/merchant/save-discount'),
                success: function (data) {
                    if (data.result == true) {
                        $(window).off('beforeunload');
                        location.href = '/basic/parameter/index?appcode=member';
                    } else {
                        _showTips(data.msg);
                        $(this).attr('disabled', 'false').removeClass('color-gray').addClass('bg-green');
                    }
                },
                error: function () {
                    _showTips('网络错误');
                }
            });
        }
    });
    $(window).on('beforeunload',function(e){
        if(!O.compare(_initDate,JSON.stringify(_getData()))){
            return '离开后，刚刚填写数据会丢失';
        }
    });
    var _getData = function(){
        if(data) data=[];
        $("#main").find("tr.detail").each(function(){
            var tdArr = $(this).children();
            var id=$(this).attr('data-id');
            var reach = tdArr.eq(0).find("input[name=reach]").val();
            var minus = tdArr.eq(0).find("input[name=minus]").val();
            var enabled;
            if(tdArr.eq(1).attr('data-check')==1){
                enabled = 1;
            }else{
                enabled = 0;
            }
            if(reach&&minus) {
                data.push({
                    "id": id,
                    "reach": reach,
                    "minus": minus,
                    "enabled": enabled
                })
            }
        });
        return data;
    };
    var _checkData = function(){
        _error=0;
        $("#main").find("tr.detail").each(function(){
            var tdArr = $(this).children();
            var reach = tdArr.eq(0).find("input[name=reach]").val();
            var minus = tdArr.eq(0).find("input[name=minus]").val();
            if(reach&&minus){
                if(parseFloat(reach) <= parseFloat(minus)||parseFloat(reach)<0||parseFloat(minus)<0) {
                    _error++;
                    $(this).next().removeClass('hide').addClass('block');
                } else {
                    $(this).next().removeClass('block').addClass('hide');
                }
            }else if(reach||minus){
                _error++;
                $(this).next().removeClass('hide').addClass('block');
            }

        });
    }
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 5000);
    };
    var init = function() {
        var interval = setInterval(function() {
            if(_count <= 0) {
                clearInterval(interval);
                _initDate = JSON.stringify(_getData());
                _count++;
            }
        }, 100)
    };
    init();
})
