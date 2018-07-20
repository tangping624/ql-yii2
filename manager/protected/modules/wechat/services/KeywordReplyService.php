<?php
/**
 * 关键字自动回复逻辑
 * User: robert
 * Date: 2015/5/7
 * Time: 14:24
 */
namespace app\modules\wechat\services;

use app\modules\wechat\repositories\KeywordReplyRepository;
use app\modules\ServiceBase;

/**
 * Description of StatService
 *
 * @author Lvq
 */
class KeywordReplyService extends ServiceBase
{
    /**
     * @var KeywordReplyRepository
     */
    private $_keywordReplyRepository;

    public function __construct(KeywordReplyRepository $keywordReplyRepository)
    {
        $this->_keywordReplyRepository = $keywordReplyRepository;
    }

    /**
     * 保存关键字自动回复
     * @param object $rule
     * @param array $keywordList
     * @throws \Exception
     * @return bool
     */
    public function addRule($rule, $keywordList)
    {
        return $this->_keywordReplyRepository->insertRule($rule, $keywordList);
    }

    /**
     * 删除关键字自动回复
     * @param string $id
     * @return bool
     */
    public function removeRule($id)
    {
        return $this->_keywordReplyRepository->deleteRule($id);
    }


    /**
     * 根据Id查询关键字回复规则
     * @param string $Id
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return bool
     */
    public function getRuleByAccountId($Id)
    {
        return $this->_keywordReplyRepository->getRuleByAccountId($Id);
    }

    /**
     * 根据公众号Id查询规则所有信息
     * @param string $Id
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return bool
     */
    public function getRuleInfo($Id)
    {
        return $this->_keywordReplyRepository->getRuleInfo($Id);
    }
}
