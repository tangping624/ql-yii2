
<?php
$this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css">
<style type="text/css">
    .ad-last{  margin-top: 0px;  }
    .advert_classify>li{  height: 60px;  float:left;  margin-right: 5px; overflow: hidden;  text-align: center;  font-size: 14px;  line-height: 48px;}
    .advert_classify>li:hover{  border-bottom: 3px solid #d4d4d4;cursor:pointer;}
    /*去掉分页部分的边框*/
    .page-box{margin-right: 0;padding: 0}
    .table{position: relative}
    .grid .table tfoot td{border:none;position: absolute;  width: 100%;z-index: 1000;padding: 10px 0;}
    .manage-content{padding-bottom: 80px}
    .search-con .search:before{display:inline-block;content:'';}
</style>
<?php $this->endBlock() ?>
<div class="manage-content">
    <div >
         <h4 class=" padding manage-title">百科管理</h4>
    </div>
    
    <div class="padding grid">
        <div class=" grid">
            <ul class="form search-con clearfix" id="search_con">
                <li class="name">
                    <span style="display:inline-block;">标题</span>
                    <input type="text" class="form-control searchinput" name="keywords" placeholder="标题" style="width: 200px;display:inline-block;margin-left:10px;">
                </li>
                <li  class="search" style="margin-top: -2px">
                    <a href="javascript:;" class="btn-pr  bg-green color-white" id="btn_search">
                        <span>搜索</span>
                    </a>
                </li>
                <li class="search pull-right ad-last" >
                    <a class="btn-pr bg-green color-white" href="/baike/manage/add
">
                        <span class="glyphicon glyphicon-plus"></span><span>新增百科</span>
                    </a>
                </li>
            </ul>
            <div class="grid-content clearfix" id="tour_grid">
                <table class="table">
                    <thead>
                    <tr >
                        <th width="50%">标题</th>
                        <th width="30%" class="align-l">分类名称</th>
                        <th width="20%" class="align-r"  style="padding-right: 25px">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="9" style="height:70px; text-align: center;">请先搜索百科</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="grid_template">
    <td class="clearfix">
       <div class="fl">
           <p style="width: 350px;" class="ellipsis"><%-__data.title%></p>
       </div>
    </td>
    <td>
        <%- __data.name||'' %>
    </td>
    <td class="align-r">
        <a href="/baike/manage/add?id=<%-__data.id%>" class="opt-btn opt-editmember">编辑</a>
       <a href="javascript:;" class="opt-btn opt-deleted"   data-id="<%-__data.id%>">删除</a>
    </td>
</script>
<script type="text/template" id="deleted_info">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr btn-refund bg-green color-white deleted-oper">确定</button>
        <button class="btn-pr btn-refund bg-white deleted-oper fr">取消</button>
    </div>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/js/public/public.js"></script>
<script type="text/javascript" src="/frontend/js/lib/global.js"></script>
<script type="text/javascript">
    seajs.use('/modules/js/baike/manage-index',function(index){
        index.init();
    });
</script>
<?php $this->endBlock(); ?>

