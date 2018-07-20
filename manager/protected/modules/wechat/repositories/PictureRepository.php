<?php
/**
 * 图文消息
 * User: robert
 * Date: 2015/5/7
 * Time: 14:19
 */
namespace app\modules\wechat\repositories;

use app\entities\PReply;
use app\entities\PWelcome;
use app\modules\RepositoryBase;
use yii\db\Query;
use app\entities\PMenu;
use app\entities\PKeywordSet;

class  PictureRepository extends RepositoryBase
{
    public function getDetailById($accountId, $type, $id=null)
    {
        $db=PMenu::getDb();
        $table='p_'.$type;

        $query = ( new Query() )
            ->from($table)
            ->select('content')
            ->where('is_deleted=0')
            ->andWhere(['=', 'type', '图文']);
        if( $id ) {
            $query->andWhere(['=', 'id', $id]);
        } else {
            $query->andWhere(['=', 'account_id', $accountId]);
        }

        $data = $query->createCommand($db)->queryOne();
        if( $data ) {
            return $data;
        }

        return false;
    }

    public function getNameByAccountId($accountId)
    {
        $query = (new Query())
            ->select('name')
            ->from('p_account')
            ->where('is_deleted=0')
            ->andWhere(['=', 'id', $accountId]);

        return $query->createCommand(PMenu::getDb())->queryScalar();
    }
    public function getAttentionUrl($accountId)
    {
        $query = (new Query())
            ->select('attention_url')
            ->from('p_account')
            ->where('is_deleted=0')
            ->andWhere(['=', 'id', $accountId]);

        return $query->createCommand(PMenu::getDb())->queryScalar();
    }
    public function getArticleById($Id)
    {
        $query = (new Query())
            ->select('*')
            ->from('p_article')
            ->where('is_deleted=0')
            ->andWhere(['=', 'id', $Id]);

        return $query->createCommand(PMenu::getDb())->queryOne();
    }
}
