var Cfg = {
    // html文件根目录
    htmlDir: '../front/games/520/',
    // js文件根目录，ps：该目录下必须有logic和lib这2个目录
    jsDir: '../front/',
    // js逻辑根目录名称
    logicJsDirName: '../front/games/520/js/',
    // css 文件根目录
    cssDir: '../front/games/520/css/',
    // inc文件根路径
    incDir: '../front/public/inc/',
    // 构建根目录，生成的js/css等文件都在此目录下
    buildDir: '../../../build'
};

var Config = {
    // 构建的js根目录：构建生成的js文件都在此目录下
    buildJsDir: Cfg.buildDir + '/js',
    // 构建的css根目录：构建生成的css文件都在此目录下
    buildCssDir: Cfg.buildDir + '/css',
    // 构建生成的map.js文件路径
    buildMapPath: Cfg.buildDir + '/map.js',
    // 构建生成的diff.js文件路径
    buildDiffPath: Cfg.buildDir + '/diff.js',
    // 构建生成的css.js文件路径
    buildCssPath: Cfg.buildDir + '/css.js'
}

for(var i in Cfg) {
    Config[i] = Cfg[i];
}


module.exports = Config;

