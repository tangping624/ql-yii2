<?php
/**
 * 被添加、消息自动回复逻辑
 * User: robert
 * Date: 2015/5/7
 * Time: 14:24
 */
namespace app\modules\wechat\services;

use app\modules\wechat\repositories\AutoReplyRepository;
use app\modules\ServiceBase;

/**
 * Description of StatService
 *
 * @author Lvq
 */
class AutoReplyService extends ServiceBase
{
    /**
     * @var AutoReplyRepository
     */
    private $_autoReplyRepository;

    public function __construct(AutoReplyRepository $autoReplyRepository)
    {
        $this->_autoReplyRepository = $autoReplyRepository;
    }

    /**
     * 保存被添加自动回复
     * @param object $welcome
     * @throws \Exception
     * @return bool
     */
    public function addWelcome($welcome)
    {
        return $this->_autoReplyRepository->insertWelcome($welcome);
    }

    /**
     * 删除被添加自动回复
     * @param $accountId
     * @throws \Exception
     * @return bool
     */
    public function removeWelcome($accountId)
    {
        return $this->_autoReplyRepository->removeWelcome($accountId);
    }

    /**
     * 删除消息自动回复
     * @param $accountId
     * @throws \Exception
     * @return bool
     */
    public function removeReply($accountId)
    {
        return $this->_autoReplyRepository->removeReply($accountId);
    }

    /**
     * 保存消息自动回复
     * @param object $reply
     * @throws \Exception
     * @return bool
     */
    public function addReply($reply)
    {
        return $this->_autoReplyRepository->insertReply($reply);
    }

    /**
     * 根据Id查询自动回复
     * @param string $Id
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return bool
     */
    public function getReplyByAccountId($Id)
    {
        return $this->_autoReplyRepository->getReplyByAccountId($Id);
    }

    /**
     * 根据Id查询被添加回复
     * @param string $Id
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return bool
     */
    public function getWelcomeByAccountId($Id)
    {
        return $this->_autoReplyRepository->getWelcomeByAccountId($Id);
    }
}