<?php 
namespace app\modules\home\repositories;
use app\entities\advert\AAdvert;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use yii\helpers\VarDumper;
use app\framework\utils\DateTimeHelper;
class AdvertRepository extends RepositoryBase{

    //获取广告
    public function getAdvert($appcode)
    {
        $param[':appcode']=$appcode;
        $sql="SELECT a.id,a.title,c.original_url,c.thumb_url,c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid=b.id INNER JOIN a_images c ON c.fid=a.id  WHERE b.app_code=:appcode  ORDER BY b.grouporder asc";
        return AAdvert::getDb()->createCommand($sql, $param)->queryAll();
    }

    //获取首页幻灯片广告
    public function getHomeAdvert($appcode)
    {
        $param[':appcode']=$appcode;
        $sql="SELECT a.id,a.title,c.original_url,c.thumb_url,c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid=b.id INNER JOIN a_images c ON c.fid=a.id  WHERE b.app_code=:appcode and grouporder=1 ORDER BY b.grouporder asc";
        return AAdvert::getDb()->createCommand($sql, $param)->queryAll();
    }

    //获取首页其它广告
    public function getOtherHomeAdvert($appcode)
    {
        $param[':appcode']=$appcode;
        //$sql="( SELECT a.id, b.grouporder, c.original_url, c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid = b.id INNER JOIN a_images c ON c.fid = a.id WHERE b.app_code =:appcode AND grouporder = 2 LIMIT 1 ) UNION ALL ( SELECT a.id, b.grouporder, c.original_url, c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid = b.id INNER JOIN a_images c ON c.fid = a.id WHERE b.app_code =:appcode AND grouporder = 3 LIMIT 1 ) UNION ALL ( SELECT a.id, b.grouporder, c.original_url, c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid = b.id INNER JOIN a_images c ON c.fid = a.id WHERE b.app_code = :appcode AND grouporder = 4 LIMIT 1 ) UNION ALL ( SELECT a.id, b.grouporder, c.original_url, c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid = b.id INNER JOIN a_images c ON c.fid = a.id WHERE b.app_code = :appcode AND grouporder = 5 LIMIT 1 ) UNION ALL ( SELECT a.id, b.grouporder, c.original_url, c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid = b.id INNER JOIN a_images c ON c.fid = a.id WHERE b.app_code = :appcode AND grouporder = 6 LIMIT 1 ) UNION ALL ( SELECT a.id, b.grouporder, c.original_url, c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid = b.id INNER JOIN a_images c ON c.fid = a.id WHERE b.app_code = :appcode AND grouporder = 7 LIMIT 1 )";
        $sql = "select b.id, a.grouporder, c.original_url, c.link_url from a_adsense a
                INNER JOIN a_advert b ON a.id = b.adsenseid
                INNER JOIN a_images c ON b.id = c.fid
                where a.app_code = :appcode and a.grouporder BETWEEN 2 and 7;
                GROUP BY a.grouporder";
        return AAdvert::getDb()->createCommand($sql, $param)->queryAll();
    }
}
