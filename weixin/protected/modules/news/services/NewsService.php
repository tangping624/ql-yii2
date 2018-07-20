<?php
namespace app\modules\news\services;
use app\modules\news\repositories\NewsRepository;
use app\modules\news\repositories\SellerTypeRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\CheckResult;
class NewsService extends ServiceBase
{

    private $_newsRepository;
    private $_sellerTypeRepository;

    public function __construct(NewsRepository $newsRepository,SellerTypeRepository $sellerTypeRepository)
    {
        $this->_newsRepository = $newsRepository;
        $this->_sellerTypeRepository=$sellerTypeRepository;

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