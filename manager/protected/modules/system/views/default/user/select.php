<?php
 
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
<!--页面样式代码-->
<link href="/modules/css/global/public.css" rel="stylesheet">
<link href="/modules/css/page/popup.css" rel="stylesheet">
<?php $this->endBlock() ?>
<div class="popup-container select-member-popup">
    <div class="popup-content">
        <div class="multi-select-search">
            <div class="multi-select-control multi-select-control-default">
                <ul class="msc-list" id="selected_user_panel">
                    <li id="input_select"><input class="search-text" type="text"></li>
                </ul>
                <div id="input_select_control_result" class="search-result">
                    <ul class="sr-list" id="iscr_items">
                    </ul>
                </div>
            </div>
        </div>
        <div class="public-panel"> 
            <div >
                <h4 class="public-tit">用户列表</h4>

                <div class="grid table-responsive" id="user_grid">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="60" class="align-c">序号</th>
                            <th width="80">姓名</th>
                            <th width="200">帐号</th>
                            <th width="70">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="form-bottom align-c">
        <button type="button" class="btn-pr ok-btn" id="btn_ok">确定</button>
        <button type="reset" class="btn-pr sub-btn" id="btn_cancel">取消</button>
    </div>
</div>


<!--用户列表模板-->
<script type="text/template" id="user_gridrow_template">
    <td class="align-c"><%- i %></td>
    <td><%- name %></td>
    <td><%- account %></td>
    <td><span uid="<%- id %>" uname="<%- name%>" class="fonticon fonticon-checkbox <%- selected%>"></span></td>
</script>
<?php $this->beginBlock('js') ?>
<!--页面js代码-->
<script type="text/javascript">
    var userLevel = '<?=$level ?>';
    seajs.use('/modules/js/system/user/select.js', function (m) {
        m.init();
    });
</script>
<?php $this->endBlock() ?>
