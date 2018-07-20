<?php 
namespace app\modules\pub\services;
use app\modules\ServiceBase;
use app\modules\pub\repositories\BrannerRepository;

class BrannerService extends ServiceBase{
       private $_brannerRepository;
    
    public function __construct(BrannerRepository $brannerRepository){
        $this->_brannerRepository = $brannerRepository; 
    }
    /**
     * 获取导航信息
     * @param type $account_id
     * @param type $appcode
     * @return type
     * @throws \InvalidArgumentException
     */
    public function getNavigation($appcode){

          if(empty($appcode)){
            throw new \InvalidArgumentException('$appcode对象不存在');
        }
        try{ 
            // 优先从缓存中取
            $cacheKey = "navigation _$appcode";
            $cache = \Yii::$app->cache;
            if ($cache->exists($cacheKey)) {
                if (!empty($cache[$cacheKey])) {
                    return $cache[$cacheKey];
                }
            }
           $arrNavigation = $this->_brannerRepository->getNavigation( $appcode);
            if (!empty($arrNavigation)) {
                $cache->set($cacheKey, $arrNavigation, 3600*24);
            }
            return $arrNavigation;
        } catch (Exception $ex) {
            \Yii::error($ex->getMessage());
            throw $ex;
        } 
    } 
}
