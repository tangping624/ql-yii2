<div class="page-menu flex" >
<!--    --><?php //var_dump($menu["navigation_list"]);exit;?>
    <?php
    use app\framework\utils\WebUtility;

    foreach ($menu["navigation_list"] as $nav) {
        $url = $nav['href'];
        $url = WebUtility::unsetParam("public_id", $url); 
        $img_url = $nav['img_url'];
        $highlight_img_url = $nav['highlight_img_url'];
        $is_cur = strpos($url, $menu['menu']) !== false;
        $img_url = $is_cur && $highlight_img_url ? $highlight_img_url : $img_url;
        $name = $nav['name'];
        $font_color =  $nav['font_color']? $nav['font_color']:"#e6e6e6";
         $highlight_font_color = $nav['highlight_font_color'];
         $color = $is_cur && $highlight_font_color ? $highlight_font_color : $font_color; 
        ?>
        <div class="align-c flex1 menu">
            <a href="<?= WebUtility::buildQueryUrl($url, ['public_id'=>$menu['public_id'],'num'=>  rand()]) ?>" target="_self" style="padding: 0.3rem 0;">
                <span class="menu-icon menu-me-icon" style="background:url(<?= $img_url ?>) no-repeat center;background-size: 1.11rem auto;width: 100%;height: 1.2rem;"></span><br/>
                <span style="margin-top:.2rem;font-size:0.67rem;margin-top: .2rem;color:<?=$color ?>"><?= $name ?></span>
            </a>
        </div>
        <?php
    }
    ?>
</div>