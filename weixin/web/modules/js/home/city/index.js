define(function(require, exports, module){
    // require('../../../../mobiend/js/mod/app');
    require("../../../../mobiend/js/lib/public");
    var template=require("../../../../mobiend/js/lib/art-template.js");

    var alongitude = $('#longitude').val(),alatitude = $('#latitude').val();
    module.exports={
        init:function(){
            this.GetAddr();
        },
        GetAddr:function(){
            var city = $.deCode($.getUrlParam('city'));
            $('.num_name').html(city);
            cityList('');
            $('.city_se').keyup(function(){
                var name = $('.city_se').val();
                cityList(name);  
            });
            $(document).on('click','.sort_name',function(){
                var citys_id = $(this).attr('data-id');
                var citys = $(this).text();
                window.location="/home/home/index?citys="+$.enCode(citys)+"&citys_id="+citys_id;
            });
        },
    };
    //城市列表
    function cityList(name){
        $.ajax({
            type: 'GET',
            data:{name:name},
            url:"/home/city/ajax-get-city",
            success: function(data){
                var html = template('city_tpml', {data:data});
                $('#city_list').html(html);
            }
        });
    }
});