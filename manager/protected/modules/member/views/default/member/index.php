<?php
use yii\helpers\Html;
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css">
<link href="/modules/css/page/popup.css" rel="stylesheet">
<style>
    body{
        background-color: #E7E8EB;
    }
    .ui-dialog-title{font-size: 16px}
    .select-member-popup .popup-content{
        bottom:0;
    }
    .select-member-popup .multi-select-control{
        margin:60px 30px 0 60px;
    }
    .multi-select-control .search-text{
        border:1px solid #ccc;
    }
    .adjustLevel{
        width:275px;
        height:30px;
    }
    .ok-btn:hover {
        background: #008329;
    }
    .search-con li{margin-bottom: 0}
    select{color: #000}
    .form-bottom{
        border-top: none;
        background-color: #F4F5F9 !important;
    }
    .img-icon{width:50px;height:50px;border-radius: 25px;vertical-align: bottom;}
    .search-con .search:before{display:inline-block;content:'';}
    .mb15{margin-bottom:15px;}

    /*去掉分页部分的边框*/
    .page-box{margin-right: 0;padding: 0}
    .table{position: relative}
    .grid .table tfoot td{border:none;position: absolute;  width: 100%;z-index: 1000;padding: 10px 0;}
    .manage-content{padding-bottom: 80px}
</style>
<?php $this->endBlock() ?>
<?php //var_dump($data);exit;?>
<div class="manage-content">
    <div class="padding">
        <h4 class="manage-title" style="color: #333;">会员管理</h4>
<!--        <input type="hidden" value="--><?//=$account_id?><!--" id="account_id">-->
    </div>
     <div class="padding grid">
        <ul class="form search-con clearfix mb15" id="search_con">
            <li>
                <span style="display:inline-block;">会员信息</span>
                <input type="text" class="form-control" name="keywords" id="name" style="width: 200px;display:inline-block;margin-left:10px;" placeholder="
会员名/手机号码">
            </li>
            
            <li class="search">
                <a href="javascript:;" class="btn btn-primary" id="btn_search">搜索</a>
            </li>
        </ul>
        <div class="grid-content clearfix" id="member_grid">
            <table class="table">
                <thead>
                <tr class="table table-hover on">
                    <th width="30%">会员头像</th>
                    <th width="30%" style="">会员名</th>
                    <th width="20%" style="">手机号码</th>
                    <!-- <th width="200">会员卡号</th>
                    <th width="200">会员积分</th>
                    <th width="200">会员等级</th> -->
                    <th width="20%" class="align-r" style="padding-right: 30px">注册时间</th>
                   <!--  <th width="150" class="align-r" style="padding-right:25px">操作</th> -->
                </tr>
                </thead>
                <tbody>
                <tr><td colspan="7" style="height:70px; text-align: center;">请先搜索会员用户</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/template" id="pop">
    <div class="popup-container select-member-popup">
        <div class="popup-content">
            <div class="multi-select-search">
                <div class="multi-select-control multi-select-control-default">
                    <span style="font-size:15px;margin-right: 30px;">会员级别</span>
                    <select class="adjustLevel" name="keywords" type="text" style="font-size:13px;">
                        <option value="choose">-选择会员等级-</option>
                    </select>
                    <div id="input_select_control_result" class="search-result">
                        <ul class="sr-list" id="iscr_items">
                        </ul>
                    </div>
                </div>
            </div>

            <div class="form-bottom align-c">
                <button type="button" class="btn-pr ok-btn" id="btn_ok">确定</button>
                <button type="reset" class="btn-pr sub-btn" id="btn_cancel">取消</button>
            </div>
        </div>
</script>
<!--用户列表模板-->
<script type="text/template" id="grid_template">
    <td class="id" data-id="" style=""><img  class="img-icon "  style="vertical-align: middle;" src="<%- headimg_url||'/modules/images/yonghu.png' %>"></td>
    <td style="">
     
     <span style="margin-left:5px;"><%- name %></span>
    </td>
    <td style=""><%- mobile||'' %></td>
    
    <td class="align-r"><%-  join_date.split(' ')[0]||'' %></td>
    
</script>

<?php $this->beginBlock('js') ?>
<script type="text/javascript">
    __REQUIRE('/modules/js/member/index.js');
</script>
<?php $this->endBlock() ?>
