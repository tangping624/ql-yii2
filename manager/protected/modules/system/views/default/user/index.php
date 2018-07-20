<?php 
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?> 
     <link rel="stylesheet" href="/modules/css/global.css"/>
     <link href="/modules/css/global/public.css" rel="stylesheet">
    <link href="/modules/css/page/page.css" rel="stylesheet">
<style>
    .tab-nav li{margin:0;padding: 0}
    .search-con .search:before{display:inline-block;content:'';}
    .search-con li{margin-bottom: 0}
    .bg-green{padding: 5px 30px}
</style>
<?php $this->endBlock() ?>
    <div class="manage-content"> 
         <div class="padding"> 
            <h4 class="manage-title">用户管理</h4> 
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li data-type="all" class="on"><span>全部</span></li> 
            <li data-type="normal"><span>正常</span></li>
            <li data-type="disables"><span>已禁用</span></li> 
        </ul>
        <div class="padding grid"> 
           <ul class="form search-con clearfix" id="search_con"> 
                <li class="name"> 
                    <span style="display: inline-block">用户信息</span>
                    <input type="text" class="form-control searchinput" id="userinfo" name="userinfo" placeholder="姓名/手机号码" style="display: inline-block;width: 200px;margin-left: 10px">
                </li>   
                <li  class="search">
                    <a href="javascript:;" class="btn-pr  bg-green color-white" id="btn_search">
                        <span>搜索</span>
                    </a>
                </li> 
                 <div class="add-btn clearfix plr30" style="padding-top: 0">
                    <a class="btn-pr bg-green color-white" href="javascript:" id="add_user_btn">
                        <span class="icon-merge add"></span><span>新增用户</span>
                    </a>
                </div>  
            </ul>  
            <div class="grid-content clearfix" id="user_grid">
                <table class="table">
                    <thead>
                    <tr class="table table-hover on">
                        <th width="80">姓名</th>
                        <th width="120">帐号</th> 
                        <th width="120">手机</th>
                        <th width="170">邮箱</th> 
                        <th width="60">状态</th> 
                    </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" style="height:70px;text-align: center;">请先搜索用户</td></tr>
                    </tbody>
                </table>
            </div> 
        </div>
        <div class="popup-panel user-info-panel" id="user_details_panel">
            <div class="pp-head">
                用户资料
                <span class="fonticon fonticon-remove" id="ud_close"></span>
            </div>
            <div class="pp-content">
                <div class="user-infor">
                    <div class="ui-first">
                        <p class="ui-name" id="ud_name"></p>

                        <p class="ui-username" id="ud_account"></p>
                        <input type="hidden" id="ud_id"/>
                    </div>
                    <div class="ui-list">
                        <p class="ui-item">
                            <span class="ui-label">手机</span>
                            <span class="ui-info" id="ud_mobile"></span>
                        </p> 
                        <p class="ui-item">
                            <span class="ui-label">邮箱</span>
                            <span class="ui-info" id="ud_email"></span>
                        </p> 
                    </div>
                </div>
            </div> 
            <div class="pp-toolbar">
                <a class="btn btn-default" href="javascript:;" id="ud_edit_btn">修改</a> 
                <a class="btn btn-default" href="javascript:;" id="ud_reset_password_btn">修改密码</a>
                <a class="btn btn-default" href="javascript:;" id="ud_disable_btn">禁用</a>
                <a class="btn btn-default" style="display: none;" href="javascript:;" id="ud_enable_btn">启用</a>
                <a class="btn btn-default" href="javascript:;" id="ud_delete_btn">删除</a>
            </div> 
        </div> 
    </div>
     <!--用户列表模板-->
    <script type="text/template" id="user_gridrow_template">
        <td><%- name %></td>
        <td><%- account %></td> 
        <td><%- mobile %></td>
        <td><%- email %></td>   
        <td><%- enabled %></td>     
    </script> 
   
    <script type="text/template" id="deleted_info">
        <div class="tips-wrap">
            <div class="delete-info">确定删除？</div>
            <button class="btn-pr btn-refund bg-green color-white deleted-oper">确定</button>
            <button class="btn-pr btn-refund bg-white deleted-oper fr">取消</button>
        </div>
    </script>
      
    <script type="text/template" id="redisabled_info">
        <div class="tips-wrap">
            <div class="delete-info">确定启用？</div>
            <button class="btn-pr btn-refund bg-green color-white redisabled-oper">确定</button>
            <button class="btn-pr btn-refund bg-white redisabled-oper fr">取消</button>
        </div>
    </script>
    
    <script type="text/template" id="disabled_info">
        <div class="tips-wrap">
            <div class="delete-info">确定禁用？</div>
            <button class="btn-pr btn-refund bg-green color-white disabled-oper">确定</button>
            <button class="btn-pr btn-refund bg-white disabled-oper fr">取消</button>
        </div>
    </script>
<?php $this->beginBlock('js') ?>    
        <script type="text/javascript">
        seajs.use('/modules/js/system/user/index.js', function (index) {
            index.init();
        });
        seajs.use('/frontend/js/plugin/dataTitle.js');
    </script>
<?php $this->endBlock(); ?>
 