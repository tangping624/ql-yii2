
<?php
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/global.css">
    <style type="text/css">
       #shop_grid .table .contents{
           width: 215px;
           display: block;
           overflow: hidden;
           text-overflow: ellipsis;
           white-space: nowrap;}
        #shop_grid .table td{}
        .search-con .search:before{display:inline-block;content:'';}
       /*去掉分页部分的边框*/
       .page-box{margin-right: 0;padding: 0}
       .table{position: relative}
       .table tfoot td{border:none;position: absolute;  width: 100%;z-index: 1000;padding: 10px 0;}
       .manage-content{padding-bottom: 80px}
    </style>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div>
            <h4 class="padding manage-title">商家管理</h4>
        </div>
        <div class="padding">
            <ul class="form search-con clearfix" id="search_con">
                <li>
                    <span style="display:inline-block;">商家名称</span>
                    <input type="text" class="form-control" name="keywords" id="name" style="width: 200px;display:inline-block;margin-left:10px;" placeholder="
商家名称">
                </li>
                <li class="search">
                    <a href="javascript:;" class="btn btn-primary" id="btn_search">搜索</a>
                </li>
                <li class="search pull-right" >
                    <a class="btn-pr bg-green color-white" href="/merchant/merchant/add">
                        <span class="glyphicon glyphicon-plus"></span><span>新增商家</span>
                    </a>
                </li>
            </ul>
            <div class="grid-content clearfix" id="shop_grid">
                <table class="table">
                    <thead>
                    <tr class="notgoodsorder on">
                        <th style="width:120px">商家名称</th>
                        <th style="width:150px">商家地址</th>
                        <th style="width:200px">商家简介</th>
                        <th style="width:100px">序号</th>
                        <th style="width:80px">是否推荐</th>
                        <th style="width:180px;text-align: center;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="9" style="height:70px;text-align: center;line-height: 70px">请先搜索商家</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/template" id="grid_template">
        <td>
            <p class="contents" style="width:150px"><%- __data.name %></p>
        </td>
        <td>
            <p class="contents"><%- __data.address %> </p>
        </td>
        <td>
            <p class="contents"><%- __data.content %> </p>
        </td>
        <td >
           <% if(__data.sort!='1410065407'){%>
           <%- __data.sort %> 
           <% } %>
        </td>
        <td class="js-edit-box edit-box"  data-id="<%-__data.id %>">
            <div class="td-pa">
                <p><label class="iosCheck green"><input type="checkbox" <%if (__data.is_recommend == '1'){ %> checked="checked"; <% } %> class="js-ioscheck"><i></i></label></p>
            </div>
        </td>
        <td>
            <a href="/merchant/merchant/add?id=<%-__data.id%>" class="opt-btn opt-editmember">编辑</a>
            <a href="javascript:;" class="opt-btn opt-deleted"   data-id="<%-__data.id%>">删除</a>
            <a href="javascript:;" class="copy" id="extend_btn" data-clipboard-action="copy" data-clipboard-text="/pub/seller/details?id=<%-__data.id%>&appcode=<%-__data.app_code%>">复制链接</a>
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
    <script type="text/javascript" src="/frontend/js/lib/clipboard.min.js"></script>
    <script type="text/javascript" src="/frontend/js/lib/global.js"></script>
    <script src="/modules/js/merchant/merchant/index.js"></script>
<?php $this->endBlock(); ?>

