<?php
namespace app\modules\appapi\services;
use app\modules\ServiceBase;
use app\modules\appapi\repositories\PublicRepository;
use app\framework\utils\PagingHelper;
class PublicService extends ServiceBase{
    private $_PublicRepository;


    public function __construct(PublicRepository $publicRepository){
        $this->_PublicRepository = $publicRepository;
    }

    //获取子分类
    public function getType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  $this->_PublicRepository->getType($id);

    }

    //获取三条新鲜事
    public function getNews($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_PublicRepository->getNews($id);
    }

    //获得城市
    public function getCity(){
        return  $this->_PublicRepository->getCity();
    }

    //新鲜事列表
    public function getNewsList($pagesize,$page,$id){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_PublicRepository->getNewsList($skip,$pagesize,$id);
    }

    //获取广告
    public function getAdvert($appcode)
    {
        return $this->_PublicRepository->getAdvert($appcode);
    }
}
