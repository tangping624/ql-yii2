//模板
define(function(require, exports, module) {               
    var cache = {}, keyWords = {}, global = window,
        keys = ('break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if'
            + ',in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with'
            + ',abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto'
            + ',implements,import,int,interface,long,native,package,private,protected,public,short'
            + ',static,super,synchronized,throws,transient,volatile,arguments,let,yield').split(',');

    (function(keys) {
        var len = keys.length;
        while(len--) {
            //keyWords = {'yield': true, 'let': true, 'arguments': true......}
            keyWords[keys[len]] = true;
        }
    })(keys);

    //是否是关键字
    var isKeyWords = function(val) {
        return keyWords[val];
    };

    var $ = function(id) {
        return document.getElementById(id);
    };

    // 过滤XSS
    var xss = function(str) {
        var div = document.createElement("div"),
            text = document.createTextNode(str), val = '';

        div.appendChild(text);
        val = div.innerHTML;
        text = null; div = null;

        return val;
    };

    //处理单/双引号互嵌的问题
    var handleStr = function(str) {
        //单引号替换为html的表示
        return str.replace(/'/g, '&apos;')
        //只将<% xxx %>外的单引号替换为html的表示
        .replace(/<%(.*?)%>/g, function(match) {
            return match.replace(/&apos;/g, "'");
        });
    };

    var template = function(str, data, context) {
        // 上下文环境
        context = context || global;

        // 如果传入的str是id
        if (/^[\w\-_]+$/.test(str)) {
            str = $(str).innerHTML;
        }
        
        str = str
            //单行和多行注释 PS: 有http://这样的
            .replace(/\/\*[\s\S]*?\*\/|[^:]\/\/.*?[\n\r\t]/g, "") 
            //去回车、换行、制表      
            .replace(/[\r\t\n]/g, " ");

        // 缓存
        if(cache[str]) {
            return data ? cache[str].call(context, data) : cache[str];
        }

        // 提取变量名
        var declare = 'var ';
        for(var i in data) {
            if(!isKeyWords(i)) {
                declare += (i + '= __data.' + i + ','); 
            }
        }

        var tempStr = handleStr(str);

        var fn = cache[str] = new Function("__data",
            declare + "__s='', __xss=" + xss + ";" +
            "__s += '" + tempStr
              .replace(/(%>)[\s]*(<%)/g, "$1$2")
              .replace(/<%=(.*?)%>/g, "' + ($1) + '")
              .replace(/<%-(.*?)%>/g, "' + (__xss($1)) + '")
              .replace(/<%/g, "'\n")
              .replace(/%>/g, "\n__s += '")
            + "';return __s;");

        return data ? fn.call(context, data) : fn;
        
    };

    module.exports = global.Template = template;
});