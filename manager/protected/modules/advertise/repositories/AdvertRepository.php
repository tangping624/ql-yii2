<?php 
namespace app\modules\advertise\repositories;
use app\entities\advert\AAdvert;
use app\entities\advert\AImages;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use app\modules\pub\utils\DateUtility;
use yii\helpers\VarDumper;
use app\framework\utils\DateTimeHelper;

class AdvertRepository extends RepositoryBase{


    public function getAdvert($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return AAdvert::findOne($id);
    }
    
    //保存广告
    public function save(AAdvert $entiy){
        if(!isset($entiy)){
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }
    
    //广告管理列表
    public function getAdvertList($skip,$limit){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.title, a.adsenseid, b.name FROM a_advert a LEFT JOIN a_adsense b ON a.adsenseid = b.id  LIMIT :skip,:limit";
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = AAdvert::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;

    }
    
    
    //删除
    public function  setDeleted($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        AImages::deleteAll(['fid' => $id]);
        return AAdvert::deleteAll(['id' => $id]);
    }
    
    //保存验证
    public function checkAdsense($adsenseid,$id=''){
        $params = [];
        $params[':adsenseid'] = $adsenseid;
        $sql = " select id from a_advert  where adsenseid=:adsenseid ";
        if(!empty($id)){
            $sql .= ' and id<>:id ';
            $params[':id'] = $id;
        }
        $sql .= ' limit 1 ';
        $db = AAdvert::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $rows = $cmd->queryScalar();
        if(!empty($rows)){
            return true;
        }
        return false;
    }
    
    
}
