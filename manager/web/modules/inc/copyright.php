<div class="copyright align-c" id="page_footer">
    <a href="http://www.goodsky2012.com" target="_self">Powered by 天宣科技</a>
</div>
<script type="text/javascript">
    //解决zepto计算高度差异的问题       
    $(function() {    
        function setCopyright(options) {
            var $body = $('body');

            options = $.extend({
                contentId: $body.attr('data-contentId') || 'page_content',
                footerId: $body.attr('data-footerId') || 'page_footer',
                offset: parseInt($body.attr('data-offset'), 10) || 0,
                isResize: $body.attr('data-resize') || false
            }, options);

            var $conetnt = $('#' + options.contentId),
                $footer = $('#' + options.footerId);

            var height = function() {
                var originH = $conetnt.height(),
                    footerH = $footer.height(),
                    winH = $(window).height();
                if(/*originH <= 0 || */footerH <= 0 || winH <= 0) {
                    return false;
                } else {
                    return winH - footerH - options.offset;
                }
            };

            var setMinHeight = function() {
                var minH = height();
                
                if(minH === false || minH <= 0){
                    setTimeout(setMinHeight, 100);
                }else{
                    $footer.css({'opacity':'1','visibility':'visible'});//防止显示跳动
                    $conetnt.css({'minHeight':minH});
                }                    
            };

            setTimeout(function() {
                setMinHeight()
            }, 100);

            options.isResize && $(window).resize(function(){
                setMinHeight()
            }); 
        }

        setCopyright();
    })
</script>