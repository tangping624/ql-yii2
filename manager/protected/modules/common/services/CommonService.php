<?php
namespace app\modules\common\services;
use app\modules\common\repositories\CommonRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\CheckResult;
class CommonService extends ServiceBase
{
    private $_commonRepository;
    
    public function __construct(CommonRepository $commonRepository)
    {
        $this->_commonRepository = $commonRepository;
    }

    //旅游&投资&合作交流列表
    public function getShopList($pagesize=10 , $page =1,$keyword,$app_code){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_commonRepository->getShopList($skip,$pagesize,$keyword,$app_code);
    }

    //编辑获取旅游&投资&合作交流新增的商家
    public function getSellerList($pagesize=10 , $page =1,$keyword,$app_code){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_commonRepository->getSellerList($skip,$pagesize,$keyword,$app_code);
    }

    //保存
    public function saveShop($house)
    {
        if (!isset($house)) {
            throw new \InvalidArgumentException('$house');
        }
        $re= $this->_commonRepository->save($house);
        return $re;
    }

    //编辑实体
    public function getShop($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_commonRepository->getShop($id);
    }

    
    //旅游&投资&合作交流删除
    public function deleteShop($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_commonRepository->deleteShop($id);
    }
    
}