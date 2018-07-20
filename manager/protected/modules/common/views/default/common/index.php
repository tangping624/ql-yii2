
<?php
$this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css">
<style type="text/css">
    .btn-pr{padding: 5px 30px}
    #tour_grid .table .contents {
        width: 215px;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
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
        <h4 class=" padding manage-title">商品管理</h4>
    </div>

    <div class="padding grid">
        <ul class="form search-con clearfix" id="search_con">
            <li class="name">
                <span style="display: inline-block;margin-right: 10px">商品名称/所属商家</span>
                <input type="text" class="form-control searchinput" name="keywords" placeholder="商品名称/所属商家" style="width: 200px;display: inline-block">
            </li>
            <li  class="search">
                <a href="javascript:;" class="btn-pr  bg-green color-white" id="btn_search">
                    <span>搜索</span>
                </a>
            </li>
            <li class="search pull-right" >
                <a class="btn-pr bg-green color-white" href="/common/common/add">
                    <span class="glyphicon glyphicon-plus"></span><span>新增商品</span>
                </a>
            </li>
        </ul>
        <div class="grid-content clearfix" id="tour_grid">
            <table class="table">
                <thead>
                <tr >
                    <th width="20%">商品图片</th>
                    <th width="35%">商品名称</th>
                    <th width="35%">所属商家</th>
                    <th width="10%" class="align-r"  style="padding-right: 25px">操作</th>
                </tr>
                </thead>
                <tbody>
                <tr><td colspan="9" style="height:70px; text-align: center;">商品数据加载中...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/template" id="grid_template">
    <td class="clearfix"><img src="<%- __data.logo||'' %>" alt="" style="width: 80px;height: 66px;"></td>
    <td><p class="contents"><%- __data.NAME||'' %></p></td>
    <td><p class="contents"><%- __data.seller_name||'' %></p></td>
    <td class="align-r">
        <a href="/common/common/add?id=<%-__data.id%>" class="opt-btn opt-editmember">编辑</a>
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
    seajs.use('/modules/js/common/index',function(index){
        index.init();
    });
</script>
<?php $this->endBlock(); ?>

