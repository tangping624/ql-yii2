<?php
namespace app\modules\house\services;
use app\modules\house\repositories\HouseRepository;
use app\modules\home\repositories\AdvertRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\CheckResult;
class HouseService extends ServiceBase
{

    private $_houseRepository;
    private $_advertRepository;
    
    public function __construct(HouseRepository $houseRepository,AdvertRepository $advertRepository)
    {
        $this->_houseRepository = $houseRepository;
        $this->_advertRepository=$advertRepository;
    }

    //获取房产广告
    public function getAdvert($appcode)
    {
        return $this->_advertRepository->getAdvert($appcode);
    }

    //获取房产类别
    public function getHouseType(){
        return  $this->_houseRepository->getHouseType();
    }


    //房产列表
    public function getHouseList($pagesize=10 , $page =1,$type_pid,$tag_id,$city_id,$keyword,$city_pid){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_houseRepository->getHouseList($skip,$pagesize,$type_pid,$tag_id,$city_id,$keyword,$city_pid);
    }



    //产品列表
    public function getProductList($pagesize=10 , $page =1,$seller_id){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_houseRepository->getProductList($skip,$pagesize,$seller_id);
    }

    //产品详情
    public function getProductDetails($product_id,$memberId){
        if (empty($product_id)) {
            throw new \InvalidArgumentException('$product_id');
        }
        return  $this->_houseRepository->getProductDetails($product_id,$memberId);
    }

    public function clickPraise($id,$memberId,$type,$product_id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerRepository->  clickPraise($id,$memberId,$type,$product_id);
    }

    //是否点赞/收藏
    public function IsProductPraise($product_id,$memberId,$type){
        return $this->_houseRepository-> IsProductPraise($product_id,$memberId,$type);
    }

    
}