<?php
/**
 * Created by PhpStorm.
 * User: tx-04
 * Date: 2017/4/25
 * Time: 9:15
 */
$this->title='麒麟文化--提醒消息'
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/modules/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/modules/css/index.css"/>
<style>
    body{background: #f0f0f0}
</style>
<?php $this->endBlock('css') ?>
<body>
  <!--header S-->
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size:18px;">消息</h1>
       </div>
       <a class="top-text" href="javascript:;" style="font-size: 1rem">删除</a>
     </div>
  </header>
  <!--header E-->
  <div class="padt1">
      <div class="messagebox">
         <h2><span>今日推荐</span></h2>
         <div class="info">
             <div class="tit clearfix">
                 <span class="time fd-right">2016-07-08</span>
                 <b>塞浦路斯一瞥</b>
             </div>
             <div class="con">
                <div class="mess"><p>塞浦路斯——一个恬静安逸、古风浓郁、贴近自然的岛国，对它的整体感觉是地中海那湛蓝湛蓝的天空、清澈碧透的海水，带着咸味的海风以及岛上人们宁静安详的生活。 塞浦路斯是一个有着悠久历史文化和壮观自然美景的小岛。相传这里就是 古希腊神话 恬静安逸、古风浓郁、贴近自然的岛国，对它的整体感觉是地中海那湛蓝湛蓝的天空、清澈碧透的海水，带着咸味的海风以及岛上人们宁静安详的生活。 塞浦路斯是一个有着悠久历史文化和壮观自然美景的小岛。相传这里就是 古希腊神话 中的爱与美神阿芙洛蒂特</p></div>
                <a href="javascript:;" class="more" style="top:0">[更多]</a>
             </div>
         </div>
      </div>
      <div class="messagebox">
         <h2><span>系统提醒</span></h2>
         <div class="info">
             <div class="tit clearfix">
                 <span class="time fd-right">2016-06-24</span>
                 <b>签证审核通过通知</b>
             </div>
             <div class="con">
                <div class="mess"><p>您好，你的塞浦路斯七日游签证审核信息已通过，我们会在一周内发送邮件到你预留的地址，请注意查收。</p></div>
             </div>
         </div>
      </div>
  </div>

  <?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/modules/js/jquery-1.9.1.js"></script>

<script type="text/javascript">
    $(function(){
        //展开更多
        $(".mess").each(function() {
            if($(this).find("p").height()> $(this).height()){
                $(this).next(".more").show();
                $(".more").each(function(){
                    $(this).click(function(){
                        $(this).prev(".mess").addClass("auto")
				  $(this).hide();
			   })
			})
	   }else{
                $(this).next(".more").hide();
            }

        });
    })
</script>
  <?php $this->endBlock()?>
