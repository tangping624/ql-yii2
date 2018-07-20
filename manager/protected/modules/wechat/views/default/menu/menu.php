<?php
$this->title = Yii::$app->params['system_name']; 
$account_id = $data['wechat_account']['id'];
$account_name = $data['wechat_account']['name'];
$app_id = $data['wechat_account']['app_id'];
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/menu/menu.min.css?v=0d37cac795"/>
<?php $this->endBlock() ?>
<div class="manage-content">
    <div class="padding mb30 border-bottom">
        <h4 class="manage-title">自定义菜单</h4>  <input type="hidden" id="account_id" value="<?=$account_id ?>"/>
    </div>
    <div class="padding">
        <form class="form form-preview">
            <div class="clearfix">
                <div class="pull-left">
                    <div class="mobile-preview">
                        <div class="mobile-header"><?= $account_name ?></div>
                        <div class="mobile-menu" id="js_menu"></div>
                    </div>
                </div>
                <div class="form-content pull-right hide" id="js_content">
                    <span class="icon-merge trigon"></span>
                    <div class="menu-editor" id="js_menu_editor"></div>
                </div>
                <div class="from-content-tips pull-right gray hide" id="js_content_tips"></div>
            </div>
            <div class="form-bottom">
                <div class="mobile-sort-btn">
                    <a href="javascript:;" class="btn btn-secondary btn-disable" id="sort_btn">菜单排序</a>
                </div>
                <div class="content-btn">
                    <a href="javascript:;" class="btn btn-primary" id="save_btn">保存并发布</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/template" id="menu_tpl">
    <%
    var data = data||[];
    var size = data.length+1;
    size = size > 3 ? 3 : size;
    %>
    <ul class="menu-list menu-size-<%-size%><%-data.length===3?' limit':''%>" data-size="<%-size%>"
        data-length="<%-data.length%>">
        <% for (var i=0; i < data.length; i++) {
        var items = data[i].items||[];
        %>
        <li class="menu-item<%-i?'':' open'%><%-data[i].current?' on':''%><%-items.length>0?' has-child':''%>"
            data-id="<%-data[i].id%>">
            <a class="menu-inner" href="javascript:;"><i class="sort-icon"></i><i class="menu-icon"></i><%-data[i].name%></a>
            <div class="child-menu-wrap">
                <ul class="child-menu-list<%-items.length===5?' limit':''%>">
                    <% for (var j=0; j < items.length; j++) { %>
                    <li class="child-menu-item<%-items[j].current?' on':''%>" data-id="<%-items[j].id%>">
                        <a class="menu-inner" href="javascript:;"><i class="sort-icon"></i><%-items[j].name%></a>
                    </li>
                    <% } %>
                    <li class="child-menu-item menu-add">
                        <a class="menu-inner" href="javascript:;"><i class="plus-icon"></i></a>
                    </li>
                </ul>
            </div>
        </li>
        <% } %>
        <li class="menu-item menu-add<%-size===1?' on':''%>">
            <a class="menu-inner" href="javascript:;"><i class="plus-icon"></i><%=size > 1 ? '' : '<span>添加菜单</span>'%></a>
        </li>
    </ul>
</script>

<script type="text/template" id="menu_item_tpl">
    <li class="menu-item on open" data-id="<%-id%>">
        <a class="menu-inner" href="javascript:;"><i class="sort-icon"></i><i class="menu-icon"></i><%-name%></a>
        <div class="child-menu-wrap">
            <ul class="child-menu-list">
                <li class="child-menu-item menu-add">
                    <a class="menu-inner" href="javascript:;"><i class="plus-icon"></i></a>
                </li>
            </ul>
        </div>
    </li>
</script>

<script type="text/template" id="child_menu_item_tpl" data-id="<%-item.id%>">
    <li class="child-menu-item on" data-id="<%-id%>">
        <a class="menu-inner" href="javascript:;"><i class="sort-icon"></i><%-name%></a>
    </li>
</script>
<!--<label class="form-radio<%-_type===3?' selected':''%>" data-type="3">
                <i class="icon-radio"></i>
                <span class="align-m">转400客服</span>
            </label> -->
<script type="text/template" id="content_tpl">
    <div class="menu-title clearfix">
        <div class="pull-left title-text" data-id="<%-id%>"><%-name%></div>
        <a href="javascript:;" class="pull-right del-menu" data-id="<%-id%>" data-type="<%-isLeaf?'子':''%>菜单">删除<%-isLeaf?'子':''%>菜单</a>
    </div>
    <% if (hasChildren) {%>
    <p class="gray mt10">已添加子菜单，仅可设置菜单名称。</p>
    <% } %>
    <div class="form-group">
        <label for="name" class="pull-left mr10"><%-isLeaf?'子':''%>菜单名称</label>
        <div class="clearfix pull-overflow">
            <input type="text" class="form-control" id="name" name="name" data-maxlength="<%-isLeaf?16:8%>"
                   style="width: 300px;" value="<%-name%>" autocomplete="off">
            <p class="form-error empty hide">请输入<%-isLeaf?'子':''%>菜单名称</p>
            <p class="form-error limit hide">字数超过上限</p>
            <p class="gray"><%-isLeaf?'字数不超过8个汉字或16个字母':'字数不超过4个汉字或8个字母'%></p>
        </div>
    </div>
    <div class="form-group<%-hasChildren?' hide':''%>" id="content_wrap">
        <label for="content" class="pull-left mr10"><%-isLeaf?'子':''%>菜单内容</label>
        <div class="clearfix pull-overflow">
            <label class="form-radio<%-_type===0?' selected':''%>" data-type="0">
                <i class="icon-radio"></i>
                <span class="align-m">发送消息</span>
            </label>
            <label class="form-radio<%-_type===1?' selected':''%>" data-type="1">
                <i class="icon-radio"></i>
                <span class="align-m">跳转链接</span>
            </label>
            <label class="form-radio<%-_type===2?' selected':''%>" data-type="2">
                <i class="icon-radio"></i>
                <span class="align-m">转多客服</span>
            </label>
            
        </div>
        <div class="editor-wrap">
            <div class="js_editor_box<%-_type===0?'':' hide'%>"></div>
            <div class="js_editor_box<%-_type===1?'':' hide'%>">
                <div class="url-editor">
                    <p class="gray">订阅者点击该<%-isLeaf?'子':''%>菜单会跳到以下链接</p>
                    <div class="form-group">
                        <label for="page_url" class="pull-left mr10">页面地址</label>
                        <div class="clearfix pull-overflow">
                            <textarea class="form-control mb5 page-url" id="page_url" name="page_url"><%-_type===1 && content?content:''%></textarea>
                            <p class="gray hide" id="url_source"></p>
                            <a href="javascript:;" id="js_choose_url">从功能库中选择</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="js_editor_box<%-_type===2?'':' hide'%>"></div>
            <div class="js_editor_box<%-_type===3?'':' hide'%>">
                <input type="hidden" id="app_id" value="<?= $app_id ?>">
            </div>
            <div class="form-error hide"></div>
        </div>
    </div>
</script>
<script type="text/template" id="app_list_tpl">
    <div class="art-box-content">
        <div class="app-wrap clearfix">
            <?php
            $all_entry_list = $data['all_entry_list'];
            if (count($all_entry_list) > 0) {
                foreach ($all_entry_list as $entry) {
//                    if (!$hasMemberCenter && in_array($entry['name'], \Yii::$app->params['membercenter_func'])) {
//                        continue;
//                    }
                    ?>
                    <div class="app-item"
                         data-link="<?= $data['vsite_url'] .$entry['relative_url'] ?>">
                        <div class="icon-wrap">
                            <img src="<?= $entry['img_url'] ?>"/>
                        </div>
                        <div class="app-name"><?= $entry['name'] ?></div>
                        <div class="selected_mask">
                            <div class="selected_mask_inner"></div>
                            <div class="selected_mask_icon"></div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="empty">暂无可选项</div>
                <?php
            } ?>
        </div>
    </div>
    <div class="art-box-footer">
        <button type="button" class="btn btn-primary">确定</button>
        <button type="button" class="btn btn-secondary">取消</button>
    </div>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-23ee9dd3d5.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
    var __SCRIPT = [
        '/frontend/3rd/plupload/plupload.full.min.js'
    ];
    __REQUIRE('/modules/js/wechat/menu/menu.js?v=64ef6769cb');
</script>
<?php $this->endBlock() ?>
