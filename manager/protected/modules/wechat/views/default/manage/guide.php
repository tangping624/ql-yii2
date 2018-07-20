<?php 
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/manage/wechat.min.css?v=2c80139ac6" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding mb40 border-bottom">
        <h4>添加公众号</h4>
    </div>

    <div class="padding form form-base">
        <div class="step-wrap step-two">
            <p class="step-1"><span>第一步：填写公众号基本信息</span></p>
            <p class="step-2"><span>第二步：在微信公众平台填入配置信息</span></p>
        </div>
        <div class="guide-wrap">
            <div class="copy-info">
                <div class="title">请按图示将接口信息填入微信公众平台<a class="wechat-link" href="https://mp.weixin.qq.com/" target="_blank">前往微信公众平台</a></div>
                <div class="content-wrap">
                	<div>
                		<div class="col-md-10"><label>接口API：</label><span><?=$data['wechat_api_url']?></span></div>
                        <div class="col-md-2">
                            <a href="javascript:;" class="btn-add copy-button" id="copy_api">复制</a>
                        </div>
                	</div>
                	<div>
                		<div class="col-md-10"><label>Token：</label><span><?=$data['token']?></span></div>
                        <div class="col-md-2">
                            <a href="javascript:;" class="btn-add copy-button" id="copy_token">复制</a>
                        </div>
                	</div>
                	<div>
                		<div class="col-md-10"><label>授权域名：</label><span><?=$data['wechat_domain']?></span></div>
                        <div class="col-md-2">
                            <a href="javascript:;" class="btn-add copy-button" id="copy_domain">复制</a>
                        </div>
                	</div>
                    <?php
                    $wechat_js_domain=$data['wechat_js_domain'];
                    $wechat_js_domain_count=count($wechat_js_domain);
                    for($i=0;$i<$wechat_js_domain_count;$i++){
                        if($wechat_js_domain[$i]=='')continue;
                        ?>
                        <div>
                            <div class="col-md-10"><label><?=$i==0?'JS域名：':''?></label><span><?=$wechat_js_domain[$i]?></span></div>
                            <div class="col-md-2">
                                <a href="javascript:;" class="btn-add copy-button" id="copy_domain">复制</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="guide-step">
                <div class="title">微信公众平台配置说明</div>
                <div class="content-wrap">
                    <div class="bg-line">
                        <div class="sub-title"><span class="step-icon">Step1</span>登录微信公众平台，进入开发者中心，启用服务器配置
                        </div>
                        <div class="img-box">
                        	<img src="/modules/images/global/step1.png" />
                        </div>
                    </div>
                    <div class="bg-line">
                        <div class="sub-title"><span class="step-icon">Step2</span>点击“修改配置”，填入上方提供的“接口API”与“Token”即可</div>
                        <div class="img-box">
                        	<img src="/modules/images/global/step2.png" />
                        </div>
                    </div>
                    <div class="bg-line">
                        <div class="sub-title"><span class="step-icon">Step3</span>如果是认证服务号，请将“授权域名”填入接口权限表的“网页授权获取用户基本信息”中</div>
                        <div class="img-box">
                        	<img src="/modules/images/global/step3.png" />
                        </div>
                    </div>
                    <div class="bg-line">
                        <div class="sub-title"><span class="step-icon">Step4</span>点击“公众号设置”下的“功能设置”，找到JS接口安全域名选择，点击设置修改</div>
                        <div class="img-box">
                        	<img src="/modules/images/global/step4.png" width="100%"/>
                        </div>
                    </div>
                    <div>
                        <div class="sub-title"><span class="step-icon">Step5</span>点击“添加功能插件”，选择“模板消息”，申请相应模板</div>
                        <div class="img-box">
                            <img src="/modules/images/global/step51.png" width="100%"/>
                        </div>
                        <div class="img-box" style="margin-top: 10px;">
                            <img src="/modules/images/global/step52.png" width="100%"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-bottom align-c">
            <button type="button" class="btn-pr ok-btn" id="prev_btn">上一步</button>
            <button type="button" class="btn-pr ok-btn" id="ok_btn">完成</button>
        </div>
    </div>
</div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/frontend/3rd/zeroclipboard/ZeroClipboard.js"></script>
    <script type="text/javascript">
        $(function(){
            seajs.use(['/frontend/js/lib/dialog','copy'],function(){
                $('.copy-button').copy({
                    beforeCopy:function(dom){
                        return $(dom).closest('div').prev().find('span').html();
                    },
                    onCopy:function(){
                        $.topTips({tip_text:'复制成功'});
                    }
                });
                /*ZeroClipboard.setMoviePath('/frontend/3rd/zeroclipboard/ZeroClipboard.swf');
                $('.copy-button').each(function(){
                    var $this = $(this);
                    var clip = new ZeroClipboard.Client();
                    clip.setHandCursor(true);
                    clip.addEventListener("mouseOver", function(){
                        clip.setText($this.closest('div').prev().find('span').html());
                    });
                    clip.addEventListener("mouseUp", function(){
                        $.topTips({tip_text:'复制成功'})
                    });
                    clip.glue($(this).attr('id'));
                });*/
                $('#ok_btn').on('click',function(){
                    window.location.href = O.path('/wechat/manage/list');
                });
                $('#prev_btn').on('click',function(){
                    window.location.href = O.path('/wechat/manage/add?id='+ O.getQueryStr('id'));
                });
            })
        })
    </script>
<?php $this->endBlock() ?>
