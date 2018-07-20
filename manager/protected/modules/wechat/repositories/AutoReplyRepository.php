<?php
/**
 * 被添加、消息自动回复数据访问
 * User: robert
 * Date: 2015/5/7
 * Time: 14:19
 */
namespace app\modules\wechat\repositories;

use app\entities\PAccount;
use app\entities\PReply;
use app\entities\PWelcome;
use app\framework\db\SqlHelper;
use app\modules\RepositoryBase;

class AutoReplyRepository extends RepositoryBase
{
    /**
     * 保存被添加自动回复
     * @param object $welcome
     * @throws \Exception
     * @return bool
     */
    public function insertWelcome($welcome)
    {
        $conn = PWelcome::getDb();
        try {
            $oldWelcome = $this->getWelcomeByAccountId($welcome->account_id);

            if (!$oldWelcome) {
                $conn->createCommand()->insert('p_welcome', $welcome->toArray())->execute();
            } else {
                $oldWelcome["content"] = $welcome->content;
                $oldWelcome["type"] = $welcome->type;
                $this->updateWelcome($oldWelcome);
            }

            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 更新被添加自动回复
     * @param array $welcome
     * @throws \Exception
     * @return bool
     */
    public function updateWelcome($welcome)
    {
        $conn = PWelcome::getDb();
        $data = ['content' => $welcome["content"], 'type'=> $welcome["type"]];
        try {
            SqlHelper::update('p_welcome', $conn, $data, ['account_id' => $welcome["account_id"], 'is_deleted'=>0]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除被添加自动回复
     * @param $accountId
     * @throws \Exception
     * @return bool
     */
    public function removeWelcome($accountId)
    {
        $conn = PWelcome::getDb();
        $data = ['is_deleted' => 1];
        try {
            SqlHelper::update('p_welcome', $conn, $data, ['account_id' => $accountId]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 保存消息自动回复
     * @param object $reply
     * @throws \Exception
     * @return bool
     */
    public function insertReply($reply)
    {
        $conn = PReply::getDb();
        try {
            $oldReply = $this->getReplyByAccountId($reply->account_id);
            if (!$oldReply) {
                $conn->createCommand()->insert('p_reply', $reply->toArray())->execute();
            } else {
                $oldReply["content"] = $reply->content;
                $oldReply["type"] = $reply->type;
                $this->updateReply($oldReply);
            }
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 更新消息自动回复
     * @param array $reply
     * @throws \Exception
     * @return bool
     */
    public function updateReply($reply)
    {
        $conn = PReply::getDb();
        $data = ['content' => $reply["content"], 'type'=> $reply["type"]];
        try {
            SqlHelper::update('p_reply', $conn, $data, ['account_id' => $reply["account_id"], 'is_deleted'=>0]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除消息自动回复
     * @param $accountId
     * @throws \Exception
     * @return bool
     */
    public function removeReply($accountId)
    {
        $conn = PReply::getDb();
        $data = ['is_deleted' => 1];
        try {
            SqlHelper::update('p_reply', $conn, $data, ['account_id' => $accountId]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 根据公司Id获取自动回复信息
     * @param $accountId
     * @return array|bool
     */
    public function getReplyByAccountId($accountId)
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('p_reply')
            ->where('is_deleted=0')
            ->andWhere(['=','account_id',$accountId]);

        $connection = PReply::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }

    /**
     * 根据公司Id获取被添加回复信息
     * @param $accountId
     * @return array|bool
     */
    public function getWelcomeByAccountId($accountId)
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('p_welcome')
            ->where('is_deleted=0')
            ->andWhere(['=','account_id',$accountId]);

        $connection = PWelcome::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }
}
