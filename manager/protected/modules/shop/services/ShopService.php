<?php
namespace app\modules\shop\services;
use app\modules\shop\repositories\GShopRepository;
use app\modules\house\repositories\SMerchantRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\CheckResult;
class ShopService extends ServiceBase
{
    private $_gShopRepository;
    private $_sMerchantRepository;
    
    public function __construct(GShopRepository $gShopRepository,SMerchantRepository $sMerchantRepository)
    {
        $this->_gShopRepository = $gShopRepository;
        $this->_sMerchantRepository = $sMerchantRepository;
    }

    //购物惠列表
    public function getShopList($pagesize=10 , $page =1,$keyword,$app_code){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_gShopRepository->getShopList($skip,$pagesize,$keyword,$app_code);
    }

    //获取购物惠的商家
    public function getSellerList($pagesize=10 , $page =1,$keyword,$app_code){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_sMerchantRepository->getSellerList($skip,$pagesize,$keyword,$app_code);
    }

    
    public function saveShop($house)
    {
        if (!isset($house)) {
            throw new \InvalidArgumentException('$house');
        }
        $re= $this->_gShopRepository->save($house);
        return $re;
    }

    public function getShop($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_gShopRepository->getShop($id);
    }

    public function getShopDetails($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_gShopRepository->getShopDetails($id);
    }

    //购物惠删除
    public function deleteShop($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_gShopRepository->deleteShop($id);
    }
    
}