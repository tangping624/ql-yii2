<?php
namespace app\modules\appapi\services;
use app\modules\appapi\repositories\AdvertRepository;
use app\modules\appapi\repositories\WikiInfoRepository;
use app\modules\appapi\repositories\WikiCategoryRepository;
use app\framework\utils\PagingHelper;
use app\modules\ServiceBase;
class WikiService extends ServiceBase{
    
    private $_advertRepository;
    private $_wikiInfoRepository;
    private $_wikiCategoryRepository;
    
    public function __construct(AdvertRepository $advertRepository,WikiInfoRepository $wikiInfoRepository,WikiCategoryRepository $wikiCategoryRepository)
    {
        $this->_advertRepository=$advertRepository;
        $this->_wikiInfoRepository=$wikiInfoRepository;
        $this->_wikiCategoryRepository=$wikiCategoryRepository;

    }

    //获取百科广告
    public function getAdvert($appcode)
    {
        return $this->_advertRepository->getAdvert($appcode);
    }

    //获取百科分类
    public function getWikiType()
    {
        return $this->_wikiCategoryRepository->getWikiType();
    }

    //百科列表
    public function getWikiList($pagesize=10 , $page =1,$id,$keywords){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_wikiInfoRepository->getWikiList($skip,$pagesize,$id,$keywords);
    }

    //百科详情
    public function getDetails($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_wikiInfoRepository->getDetails($id);
    }
}
