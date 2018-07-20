<?php
namespace app\modules\home\services;
use app\modules\home\repositories\SellerTypeRepository;
use app\modules\home\repositories\SMerchantRepository;
use app\modules\home\repositories\AdvertRepository;
use app\modules\home\repositories\HotSearchRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;

class HomeService extends ServiceBase {

    private $_sMerchantRepository;
    private $_sellerTypeRepository;
    private $_advertRepository;
    private $_hotSearchRepository;
    
    public function __construct(SellerTypeRepository $sellerTypeRepository,SMerchantRepository $sMerchantRepository,AdvertRepository $advertRepository,HotSearchRepository $hotSearchRepository)
    {
        $this->_sellerTypeRepository = $sellerTypeRepository;
        $this->_sMerchantRepository = $sMerchantRepository;
        $this->_advertRepository=$advertRepository;
        $this->_hotSearchRepository=$hotSearchRepository;
       
    }

    //获取分类
    public function getSellerType()
    {
        return $this->_sellerTypeRepository->getSellerType();
    }

    //获取推荐商家
    public function getSellerRecommend($cityPid)
    {
        return $this->_sMerchantRepository->getSellerRecommend($cityPid);
    }

    //获取首页幻灯片广告
    public function getHomeAdvert($appcode)
    {
        return $this->_advertRepository->getHomeAdvert($appcode);
    }

    public function getOtherHomeAdvert($appcode)
    {
        return $this->_advertRepository->getOtherHomeAdvert($appcode);
    }

    //获取首页分类商家
    public function getSellerList($cityPid='',$typePid='',$pageIndex=1,$pageSize=10,$appcode){
        $skip = PagingHelper::getSkip($pageIndex, $pageSize);
        $rs = $this->_sMerchantRepository->getSellerList($cityPid,$typePid,$skip,$pageSize,$appcode);
        return $rs;
    }

    //获取搜索列表
    public function getSearchList($pagesize,$page,$type,$keywords){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_sMerchantRepository->getSearchList( $skip,$pagesize,$type,$keywords);
    }

    public function saveHotSearch($keywords){

        return  $this->_hotSearchRepository->saveHotSearch($keywords);
    }

    public function getHotSearch(){
        return  $this->_hotSearchRepository->getHotSearch();
    }
}
