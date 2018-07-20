<?php
/**
 * 关键字自动回复数据访问
 * User: robert
 * Date: 2015/5/7
 * Time: 14:19
 */
namespace app\modules\wechat\repositories;

use app\entities\PAccount;
use app\entities\PReply;
use app\entities\PKeywordSet;
use app\entities\PKeyword;
use app\entities\PRuleReply;
use app\entities\PWelcome;
use app\framework\db\SqlHelper;
use app\framework\utils\StringHelper;
use app\modules\RepositoryBase;

class KeywordReplyRepository extends RepositoryBase
{
    /**
     * 保存关键字规则
     * @param object $rule
     * @param array $keywordList
     * @throws \Exception
     * @return bool
     */
    public function insertRule($rule, $keywordList)
    {
        $conn = PKeywordSet::getDb();
        $transaction = $conn->beginTransaction();

        try {
            $oldRule = $this->getRuleByRuleId($rule->id);
            if (!$oldRule) {
                $conn->createCommand()->insert('p_keyword_set', $rule->toArray())->execute();
            } else {
                $oldRule["name"] = $rule->name;
                $oldRule["type"] = $rule->type;
                $oldRule["content"] = $rule->content;
                $result = $this->updateRule($oldRule);
                if (!$result) {
                    $transaction->rollBack();
                    return false;
                }
            }

            //新增关键字
            $result = $this->insertKeywords($keywordList, $rule, $conn);

            if (!$result) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;

        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 更新被添加自动回复
     * @param array $rule
     * @throws \Exception
     * @return bool
     */
    public function updateRule($rule)
    {
        $conn = PKeywordSet::getDb();
        $data = ['name' => $rule["name"]
                ,'type' => $rule["type"]
                ,'content' => $rule["content"]];
        try {
            return SqlHelper::update('p_keyword_set', $conn, $data, ['id' => $rule["id"], 'is_deleted'=>0]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除关键字回复
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteRule($id)
    {
        $conn = PKeywordSet::getDb();
        $data = ['is_deleted' => 1];
        try {
            SqlHelper::update('p_keyword_set', $conn, $data, ['id' => $id]);
            SqlHelper::update('p_keyword', $conn, $data, ['set_id' => $id]);
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 保存关键字信息
     * @param $keywordList
     * @param $rule
     * @param $conn
     * @return bool
     */
    private function insertKeywords($keywordList, $rule, $conn)
    {
        //$conn->createCommand()->update("p_keyword", ['is_deleted' => 1], ['set_id' => $rule->id])->execute();
        if (count($keywordList)>0) {
            $oldKeywordIds = [];
            foreach ($keywordList as $row) {
                if (empty($row->id)) {
                    $keyword = new PKeyword();
                    $keyword->id = StringHelper::uuid();
                    $keyword->set_id = $rule->id;
                    $keyword->account_id = $rule->account_id;
                    $keyword->keyword = $row->keyword;
                    $keyword->is_exact = $row->is_exact;

                    $keyword->created_on = $rule->created_on;
                    $keyword->modified_on = $rule->modified_on;
                    $keyword->created_by = $rule->created_by;
                    $keyword->modified_by = $rule->modified_by;
                    $keyword->is_deleted = 0;

                    $result = $conn->createCommand()->insert("p_keyword", $keyword->toArray())->execute();
                    if (!$result) {
                        return false;
                    }

                    $oldKeywordIds[] = $keyword->id;
                } else {
                    $data = ['keyword' => $row->keyword, 'is_exact' => $row->is_exact];
                    $conn->createCommand()->update("p_keyword", $data, ['id' => $row->id])->execute();

                    $oldKeywordIds[] = $row->id;
                }

            }

            if (count($oldKeywordIds)>0) {
                $this->deleteKeywords($oldKeywordIds, $rule, $conn);
            }
        }
        return true;
    }

    /**
     * 删除不存在的关键字信息
     * @param $oldKeywordIds
     * @param $rule
     * @param $conn
     * @return bool
     */
    private function deleteKeywords($oldKeywordIds, $rule, $conn)
    {
        return $conn->createCommand()->update("p_keyword", ['is_deleted' => 1], ['and', ['=','set_id',$rule->id], ['not in','id',$oldKeywordIds]])->execute();
    }

    /**
     * 获取规则所有信息
     * @param $accountId
     * @return array
     */
    public function getRuleInfo($accountId)
    {
        $rules = $this->getRuleByAccountId($accountId);

        for ($i=0; $i<count($rules); $i++) {
            $ruleId = $rules[$i]["id"];

            $keywords = $this->getKeywordByRuleId($ruleId);
            $rules[$i]["keyword_data"] = $keywords;
        }

        return $rules;
    }

    /**
     * 根据公众号Id获取规则信息
     * @param $accountId
     * @return array|bool
     */
    public function getRuleByAccountId($accountId)
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('p_keyword_set')
            ->where('is_deleted=0')
            ->andWhere(['=','account_id',$accountId]);

        $connection = PKeywordSet::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }

    /**
     * 根据规则Id获取规则信息
     * @param $ruleId
     * @return array|bool
     */
    public function getRuleByRuleId($ruleId)
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('p_keyword_set')
            ->where('is_deleted=0')
            ->andWhere(['=','id',$ruleId]);

        $connection = PKeywordSet::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }

    /**
     * 根据规则Id获取规则对应关键字信息
     * @param $ruleId
     * @return array|bool
     */
    public function getKeywordByRuleId($ruleId)
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('p_keyword')
            ->where('is_deleted=0')
            ->andWhere(['=','set_id',$ruleId]);

        $connection = PKeyword::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }
}
