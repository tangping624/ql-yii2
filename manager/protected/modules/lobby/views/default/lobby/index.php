
<?php
$this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css">
<style type="text/css">
    /*.advert-manage{  font-size: 21px;  margin-top: 12px;  margin-left: 6%;  }*/
    .advert{  font-size: 14px;  margin-left: 4.5%;  maargin-top:30px;  width:6%;  padding:15px;  }
    .search-con li {  margin-right: 3%;  }
    .ad-last{  margin-top: 0px;  }
    .classify-color{  border-bottom: 3px solid #44b549!important;  }
    .advert_classify{  width:100%;  height:60px;  }
    .advert_classify>li{  height: 60px;  float:left;  margin-right: 5px; overflow: hidden;  text-align: center;  font-size: 14px;  line-height: 48px;}
    .advert_classify>li:hover{  border-bottom: 3px solid #d4d4d4;cursor:pointer;}
    .edit-box{border: 1px dashed transparent;border-bottom: 1px solid #E7E6EB;}
    .edit-box .icon-tedit, .table-pr .noedit-box .icon-tedit{display: none;}
    .edit-box:hover, .table-pr .tedit-checked{border: 1px dashed #3EB642;position: relative;}
    .edit-box:hover .icon-tedit, .table-pr .tedit-checked .icon-tedit{display:inline-block;position: absolute;right:0;top:0;cursor: pointer;}
    .upload{ position:absolute;top:207px;left:420px;}
    /*#tour_grid .table>tbody>tr>td{padding:5px;}*/
    .search-con .search:before{display:inline-block;content:'';}
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
</style>
<?php $this->endBlock() ?>
<div class="manage-content">
    <div >
         <h4 class=" padding manage-title">游说管理</h4>
    </div>
    <div class="padding grid">
        <div class="grid">
            <ul class="form search-con clearfix" id="search_con">
                <li>
                    <span style="display:inline-block;">标题</span>
                    <input type="text" class="form-control" name="keywords" id="name" style="width: 200px;display:inline-block;margin-left:10px;" placeholder="
标题">
                </li>
                <li class="search">
                    <a href="javascript:;" class="btn bg-green color-white" id="btn_search">搜索</a>
                </li>
                <li class="search pull-right ad-last" >
                    <a class="btn-pr bg-green color-white" href="/lobby/lobby/add
">
                        <span class="glyphicon glyphicon-plus"></span><span>新增游说</span>
                    </a>
                </li>
            </ul>
            <div class="grid-content clearfix" id="tour_grid">
                <table class="table">
                    <thead>
                    <tr >
                        <th style="width:30%;">标题</th>
                        <th style="width:30%;">发布人</th>
                        <th style="width:31%;">发布时间</th>
                        <th style="width:9%;padding-left: 23px;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="9" style="height:70px;text-align: center;">请先搜索游说</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="grid_template">
    <td><p class="contents"><%-__data.title%></p></td>
    <td><% if(__data.source=='1'){%><%= __data.cname%><% }else{%><%= __data.name%><% } %></td>
    <td><%-__data.created_on.split(' ')[0]%></td>
    <td>
        <a href="/lobby/lobby/add?id=<%-__data.id%>" class="opt-btn opt-editmember" >编辑</a>
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
<script src="/modules/js/lobby/index.js"></script>
<?php $this->endBlock(); ?>

