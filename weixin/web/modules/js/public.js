var pub=(function () {
	return {
		Geturl:function(name){  
		    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");  
		   	var r = window.location.search.substr(1).match(reg);
		    if (r!==null) return unescape(r[2]); return null;  
		},
		deCode: function (str) {
			return decodeURIComponent(str);
		},
		enCode: function (str){
			return encodeURIComponent(str);
		},
		filterLabel:function(selector1,selector2){//过滤p标签
			var strs=$(selector1);
			var arr=[];
            for(var t=0;t<strs.length;t++){
                var str=strs[t].innerText;
                var reTag = /<img(?:.|\s)*?>/g;
                var c = str.replace(reTag,'');
                var dd=c.replace(/<\/?.+?>/g,"");
                var dds=dd.replace(/ /g,"");
                arr.push(dds);
            }
            $(selector2).each(function(i,v){
            	$(v).html(arr[i]);
            });
		},
	};
})();
	//输入字数显示
function setShowLength(obj, maxlength, id){	 
	var rem =obj.value.length; 
	var wid = id; 
	if (rem > maxlength){ 
      	rem = maxlength; 	
	}	 
	document.getElementById(wid).innerHTML = rem + "/"+maxlength; 
}
    // 限制输入正整数和小数点
function key_Num(ob) {
	ob.value=ob.value.replace(/[^\d.]/g,'');
}
	//限制输入数字和小数点
function keyNum_(ob) {
	ob.value=ob.value.replace(/[^\d.-]/g,'');
}
	//只能输入数字
function keyNum(ob) {
	ob.value=ob.value.replace(/[^\d]/g,'');
}
