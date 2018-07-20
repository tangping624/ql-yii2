<?php
namespace app\modules\baike\services;
use app\modules\ServiceBase;
use app\modules\baike\repositories\ManageRepository;
use app\framework\utils\PagingHelper;
class ManageService  extends ServiceBase{
    private $_manageRepository;
    public function __construct(ManageRepository $manageRepository)
    {
        $this->_manageRepository = $manageRepository;

    }

    //百科管理列表
    public function getBaikeList($pagesize=10 , $page =1,$keywords){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_manageRepository->getBaikeList($skip,$pagesize,$keywords);
    }


    public function saveAdvert( $advert,$user_id, $isNew){

       if(!$isNew){
             $advert->modified_on = date('Y-m-d H:i:s', time());
        }else{
            $advert->created_by = $user_id;
            $advert->modified_by = $user_id;
            $advert->created_on = date('Y-m-d H:i:s', time());
            $advert->modified_on = date('Y-m-d H:i:s', time());
        }
       $re= $this->_manageRepository->save($advert);

        return $re;
    }

    public function getWiki($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_manageRepository->getWiki($id);

    }

    public function getCategory(){

        return  $this->_manageRepository->getCategory();
    }

    public function deleteWikiInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_manageRepository->deleteWikiInfo($id);
    }



}
