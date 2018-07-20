<?php 
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css"/>
<link rel="stylesheet" href="/modules/css/system/account/wechat.css" />

<style type="text/css">  
 .media_cover{width: 40%;margin-right: 2%;border:2px dotted #d9dadc;height:195px;text-align: center;}
 .media_cover .add_wrap{margin-top:65px;display:block;text-decoration: none;}
 .media_cover .add_wrap p{color:#d9d9d9;}
 .media_add{background: url("/frontend/js/widgets/weixinEdition/images/base_z.png") 0 -2839px no-repeat;width: 36px; height: 36px;vertical-align: middle;display: inline-block;overflow: hidden;}
 
.imgup-wrap{padding-bottom: 20px;}
.imgup-wrap .title{margin-bottom:3px;}
.img-wrap{width: 100%;margin-right: 2%;border:2px dotted #d9dadc;height:195px;text-align: center;}
.img-wrap p{text-align: center;margin-top:20px;font-size:12px;color:#B2B2B2;}
.img-wrap .per{background-color:#ECECEC;width:100px;height:10px; margin: 10px auto;}
.img-wrap .pct{float:left;width:10%;background-color: green;height:100%;}
/*.first-box{margin-left:0;}*/
.img-wrap .img-box{height:calc(100% - 30px);border:1px solid #E7E7EB;background-color: white;}
.img-wrap .img-box img{width:100%;height:100%;}
.img-wrap .opeate{border:1px solid #E7E7EB;border-top:0;border-right:0;height:30px;}
.img-wrap .opeate > span{float:left;height:100%;width:50%;border-right:1px solid #E7E7EB;cursor: pointer;}
.img-wrap .opeate > span:hover{background-color: #ECECEC}
.img-wrap .icon{display:inline-block;margin: 6px 0 0 35px;height: 17px;}
.img-wrap .f-btn .icon{background-position: -211px 0; width: 16px;}
.img-wrap .d-btn .icon{background-position: -315px 0; width: 15px;} 
</style> 
<?php $this->endBlock() ?>

<div class="manage-content">
     <div class="padding">
                <h4 class="manage-title">公众号配置</h4>   
            </div> 
    <div class="padding form" id="config_form">
        <div class="panel panel-default panel-reset">
            <div class="panel-heading">基础信息
                <a href="/system/account/guide">公众号配制指引</a>
            </div>
            <table class="table"> 
                <tr>
                    <td width="120px">公众号名称</td>
                    <td>
                        <span class="text"><?=$data['name']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['name']?>" data-value="<?=$data['name']?>" data-column="name">
                        </div>
                    </td>
                    <td width="120px"><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>原始ID</td>
                    <td>
                        <span class="text"><?=$data['original_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['original_id']?>" data-value="<?=$data['original_id']?>" data-column="original_id">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>微信号</td>
                    <td>
                        <span class="text"><?=$data['wechat_number']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['wechat_number']?>" data-value="<?=$data['wechat_number']?>" data-column="wechat_number">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>微信号类型</td>
                    <td>
                        <span class="text"><?=$data['type']?></span>
                        <div class="edit hide">
                            <select class="form-control form-select-control" data-column="type">
                                <option value="服务号" <?php if($data['type']=='服务号'){ ?>selected<?php } ?>>服务号</option>
                            </select>
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>AppID</td>
                    <td>
                        <span class="text"><?=$data['app_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['app_id']?>" data-value="<?=$data['app_id']?>" data-column="app_id">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>AppSecret</td>
                    <td>
                        <span class="text"><?=$data['app_secret']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['app_secret']?>" data-value="<?=$data['app_secret']?>" data-column="app_secret">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <!-- <tr>
                    <td>商户号</td>
                    <td>
                        <span class="text"><?=$data['mch_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['mch_id']?>" data-value="<?=$data['mch_id']?>" data-column="mch_id">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>  
                <tr>
                    <td>商户密钥</td>
                    <td>
                        <span class="text"><?=$data['mch_key']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['mch_key']?>" data-value="<?=$data['mch_key']?>" data-column="mch_key">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr> -->
                  <tr>
                    <td>关注URL</td>
                    <td>
                        <span class="text"><?=$data['attention_url']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['attention_url']?>" data-value="<?=$data['attention_url']?>" data-column="attention_url">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
               <!--  <tr>
                   <td>Api证书</td>
                   <td>
                       <span class="text1"><?=$data['mch_ssl_cert']?'已导入':'未导入'?></span>
                   </td>
                   <td><a href="javascript:;" class="edit-btn" id="import_cert">导入</a></td>
               </tr>
               <tr>
                   <td>Api密钥</td>
                   <td>
                       <span class="text1"><?=$data['mch_ssl_key']?'已导入':'未导入'?></span>
                   </td>
                   <td><a href="javascript:;" class="edit-btn" id="import_key">导入</a></td>
               </tr>  -->
               <!-- <tr>
                    <td>公众号二维码</td>
                    <td>    
                         <div class="media_cover" id="qrcode_urlconver">  
                        <?php /*if(!empty($data['qrcode_url'])) {*/?>
                            <div class="img-wrap">
                                <div class="img-box">
                                    <img src="<?/*=$data['qrcode_url']*/?>" class="js-img" _src="<?/*=$data['qrcode_url']*/?>">
                                </div>
                                <div class="opeate">
                                    <span class="opt-btn f-btn"> 
                                    </span>
                                    <span class="opt-btn d-btn opt-qrcode">
                                        <span class="icon-merge icon"></span>
                                    </span>
                                </div>
                            </div> 
                             <a href="javascript:;" id="qrcode_url" class="add_wrap add-media" style="position: relative; z-index: 1;display: none;">
                                <span class="media_add"></span>
                                <p class="color-gray add-media-tips">上传图片</p>
                            </a> 
                        <?php /*}else{ */?>
                            <a href="javascript:;" id="qrcode_url" class="add_wrap add-media" style="position: relative; z-index: 1;">
                                <span class="media_add"></span>
                                <p class="color-gray add-media-tips">上传图片</p>
                            </a> 
                        <?php /*}*/?>
                             <div class="msg_processbar"> <span class="upload-processbar-width-wrap"><span class="upload-processbar-width"></span></span></div>
                        </div> 
                    </td>
                    <td><span class="help-block">只支持JPG图片</span> </td>
                </tr>-->
               <!-- <tr>
                    <td>公众号头像</td>
                     <td> 
                         <div class="media_cover" id="headimg_urlconver">  
                              <?php /*if(!empty($data['headimg_url'])) {*/?>
                            <div class="img-wrap">
                                <div class="img-box">
                                    <img src="<?/*=$data['headimg_url']*/?>" class="js-img" _src="<?/*=$data['headimg_url']*/?>">
                                </div>
                                <div class="opeate">
                                    <span class="opt-btn f-btn"> 
                                    </span>
                                    <span class="opt-btn d-btn  opt-head">
                                        <span class="icon-merge icon"></span>
                                    </span>
                                </div>
                            </div> 
                             <a href="javascript:;" id="headimg_url" class="add_wrap add-media" style="position: relative; z-index: 1;display: none;">
                                <span class="media_add"></span>
                                <p class="color-gray add-media-tips">上传图片</p>
                            </a>
                        <?php /*}else{ */?>
                            <a href="javascript:;" id="headimg_url" class="add_wrap add-media" style="position: relative; z-index: 1;">
                                <span class="media_add"></span>
                                <p class="color-gray add-media-tips">上传图片</p>
                            </a>
                            
                           <?php /*} */?>
                              <div class="msg_processbar"> <span class="upload-processbar-width-wrap"><span class="upload-processbar-width"></span></span></div>
                        </div>
                     </td>
                    <td><span class="help-block">只支持JPG图片</span></td>
                </tr>-->
            </table>
        </div>
         
        <div class="panel panel-default panel-reset">
            <div class="panel-heading">接口配置<a class="wechat-link float-right" href="https://mp.weixin.qq.com/" target="_blank">进入微信公众平台配置</a></div>
            <table class="table">
                <tr>
                    <td width="100">接口API</td>
                    <td class="copytext"><?=$data['wechat_api_url']?></td>
                    <td width="60"><a href="javascript:;" class="copy-button" id="copy_api">复制</a></td>
                </tr>
                <tr>
                    <td>Token</td>
                    <td class="copytext"><?=$data['token']?></td>
                    <td><a href="javascript:;" class="copy-button" id="copy_token">复制</a></td>
                </tr>
                <tr>
                    <td>授权域名</td>
                    <td class="copytext"><?=$data['wechat_domain']?></td>
                    <td><a href="javascript:;" class="copy-button" id="copy_domain">复制</a></td>
                </tr> 
                    <tr>
                        <td>JS域名</td>
                        <td class="copytext"><?=$data['wechat_js_domain']?></td>
                        <td><a href="javascript:;" class="copy-button" id="copy_domain">复制</a></td>
                    </tr> 
            </table>
        </div>
    </div>
</div>
 <!-- 模板 -->
<!-- 上传图片的模版 -->
<script type="text/template" id="img_templ">
    <div class="img-wrap" id="<%-id%>">
        <div class="img-box">
            <p class="wait">等待中...</p>
            <div class="per"><span class="pct"></span></div>
        </div>
        <div class="opeate"> 
             <span class="opt-btn f-btn" > 
            </span>
            <span class="opt-btn d-btn opt-qrcode" >
                <span class="icon-merge icon"></span>
            </span>
        </div>
    </div>
</script>
<!-- 上传图片的模版 -->
<script type="text/template" id="img_templhead">
    <div class="img-wrap img-header" id="<%-id%>" >
        <div class="img-box">
            <p class="wait">等待中...</p>
            <div class="per"><span class="pct"></span></div>
        </div>
        <div class="opeate"> 
             <span class="opt-btn f-btn" > 
            </span>
            <span class="opt-btn d-btn opt-head" >
                <span class="icon-merge icon"></span>
            </span>
        </div>
    </div>
</script>
 <script type="text/template" id="de_templ">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr bg-green color-white delete-btn">确定</button>
        <button class="btn-pr bg-white fr cancel-btn">取消</button>
    </div> 
</script>
 <script type="text/template" id="de_templhead">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr bg-green color-white delete-btnhead">确定</button>
        <button class="btn-pr bg-white fr cancel-btn">取消</button>
    </div> 
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/modules/js/system/account/upload.js"></script>
<script type="text/javascript" src="/modules/js/system/account/upload-key.js"></script>

<script type="text/javascript" src="/modules/js/system/account/upload-headimgurl.js"></script>
<script type="text/javascript" src="/modules/js/system/account/upload-qrcodeurl.js"></script> 
<script type="text/javascript"> 
        seajs.use('/modules/js/system/account/config.js', function (index) {
//            index.init();
        }); 
    </script> 
<?php $this->endBlock() ?>