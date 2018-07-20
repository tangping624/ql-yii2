<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/7/7
 * Time: 15:31
 */
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>多客服</title>
    <style type="text/css">
        * { margin: 0; padding: 0; box-sizing: border-box;}
        li { list-style: none; }
        .wrapper { width: 100%; overflow-x: hidden; overflow-y: auto; font-size: 12px; font-family: "Microsoft YaHei", "Helvetica";}
        .wrapper .wrap { padding-top: 55px;}
        .wrapper .row-content { line-height: 32px;}
        .wrapper .row-content:hover { background-color: #E4E6E8;}
        .wrapper .row-content:after { content: ""; display: block; overflow: hidden; width: 0; height: 0; font-size: 0; clear: both;}
        .wrapper .label { width: 4em; margin: 0 1.5em; float: left;}
        .wrapper .value { float: left;}
        .wrapper .room-list { margin-left: 7em;}
        .wrapper .room-list ul { float: left; width: 100%;}
        .wrapper .room-list li { min-width: 340px; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
        .wrapper .tabs { position: fixed; width: 100%; z-index: 100; background: #fff;}
        .wrapper .tabs li { float: left; line-height: 30px; width: 90px; cursor: pointer; margin: 1em 0 1em 1.5em; position: relative; border-radius: 3px; text-align: center;}
        .wrapper .tabs li:hover,
        .wrapper .tabs li.on { background: #8c929a; color: #FFF;}
        .wrapper .row-content:after,
        .wrapper .checkbox-list:after,
        .wrapper .tabs:after { content: ""; display: block; overflow: hidden; width: 0; height: 0; font-size: 0; clear: both;}
        .wrapper .checkbox-list { padding-bottom: 70px;}
        .wrapper .checkbox-list .empty { line-height: 36px; text-align: center;}
        .wrapper .checkbox { width: 108px; margin:0 5px 5px 0; cursor: pointer; float: left; display: block; position: relative; line-height: 36px; padding: 0 0 0 1.5em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
        /*.wrapper .checkbox.selected { background-color: #44b549; color:#FFF;}*/
        .wrapper .checkbox:before { content: ""; background: url(/modules/images/icon/checkbox.png) no-repeat 0 0; display: inline-block; position: relative; width: 16px; height: 16px; top: 3px; margin-right: 5px;}
        .wrapper .checkbox.selected:before { background-position: -16px 0;}
        .wrapper .button-area { height: 60px; background-color: #efefef; position: fixed; bottom: 0; width: 100%; z-index: 100;}
        .wrapper .button-area .button { text-decoration: none; color:#fff; width: 90px; line-height: 30px; margin: 15px auto 0 auto; background-color: #05bd11; border-radius: 3px; display: block; text-align: center; position: relative;}
        .wrapper .button-area .button .status { position: absolute; left: 100px; display: block; white-space: nowrap; top: 0;}
        .wrapper .button-area .button .status.success { color: #05bd11;}
        .wrapper .button-area .button .status.error { color: #980000;}
        .wrapper .button-area .button.disabled { background: #dedede; color: #999; cursor: default;}
    </style>
</head>
<body>
<div class="wrapper">
    <ul class="tabs">
        <li class="on" data-type="base">会员基本信息</li>
        <li data-type="corp">受理公司设置</li>
    </ul>
    <div class="wrap member-info">
        <div class="row-content">
            <div class="label">粉丝昵称</div>
            <div class="value" data-id="nick_name"></div>
        </div>
        <div class="row-content">
            <div class="label">会员姓名</div>
            <div class="value" data-id="name"></div>
        </div> 
        <div class="row-content">
            <div class="label">会员性别</div>
            <div class="value" data-id="sex" data-empty="未知"></div>
        </div> 
        <div class="row-content">
            <div class="label">会员级别</div>
            <div class="value" data-id="member_level"></div>
        </div>
        <div class="row-content">
            <div class="label">联系电话</div>
            <div class="value" data-id="mobile"></div>
        </div>
        <div class="row-content">
            <div class="label">证件号码</div>
            <div class="value" data-id="id_code"></div>
        </div> 
    </div>
    <div class="wrap corp-setting" style="display: none;">
        <div class="corp-content">
            <ul class="checkbox-list"></ul>
        </div>
        <div class="button-area">
            <a href="javascript:;" class="button disabled" id="submit_btn">保存<span class="status" id="status"></span></a>
        </div>
    </div>
</div>
<script type="text/javascript" src="/frontend/js/lib/global.js"></script>
<script type="text/javascript">
    var request=function(url,data){
        return O.ajaxEx({
            url:url,
            type:'post',
            data:data||{}
        }).error(function(){
            //do nothing
        });
    },
    strToJson=function(str){
        return (new Function("return " + str))();
    },
    submitBtn=$('#submit_btn'),
    hasChange=false,
    changeStatus=function(change){
        hasChange=change;
        change?submitBtn.removeClass('disabled'):submitBtn.addClass('disabled');
    },
    ticket=O.getQueryStr('ticket'),
    account_id=O.getQueryStr('public_id'),
    workAccount=null,
    isSave=false;

    var Tools={
        bindEvent:function(){
            var $this=this;
            $('.tabs').off('click').on('click','li',function(){
                var item=$(this);
                if(!item.hasClass('on')){
                    item.addClass('on').siblings().removeClass('on');
                    $('.wrap').toggle();
                    $this.tabToggle(item.data('type'));
                }
            });

            var checkList=$('.checkbox-list').off('click');
            checkList.on('click','.checkbox-el',function(){
                var item=$(this),checkAll=checkList.find('.checkbox-all');
                item.toggleClass('selected');
                if(checkList.find('.checkbox-el').length==checkList.find('.checkbox-el.selected').length){
                    checkAll.addClass('selected');
                }else{
                    checkAll.removeClass('selected');
                }
                changeStatus(true);
            });
            checkList.on('click','.checkbox-all',function(){
                var item=$(this),
                    all=item.siblings();
                item.toggleClass('selected');
                item.hasClass('selected')?all.addClass('selected'):all.removeClass('selected');
                changeStatus(true);
            });

            $('#submit_btn').off('click').on('click',function(){
                if(hasChange){
                    changeStatus(false);
                    if(workAccount){
                        saveData({
                            worker:workAccount,
                            accountId:account_id,
                            corp:$this.getCorpList()
                        });
                    }else{
                        cacheWorkAccount(true);
                    }
                }
            });
        },
        tabToggle:function(type){
            if(type=='corp'){
                this.showCorp();
            }else{
                this.showMember();
            }
        },
        showMember:function(openId){
            var $this=this;
            if(openId){
                request(O.path('/wechat/dkf/get-member'),{
                    openId:openId,
                    accountId:account_id
                }).then(function(res){
                    if(res.result){
                        $this.renderMember(res.data);
                    }
                });
            }
        },
        showCorp:function(){
            var $this=this;
            if(!$this.isCorpRender){
                request(O.path('/wechat/dkf/corp-info'),{
                    worker:workAccount,
                    accountId:account_id
                }).then(function(res){
                    if(res.result){
                        $this.isCorpRender=true;
                        $this.renderCorp(res.data);
                    }
                });
            }
        },
        renderMember:function(data){
            if(!data)return;
            var valueArray=$('.value'),room_list=data['room_list']||[],html=[];
            if(room_list.length){
                for(var i=0;i<room_list.length;i++){
                    html.push('<li>'+room_list[i]['room_name']+'</li>');
                }
            }else{
                html.push('<li>无</li>');
            }
            valueArray.each(function(index){
                var el=valueArray.eq(index),
                    id=el.data('id');
                el.text(data[id]||el.data('empty')||'');
            });
            $('.room-list-wrap').html(html.join(''));
        },
        renderCorp:function(data){
            var html=[],
                selected=data.selected,
                company=data.company,
                selectedMap={};
            if(selected&&selected.length){
                for(var idx=0;idx<selected.length;idx++){
                    var corpId=selected[idx]['corp_id'];
                    selectedMap[corpId]=true;
                }
            }
            if(company&&company.length){
                html.push('<li class="checkbox checkbox-all'+(company.length==selected.length?' selected':'')+'">全部</li>');
                for(var i=0;i<company.length;i++){
                    html.push('<li class="checkbox checkbox-el'+(selectedMap[company[i]['id']]?' selected':'')+'" data-id="'+company[i]['id']+'">'+company[i]['name']+'</li>');
                }
            }else{
                html.push('<li class="empty">暂无数据</li>');
            }
            $('.checkbox-list').html(html.join(''));
        },
        getCorpList:function(){
            var selectedList=$('.checkbox-list').find('.checkbox-el.selected'),
                data=[];
            selectedList.each(function(index){
                data.push(selectedList.eq(index).data('id'));
            });
            return JSON.stringify(data);
        }
    };

    var event={
        OnUserChange:function(data){
            Tools.showMember(data['useraccount']);
        },
        OnMapMsgClick:function(EventData){
            //TODO
        }
    };

//    event['OnUserChange']({
//        useraccount:'oQ9GEuM17Rd4H5mXjUFvS2ghUZo0'
//    });

    Tools.bindEvent();

    function cacheWorkAccount(){
        isSave=false;
        $.getScript('http://crm1.dkf.qq.com/php/index.php/thirdapp/appdemo/get_workeraccount_by_sessionkey?callback=workerAccountCallback&ticket='+ticket);
    }

    function workerAccountCallback(data){
        workAccount=data['workeraccount'];
        if(isSave){
            isSave=false;
            saveData({
                worker:workAccount,
                accountId:account_id,
                corp:Tools.getCorpList()
            });
        }
    }

    cacheWorkAccount();

    function setStatus(res){
        var status=$('#status');
        if(res.result){
            status.text('保存成功');
            status.addClass('success');
        }else{
            status.text('保存失败');
            status.addClass('error');
        }
        setTimeout(function(){
            status.hide();
        },3000);
    }

    function saveData(data){
        var status=$('#status').text('').attr('class','status').show();
        if(!data.worker||data.worker.indexOf('@')==-1){
            status.text('客服账号获取失败');
            status.addClass('error');
            return;
        }
        request(O.path('/wechat/dkf/set-corp'),data).then(setStatus);
    }

    function MCS_ClientNotify(eventData) {
        var data=strToJson(eventData);
        event[data['event']](data);
    }
</script>
</body>
</html>