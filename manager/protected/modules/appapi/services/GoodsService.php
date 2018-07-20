<?php
namespace app\modules\appapi\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\appapi\repositories\GoodsRepository;
class GoodsService  extends ServiceBase{
    private $_goodsRepository;
    public function __construct(GoodsRepository $goodsRepository)
    {
        $this->_goodsRepository = $goodsRepository;

    }

    //获取购物惠分类
    public function getShopType($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_goodsRepository->getShopType($id);
    }
    
    //商品列表
    public function getShopList($pagesize=10 , $page =1,$type_id,$seller_id){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_goodsRepository->getShopList($skip,$pagesize,$type_id,$seller_id);
    }

    //产品详情
    public function getProductDetails($product_id,$memberId){
        if (empty($product_id)) {
            throw new \InvalidArgumentException('$product_id');
        }
        return  $this->_goodsRepository->getProductDetails($product_id,$memberId);
    }



    //是否点赞/收藏
    public function IsProductPraise($product_id,$memberId,$type){
        return $this->_goodsRepository-> IsProductPraise($product_id,$memberId,$type);
    }


}
