<?php
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/global.css">
    <style type="text/css">
        .search-con .search:before{display:inline-block;content:'';}

        /*去掉分页部分的边框*/
        .page-box{margin-right: 0;padding: 0}
        .table{position: relative}
        .grid .table tfoot td{border:none;position: absolute;  width: 100%;z-index: 1000;padding: 10px 0;}
        .manage-content{padding-bottom: 80px}
        .ellipsis{width:150px;overflow: hidden;white-space: nowrap;}
    </style>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div>
            <h4 class="padding manage-title">新鲜事管理</h4>
        </div>
        <div class="padding grid">
            <ul class="form search-con clearfix " id="search_con">
                <li class="name">
                    <span style="display:inline-block;">标题/分类</span>
                    <input type="text" class="form-control" name="keywords" placeholder="标题/分类" style="width: 250px;height: 36px;display:inline-block;margin-left:10px;">
                </li>
                <li  class="search">
                    <a href="javascript:;" class="btn btn-primary" id="btn_search">
                        <span>搜索</span>
                    </a>
                </li>
                <li class="search pull-right" >
                    <a class="btn-pr bg-green color-white" href="/news/news/add">
                        <span class="glyphicon glyphicon-plus"></span><span>新增新鲜事</span>
                    </a>
                </li>
            </ul>
            <div class="grid-content clearfix" id="shop_grid">
                <table class="table">
                    <thead>
                      <tr class="notgoodsorder on">
                        <th  style="width:22%">标题</th>
                        <th style="width:22%">分类</th>
                        <th  style="width:22%">发布人</th>
                        <th  style="width:25%">发布时间</th>
                        <th  style="width:9%;padding-left: 23px">操作</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td colspan="9" style="height:70px; text-align: center;">请先搜索新鲜事</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/template" id="grid_template">
        <td><p class="ellipsis" ><%-__data.title%></p></td>
        <td><%-__data.typename%></td>
        <td><% if(__data.source=='1'){%><%-__data.uname%><% }else{%><%-__data.name%><% } %></td>
        <td><%-__data.created_on.split(' ')[0]%></td>
        <td>
          <a href="/news/news/add?id=<%-__data.id%>" class="opt-btn opt-editmember" >编辑</a>
          <a href="javascript:;" class="opt-btn opt-deleted"   data-id="<%-__data.id%>" >删除</a>
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
    <script type="text/javascript" src="/frontend/js/lib/global.js"></script>
    <script type="text/javascript">
      __REQUIRE('/modules/js/news/news/index.js');
    </script>
<?php $this->endBlock(); ?>

