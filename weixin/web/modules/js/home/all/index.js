define(function(require, exports, module){
    // require('../../../../mobiend/js/mod/app');
    require("../../../../mobiend/js/lib/public");
    var template=require("../../../../mobiend/js/lib/art-template.js");
    module.exports={
        init:function(){
            this.GetClass();
        },
        GetClass:function(){
            $.ajax({
                type: 'GET',
                data:'',
                url:"/home/all/ajax-get-type",
                success: function(data){
                    // $.each(data,function(index,item){  
                    //     if(item.treeText=='特产'){
                    //         data.splice(index,1);
                    //     }
                    //  });
                    var dataTpl = {list:data};
                    var html = template('classList', dataTpl);
                    $('#classBox').html(html);
                    classOne();
                    classTwo();
                }
            });
        },
    };
    
    function classOne(){
        $('.tit,.allDel').click(function(){
            var id= $(this).attr('id');
            var appcode= $(this).attr('appcode');
            var text;
            if($(this).hasClass('tit')){
                text = $(this).text().trim();
            }else{
                text = $(this).attr('data-val');
            } 
            var myHref = getUrl(text);
            location.href=myHref+id+'&appcode='+appcode;
        });
    }
    function classTwo(){
        $('.classDel').click(function(){
            var id = $(this).attr('id');
            var type_id = $(this).attr('type_id');
            var appcode = $(this).attr('appcode');
            var text = $(this).parents('.category').siblings('.tit').text().trim();
            var myHref = getUrl(text);
            location.href=myHref+id+'&appcode='+appcode+'&type_id='+type_id;
        });
    }
    function getUrl(i){
            var href='';
            if(i == '百科'){
                href = '/wiki/wiki/index?id=';
            }else if(i == '游说'){
                href = '/lobby/lobby/index?id=';
            }else if(i == '携程'){
                href = '/ctrip/ctrip/index?id=';
            }else{
                href = '/pub/seller/index?id='
            }
            return href;
    }
});