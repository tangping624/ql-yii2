// 修复jquery兼容问题
define(function (require, exports, module) {

    if( !$.browser && O.ie ) {
        // 该方法在overall.js中
        var ie = O.ie();

        $.browser = {
            msie: ie,
            version: ie
        }
    }
    
});