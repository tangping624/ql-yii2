<?php
namespace app\modules\advertise\repositories;
use app\entities\advert\AAdsense; 
use app\modules\RepositoryBase;
class AdsenseRepository extends RepositoryBase{
    
    public function getAdsenses() { 
        return AAdsense::find()->where(['isenabled'=>1])->orderBy('orderby,grouporder')->all();
    }
}
