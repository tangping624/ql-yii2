
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css"/>
<style type="text/css">
    .opt-finish{display: none;}
    .opt-docancle{display: none;}
    .opt-sort{display: none; top: -7px;}
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding: 8px;}
    .label{width:80px!important;margin-top:5px;font-size: 14px;text-align: right;margin-right: 10px;}
    .img_size{width:40px;height:40px;}
    th,td{text-align: center}
    .search-con .search:before{display:inline-block;content:'';}
</style>
<?php $this->endBlock() ?>
<?php //var_dump($model);exit;?>
<div class="manage-content">
    <h4 class="padding manage-title">分类设置</h4>
    <div class="padding">

    </div>
    <div class="padding grid member-form">
        <ul class="form search-con clearfix" id="search_con">
            <li class="search pull-right ad-last" >
                <a class="btn-pr bg-green color-white" href="/baike/bai-ke/add">
                    <span class="glyphicon glyphicon-plus"></span><span>新增分类</span>
                </a>
            </li>
        </ul>
        <div class="grid-content" id="type_grid">
            <table class="table">
                <thead>
                <tr class="notgoodsorder on">
                    <th class="align-l">分类名称</th>
                    <th class="align-r" style="padding-right: 18px;">
                        操作&nbsp;&nbsp;
                        <a title="完成" href="javascript:;" class="btn btn-primary opt-btn opt-hide opt-finish">完成</a>
                        <a title="取消" href="javascript:;" class="btn btn-secondary opt-btn  opt-hide opt-docancle">取消</a>
                    </th>
                </tr>
                </thead>
                <tbody class="sort_list">
                <tr><td colspan="7" style="height:70px;text-align: center;">正在加载数据...</td></tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/template" id="type_templ">
    <%for(var i=0;i<data.length;i++){%>
        <tr>
            <td class="align-l"><%=data[i].name%></td>
            <td class="align-r">
                <span class="w5" style="">
                    <a href="/baike/bai-ke/add?id=<%=data[i].id%>" data-id=" " class="opt-btn opt-edit">编辑</a>
                    <a href="javascript:;" class="opt-btn opt-deleted"   data-id="<%=data[i].id%>">删除</a>
                </span>
            </td>
        </tr>
    <% }%>
</script>
<!--模板-->
<script type="text/template" id="de_templ">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr btn-refund bg-green color-white deleted-oper">确定</button>
        <button class="btn-pr btn-refund bg-white cancel-btn fr">取消</button>
    </div>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/frontend/js/lib/jquery.ui/jquery.sortable.js"></script>
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
<script type="text/javascript">
    seajs.use('/modules/js/baike/index',function(index){
        index.init();
    })
</script>
<?php $this->endBlock(); ?>