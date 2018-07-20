define(function(require, exports, module) {
    window.Template = require('../lib/template');
    
    jQuery.fn.multilquery=function(options){
        var $this= $(this),
            template = $('#'+options.templateid).html(),
            allowSearch = options.allowSearch || false,
            url = options.url,
            onleafclick = options.onleafclick || null,
            defaultData = options.defaultData || null,
            cacheData = {};
    
        var loadData = function(id,callback){
            O.ajaxEx({
                url: O.path(url+'?parent_id=' + id),
                type: 'post',
                async: false,
                success: function(json) {
                    var data={
                        ques:[]
                    }
                    data.ques = json.data;
                    callback&&callback(data);
                },
                error: errorCallback
            })
        }
        
        var loadFirstLevel = function(){
            loadData('null',function(data){
                data.level = 1;
                data.allowSearch = allowSearch;
                $this.find('.querylist').html(Template(template,data));
                cacheData['level1']=data;
            })
        }
        
        var loadQuesById = function(id,level,callback){
            loadData(id,function(data){
                var ques;
                if(data&&data.ques.length>0){
                    data.level = level;
                    data.allowSearch = allowSearch;
                    ques = Template(template,data);
                    cacheData['level'+level]=data;
                }
                
                var items = $this.find('.queryitem');
                for(var i=level-1;i<items.length;i++){
                    items.eq(i).hide().remove();
                }

                if(data&&data.ques.length>0){
                    $this.find('.querylist').append($(ques));
                }
                
                callback&&callback(data);
            })
        }
        
        var loadDefaultLevel = function(){
            var multiData = defaultData.data;
            var data={ques:[]},ques;
            for(var i=0; i<multiData.length; i++){
                var level = i+1;
                data.ques = multiData[i];
                data.level = level;
                data.allowSearch = allowSearch;
                ques = Template(template,data);
                cacheData['level'+level]=data;
                
                if(i==0){
                    $this.find('.querylist').html(ques);
                }else{
                    $this.find('.querylist').append($(ques));
                }  
            }
            
            var select_data = defaultData.select_data;
            var queryitems = $this.find('.querylist .queryitem');
            for(var j=0; j<select_data.length; j++){
                queryitems.eq(j).find('li[id="'+select_data[j]+'"]').addClass('on');
            }
            
            var leafIndex = queryitems.eq(queryitems.length-1).find('ul li').index(queryitems.eq(queryitems.length-1).find('li[class="on"]'));
            $this.find('.querylevel-wrap').html(((multiData[multiData.length-1][leafIndex]||{})['full_name']||"").replace(/-/g,'>'));
            
            if(onleafclick){
                onleafclick(select_data[select_data.length-1]);
            }
        }
        
        var bindEvent = function(){
            $this.on('click','li',function(){
                var id = $(this).attr('id');
                var currlevel = $(this).closest('.queryitem').attr('level');
                var level = +currlevel +1;

                /*var levels = $this.find('.querylevelitem');
                if(levels.length>0){
                    for(var i=currlevel-1; i<levels.length; i++){
                        levels.eq(i).hide().remove();
                    }
                }
                
                if($this.find('.querylevel-wrap .level-'+currlevel)[0]){
                    if(currlevel ==1){
                        $this.find('.querylevel-wrap .level-'+currlevel).eq(0).html($(this).html());
                    }else{
                        $this.find('.querylevel-wrap .level-'+currlevel).eq(0).html(' > '+$(this).html());
                    }
                }else{
                    if(currlevel == 1){
                        $this.find('.querylevel-wrap').append('<span class="querylevelitem level-'+currlevel+'">'+$(this).html()+'</span>');
                    }else{
                        $this.find('.querylevel-wrap').append('<span class="querylevelitem level-'+currlevel+'"> > '+$(this).html()+'</span>');
                    }
                }*/
                
                $(this).closest('.queryitem').find('li').removeClass('on');
                $(this).addClass('on');
                $this.find('.querylevel-wrap').html($(this).attr('fullname').replace(/-/g,'>'));
                loadQuesById(id,level,function(data){
                    if(data&&data.ques.length==0){
                        onleafclick&&onleafclick(id);
                    }
                });
            })
            
            $this.on('keyup','.s-input',function(){
                var level = $(this).closest('.queryitem').attr('level');
                var data = cacheData['level'+level];
                var keyword = $.trim($(this).val());
                if(data){
                    var filterData={ques:[]};
                    var lihtml = '';
                    for(var i=0;i<data.ques.length;i++){
                        var q = data.ques[i];
                        if(q.name.indexOf(keyword)!=-1){
                           filterData.ques.push(q);
                           lihtml+='<li id="'+q.id+'">'+q.name+'</li>'; 
                        } 
                    }
                    
                    $this.find('.queryitem-level-'+level+' ul').html(lihtml);
                    
                    var items = $this.find('.queryitem');
                    for(var i=level;i<items.length;i++){
                        items.eq(i).hide().remove();
                    }
                }
            })
        }
        
        var errorCallback = function() {
            $.topTips({mode:'warning',tip_text:'出现异常'});
        }
        
        var init = function(){
            if(defaultData){
                loadDefaultLevel();
            }else{
                loadFirstLevel();
            }
            bindEvent();
        }
        init();
    }
})