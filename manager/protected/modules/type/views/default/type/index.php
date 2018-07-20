<?php 
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link href="/modules/css/global/public.css" rel="stylesheet">
<link href="/modules/css/page/page.css" rel="stylesheet">
<link href="/modules/css/global.css" rel="stylesheet">  
<style> 
    .manage-container{
        margin-top:36px;
    }
    .btn{
        text-align:center;
        width:100px;
        padding:10px 0;
        margin-left:157px; 
        background-color:#fff;
        color:#000;
        border:1px solid #e7e6eb;
    }
    .title{
        width:300px;
        text-align:center;
    }
</style>
<?php $this->endBlock() ?>

    <div class="manage-content">
        <div class="manage-main">
            <div class="public-panel organization-framework">
                <div class="pp-side">
                    <h4 class="public-tit">
                        分类设置
                    </h4>
                    <div class="js-tree">
                        
                    </div>
                </div>
                <div class="pp-main">
                    <div class="public-tit"></div>

                    <div class="grid" id="user_grid">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/template" id="grid_template">
        <table class="table table-hover" >
            <thead>
                <tr><th style="font-size:16px;font-weight:bold;padding-left:80px;" colspan="2">类型详情</th></tr>
            </thead>
            <tbody>
              <% if(list.orderby){%>
                <tr>
                    <td class="title">
                    <span style="padding-right:27px;">序号</span>
                       
                    </td>
                    <td>
                        <%- list.orderby %>
                    </td>
                </tr>
               <% } %> 
                <tr>
                    <td class="title">类型名称</td>
                    <td><%- list.name %></td>
                </tr>
                <tr>
                    <td class="title">类型图标</td>
                    <td><img src="<%- list.icon %>" onerror="javascript:this.src='/modules/images/no.png'" style="width:60px;"></td>
                </tr>
                <% if(list.is_display=='0'||list.is_display=='1'){%>
                <tr>
                <td class="title">是否显示</td>
                    <td  class="js-edit-box edit-box"  data-id="<%- list.id %>">
                        <div class="td-pa">
                            <p><label class="iosCheck green"><input type="checkbox" <%if (list.is_display == '1'){ %> checked="checked"; <% } %> class="js-ioscheck"><i></i></label></p>
                        </div>
                    </td>
                </tr>
                <% } %> 
            </tbody>
        </table>
    </script>
<?php $this->beginBlock('js') ?>
    <script>
        __REQUIRE('/modules/js/type/type/index');
    </script>
<?php $this->endBlock() ?>