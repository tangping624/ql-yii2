<?php
namespace app\modules\shop\services;
use app\modules\shop\repositories\GTypeRepository;
use app\modules\shop\repositories\GShopRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
class TypeService extends ServiceBase
{
    private $_gTypeRepository;
    private $_gShopRepository;
    
    public function __construct(GTypeRepository $gTypeRepository,GShopRepository $gShopRepository)
    {
        $this->_gTypeRepository = $gTypeRepository;
        $this->_gShopRepository = $gShopRepository;
    }

    //购物惠分类列表
    public function getTypeList($pagesize=10 , $page =1,$keyword,$app_code){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_gTypeRepository->getTypeList($skip,$pagesize,$keyword,$app_code);
    }
    
    public function saveType($house)
    {
        if (!isset($house)) {
            throw new \InvalidArgumentException('$house');
        }
        $re= $this->_gTypeRepository->save($house);
        return $re;
    }

    public function getType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_gTypeRepository->getType($id);
    }
    

    //购物惠分类删除
    public function deleteType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_gTypeRepository->deleteType($id);
    }

    //判断分类下有没有商品
    public function getTypeGoods($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_gShopRepository->getTypeGoods($id);
    }
    
}