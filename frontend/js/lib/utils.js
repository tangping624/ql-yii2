/**
 * Created by weizs on 2015/5/18.
 */
'use strict';
/*global plupload*/
define(function (require, exports, module) {

    var preventScroll = function(dom){
        if(dom.jquery){
            dom = dom.get(0);
        }
        if(navigator.userAgent.indexOf('Firefox') >= 0){   //firefox
            dom.addEventListener('DOMMouseScroll',function(e){
                dom.scrollTop += e.detail > 0 ? 60 : -60;
                e.preventDefault();
            },false);
        }else{
            dom.onmousewheel = function(e){
                e = e || window.event;
                dom.scrollTop += e.wheelDelta > 0 ? -60 : 60;
                return false;
            };
        }
    };


    $.fn.extend({
        preventScroll:function(){
            return this.each(function(){
                preventScroll(this);
            });
        }
    });

    var utils={
        isURL:function(url){
            return /^((http|ftp|https):\/\/)(([a-zA-Z0-9._-]+.[a-zA-Z]{2,6})|([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}))(:[0-9]{1,4})*(\/[a-zA-Z0-9&%_.\/-~-#!]*)?.*$/.test(url);
        },
        replacePhoneNumber:function(string){
            if(!string){
                return '';
            }
            return string
                .replace(/&nbsp;/g,' ')
                .replace(/<A href="tel:\/\/.*?">.*?<\/A>|<A href='tel:\/\/.*?'>.*?<\/A>/ig,function(v){return $(v).text();})
                .replace(/((400|800)(\d{7}))|((400|800) (\d{7}|(\d{3} \d{4})))|((400|800)-(\d{7}|(\d{3}-\d{4})))|((\d{4}|\d{3})( |-)(\d{8}|\d{7}))|(1[34578]\d{9})|(1[34578]\d{1} \d{4} \d{4})|(1[34578]\d{1}-\d{4}-\d{4})/g,function(val){
                    //屏蔽属性替换
                    var array = Array.prototype.slice.apply(arguments),
                        len = array.length,
                        before = array[len-1].substring(array[len-2]+array[0].length),
                        nodeOpen = before.indexOf('<'),nodeClose = before.indexOf('>'),aOpen = before.indexOf('<a'),aClose = before.indexOf('</a');

                    if(nodeOpen>nodeClose){
                        return val;
                    }
                    if(aClose!==-1){
                        if(aOpen!==-1){
                            if(aOpen>aClose){
                                return val;
                            }
                        }else{
                            return val;
                        }
                    }
                    return '<a href="tel://'+val+'" title="'+val+'">'+val+'</a>';
                });
        },
        getPosition:function(dom){
            var pos=-1;
            if(dom.selectionStart||dom.selectionStart===0){//非IE浏览器
                pos= dom.selectionStart;
            }else{//IE
                var range = document.selection.createRange();
                range.moveStart('character',-dom.value.length);
                pos=range.text.length;
            }
            return pos;
        },
        focus:function(dom){
            var $dom=dom.jquery?dom:$(dom);
            $dom.focus();
            $dom.val($dom.val());
        },
        image:function(url,width,height,q){
             return url;
            //return typeof(url) === 'string' && url.substr(-4) === '.gif' ? url : [url,'@',width||'',width&&'w_',height||'',height&&'h_',q||90,'Q.png'].join('');
        },
        upload:function(options){
            if(!options){
                return null;
            }
            var defaultSetting={
                runtimes: 'html5,flash,silverlight,html4',
                flash_swf_url: '/frontend/3rd/plupload/Moxie.swf',
                silverlight_xap_url: '/frontend/3rd/plupload/Moxie.xap'
            };
            var setting={};
            $.extend(true,setting,defaultSetting,options,{
                init:{
                    Error: function (up, err) {
                        if(err.code===-200){
                            location.reload();
                        }
                        if(options.init&&options.init.Error){
                            options.init.Error.apply(this,arguments);
                        }
                    }
                }
            });
            return new plupload.Uploader(setting);
        },
        download:function(url,callback){
            var download=$('#iframe_download_element');
            if(download.length){
                download.attr('src',url);
            }else{
                download=$('<iframe>').attr({
                    id:'iframe_download_element',
                    src:url
                }).css({
                    width:0,
                    height:0,
                    overflow:'hidden'
                }).appendTo($('body'));
            }
            if(callback){
                download.load(callback);
            }
        },
        commafy : function(num) {
            num = num + '';
            num = num.replace(/[ ]/g, ''); //去除空格
            if (num === '') {
                return;
            }
            if (isNaN(num)){
                return;
            }
            var index = num.indexOf('.');
            var reg = /(-?\d+)(\d{3})/;
            if (index===-1) {//无小数点
                while (reg.test(num)) {
                    num = num.replace(reg, '$1,$2');
                }
            } else {
                var intPart = num.substring(0, index);
                var pointPart = num.substring(index + 1, num.length);
                while (reg.test(intPart)) {
                    intPart = intPart.replace(reg, '$1,$2');
                }
                num = intPart +'.'+ pointPart;
            }
            return num;
        },
        preventScroll : preventScroll
    };

    //同时绑定到jQuery
    $.extend({
        utils:utils
    });

    return utils;
});
