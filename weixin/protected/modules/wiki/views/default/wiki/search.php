<?php
use yii\helpers\Html;
$this ->title='百科搜索';
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/index/index.css" />
<link rel="stylesheet" href="/modules/css/base.css" />

<?php //var_dump($advert);exit;?>
  <!--header S-->
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <input type="text" value="" class="top-inptxt" />
       </div>
       <a class="top-text" href="javascript:;" style="font-size: 0.88rem">确定</a>
     </div>
  </header>
  <!--header E-->
  <div class="padt1">
      <div class="Box mart0">
         <ul class="clearfix searchList">
             <li class="empty" style="display: none;text-align: center;"><b>暂无搜索记录</b></li>
         </ul>
      </div>
      <div class="clearbtn" style="display: none;padding: 2rem 2.56rem;"><a href="#">清除历史记录</a></div>
  </div>
  <?php $this->beginBlock('js') ?>
  <script type="text/javascript" src="/mobiend/js/lib/zepto/zepto.js"></script>
  <script type="text/javascript">
        //页面搜索记录的显示
        var str=localStorage.searchlist;
        if(!str){
            $('.empty').show();
            $('.clearbtn').hide();
        }else{
            $('.clearbtn').show();
            $('.empty').hide();
            if(str.indexOf('|')==-1){
                $('.searchList').append('<li class="searchHistory"><b>'+str+'</b></li>');
            }else{
                str=str.split('|');
//                str=$.trim(str);
                for(var i=str.length-1;i>=0;i--){
                    $('.searchList').append('<li class="searchHistory"><b>'+str[i]+'</b></li>');
                }
            }
            $('.searchList li b').each(function(i,v){
                $(v).html().length==0&&$(v).closest('.searchHistory').remove();
            })
        }

        //向本地存储写入搜索记录
        $('.top-text').click(function(){
            var searchText=$(this).prev().find('input').val().trim();
//            searchlist=localStorage.searchlist;
            console.log(searchText.length);
            if(!localStorage.searchlist){
                localStorage.setItem('searchlist',searchText);
            }else{
                $('.clearbtn').show();
                var historyItems='';
                if(searchText.length!=0){
                    searchlist=localStorage.searchlist;
                    searchlist = searchText + '|' + searchlist.split('|').filter(function(e){return e != searchText }).join('|');
                    var keywords=searchlist.split('|');
                }
                if(searchText){
                    for(var i=0;i<5;i++){
                        if(!keywords[i]){
                            continue;
                        }
                        historyItems+=keywords[i]+'|';
                    }
                    localStorage.searchlist =historyItems||searchlist;
                }
            }
            location.href='/wiki/wiki/search-index?id=&keywords='+searchText;
        })
        //清空历史搜索记录

        $('.clearbtn').click(function(){
            localStorage.removeItem('searchlist');
            $('.searchHistory').remove();
            $('.empty').show();
            $('.clearbtn').hide();

        })

        //点击历史记录条目也要进行关键词传递搜索
        $('.searchHistory').click(function(){
            var searchText=$(this).find('b').html();
            $('.top-inptxt').val(searchText);
            location.href='/wiki/wiki/search-index?id=&keywords='+searchText;
        })
  </script>
  <?php $this->endBlock() ?>
