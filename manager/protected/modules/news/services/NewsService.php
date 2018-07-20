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
    public function getNewsList($pagesize=10 , $page =1,$keyword){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_newsRepository->getNewsList($skip,$pagesize,$keyword);
    }


    public function saveNews($news,$user_id, $isNew){

        if(!$isNew){
            $news->modified_on = date('Y-m-d H:i:s', time());
        }else{
            $news->created_by = $user_id;
            $news->modified_by = $user_id;
            $news->created_on = date('Y-m-d H:i:s', time());
            $news->modified_on = date('Y-m-d H:i:s', time());
        }
        $news->source = 1;
        $news->member_id = $user_id;
        $re= $this->_newsRepository->save($news);
        return $re;
    }

    public function getNew($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_newsRepository->getNew($id);
    }

    public function getType()
    {
        return  $this->_sellerTypeRepository->getType();
    }

    //新鲜事删除
    public function deleteNews($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_newsRepository->deleteNews($id);
    }
}