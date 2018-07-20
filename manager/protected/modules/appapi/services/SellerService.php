<?php
 namespace app\modules\appapi\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\appapi\repositories\SellerRepository;
class SellerService  extends ServiceBase{
    private $_sellerRepository;
    public function __construct(SellerRepository $sellerRepository)
    {
        $this->_sellerRepository = $sellerRepository;
        
    }


 public function getBlogList($pagesize=10 , $page =1,$type_pid,$type_id,$city_id,$keyword,$city_pid){
    if ($page < 0) {
     throw new \InvalidArgumentException('$page');
    }
    if ($pagesize <= 0) {
      throw new \InvalidArgumentException('$pagesize');
   }
    $skip = PagingHelper::getSkip($page, $pagesize);
     return  $this->_sellerRepository->getBlogList($skip,$pagesize,$type_pid,$type_id,$city_id,$keyword,$city_pid);
 }


   public function getDetails($id, $memberId){
    if (empty($id)) {
     throw new \InvalidArgumentException('$id');
    }
    return $this->_sellerRepository->getDetails($id, $memberId);
   }

    public function getImages($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerRepository-> getImages($id);
    }

    public function getCount($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerRepository-> getCount($id);
    }

    //点赞/收藏加1
    public function setAddOne($id,$type)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_sellerRepository->setAddOne($id,$type);
    }

    //收藏减1
    public function setSubtractOne($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_sellerRepository->setSubtractOne($id);
    }

    public function clickPraise($id,$memberId,$type,$product_id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerRepository->  clickPraise($id,$memberId,$type,$product_id);
    }

    //是否点赞/收藏
    public function IsPraise($id,$memberId,$type){
        return $this->_sellerRepository-> IsPraise($id,$memberId,$type);
    }

    //取消收藏商家
    public function cancelCollection($memberId,$id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerRepository->cancelCollection($memberId,$id);
    }

    //取消收藏商品
    public function cancelCollectionGoods($memberId,$id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerRepository->cancelCollectionGoods($memberId,$id);
    }

    public function getRoundMerchant($id,$type_pid){
        return $this->_sellerRepository->getRoundMerchant($id,$type_pid);
    }
}
