<?php
namespace app\modules\house\services;
use app\modules\house\repositories\HouseRepository;
use app\modules\house\repositories\SMerchantRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\CheckResult;
class HouseService extends ServiceBase
{

    private $_houseRepository;
    private $_sMerchantRepository;
    
    public function __construct(HouseRepository $houseRepository,SMerchantRepository $sMerchantRepository)
    {
        $this->_houseRepository = $houseRepository;
        $this->_sMerchantRepository = $sMerchantRepository;
    }

    //房产列表
    public function getHouseList($pagesize=10 , $page =1,$keyword){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_houseRepository->getHouseList($skip,$pagesize,$keyword);
    }

    //获取房产的商家
    public function getSellerList($pagesize=10 , $page =1,$keyword){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_sMerchantRepository->getSellerList($skip,$pagesize,$keyword,$app_code='house');
    }

    //编辑获取房产类别
    public function getHouseType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_houseRepository->getHouseType($id);
    }

    public function saveHouse($house)
    {
        if (!isset($house)) {
            throw new \InvalidArgumentException('$house');
        }
        $re= $this->_houseRepository->save($house);
        return $re;
    }

    public function getHouse($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_houseRepository->getHouse($id);
    }

    public function getHouseDetails($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_houseRepository->getHouseDetails($id);
    }
    
    //房产删除
    public function deleteHouse($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_houseRepository->deleteHouse($id);
    }
    
}