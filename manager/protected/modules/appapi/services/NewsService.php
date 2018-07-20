<?php
namespace app\modules\appapi\services;
use app\modules\appapi\repositories\NewsRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
class NewsService extends ServiceBase
{

    private $_newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->_newsRepository = $newsRepository;

    }

    //新鲜事列表
    public function getNewsList($pagesize=10 , $page =1,$id){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_newsRepository->getNewsList($skip,$pagesize,$id);
    }


    public function getNew($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_newsRepository->getNew($id);
    }


}