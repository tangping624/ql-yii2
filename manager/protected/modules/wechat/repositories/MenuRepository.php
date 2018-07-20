<?php
/**
 * 微信自定义菜单
 * User: OceanDeng(denghg@mysoft.com.cn)
 * Date: 2015/5/7
 * Time: 14:19
 */

namespace app\modules\wechat\repositories;

use app\entities\PMenu;
use app\framework\db\SqlHelper;
use app\modules\RepositoryBase;

class MenuRepository extends RepositoryBase
{
    /**
     * 新增菜单
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $menuInfo
     * @return bool
     * @throws \Exception
     */
    public function addMenu($menuInfo)
    {
        $conn = PMenu::getDb();
        $transaction = $conn->beginTransaction();
        // $data=["id"=>$menuInfo->id,"account_id"=>$menuInfo->account_id,"name"=>$menuInfo->name,"level"=>$menuInfo->level];
        try {
            $conn->createCommand()->insert('p_menu', $menuInfo->toArray())->execute();
//todo 新增图文
//            $result=null;
//            if (!$result) {
//                $transaction->rollBack();
//                return false;
//            }

            $transaction->commit();
            return true;

        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }


    /**
     * 设置跳转的页面地址
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $id 菜单ID
     * @param $jumpPageUrl 跳转的页面地址
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function setMenuJumpPage($id, $jumpPageUrl, $modified_by, $modified_on)
    {
        $conn = PMenu::getDb();
        $data = ["content" => $jumpPageUrl, "type" => "链接", "modified_by" => $modified_by, "modified_on" => $modified_on];
        try {
            SqlHelper::update('p_menu', $conn, $data, ['id' => $id]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 重命名菜单
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $menuName 重命名菜单名称
     * @param $id 菜单ID
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function renameMenu($menuName, $id, $modified_by, $modified_on)
    {
        $conn = PMenu::getDb();

        $data = ["name" => $menuName, "modified_by" => $modified_by, "modified_on" => $modified_on];
        try {
            SqlHelper::update('p_menu', $conn, $data, ['id' => $id]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 根据id查找菜单
     * @param string $menuId
     * @return bool|array
     */
    public function findMenuById($menuId)
    {
        $row = (new \yii\db\Query())
            ->select('id,type,content,parent_id')
            ->from('p_menu')
            ->where(['id' => $menuId, 'is_deleted' => 0])
            ->createCommand(PMenu::getDb())
            ->queryOne();
        
        return $row;
    }

    /**
     * 删除菜单
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $id 菜单ID
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function deleteMenu($id, $modified_by, $modified_on)
    {
        $conn = PMenu::getDb();
        $data = ["is_deleted" => true,"modified_by"=>$modified_by,"modified_on"=>$modified_on];
        try {
            //$idParam = "id='{$id}'";
            //$parent_id_param = "parent_id='{$id}'";
            //SqlHelper::update('p_menu', $conn, $data, ['or', $idParam, $parent_id_param]);
            SqlHelper::update('p_menu', $conn, $data, ['or', ['=', 'id', $id], ['=', 'parent_id', $id]]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 设置菜单响应事件
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $id 菜单ID
     * @param $type 信息类型
     * @param $content 内容
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function setMenuSendMessage($id, $type, $content,$modified_by, $modified_on)
    {
        $conn = PMenu::getDb();
        $data = ["type" => $type, "content" => $content,"modified_by"=>$modified_by,"modified_on"=>$modified_on];
        try {
            SqlHelper::update('p_menu', $conn, $data, ['id' => $id]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取菜单信息
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $account_id
     * @param $id
     * @return mixed
     */
    public function getMenuInfo($account_id, $id)
    {
        $query = (new \yii\db\Query())
            ->select('id,type,content')
            ->from('p_menu')
            ->where(['=', 'id', $id])
            ->andWhere(['=', 'account_id', $account_id]);

        $connection = PMenu::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }

    /**
     * 根据公众号ID获得菜单列表
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $account_id 公众号ID
     * @return array
     */
    public function getMenuList($account_id)
    {
        $query = (new \yii\db\Query())
            ->select('id,name,level,parent_id,type,content,event_key,sort')
            ->from('p_menu')
            ->where(['=', 'account_id', $account_id])
            ->andWhere('is_deleted=0')
            ->orderBy(['sort' => SORT_ASC]);

        $connection = PMenu::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }

    /**
     * 根据公众号ID获得最后一个菜单列表
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $account_id 公众号ID
     * @param $parent_id 父ID
     * @return array
     */
    public function getMaxMenu($account_id, $parent_id)
    {
        $query = (new \yii\db\Query())
            ->select('id,name,level,parent_id,type,content,event_key,sort')
            ->from('p_menu')
            ->where(['=', 'account_id', $account_id])
            ->andWhere('is_deleted=0');

        if ($parent_id == null) {
            $query->andWhere("parent_id is null");
        } else {
            $query->andWhere(['=', 'parent_id', $parent_id]);
        }
        $query->orderBy(['sort' => SORT_DESC]);

        $connection = PMenu::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }


    /**
     * 设置菜单的排序
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $ids id集合：id按序排序逗号分隔从前端传入
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function setMenuSort($ids,$modified_by, $modified_on)
    {
        $idData = json_decode($ids); //explode(',', $ids);
        /*$sql = "";
        for ($index = 0; $index < count($idData); $index++) {
            $sort = $idData[$index]->sort;
            $id = $idData[$index]->id;
            $sql .= "update p_menu set sort={$sort},modified_by='{$modified_by}',modified_on='{$modified_on}' where id='{$id}';";
        }*/
        $conn = PMenu::getDb();
        $transaction = $conn->beginTransaction();
        try {
            for ($index = 0; $index < count($idData); $index++) {
                $sort = $idData[$index]->sort;
                $id = $idData[$index]->id;
                $conn->createCommand("update p_menu set sort=:sort,modified_by=:modified_by,modified_on=:modified_on where id=:id",
                    [':sort'=>$sort, ':modified_by'=>$modified_by, ':modified_on'=>$modified_on, ':id'=>$id])->execute();
            }
            //$conn->createCommand($sql)->execute();
            $transaction->commit();
            return true;

        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 重设动作
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $id
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function resetAction($id,$modified_by, $modified_on)
    {
        $conn = PMenu::getDb();
        $data = ["type" => null,"content"=>null, "modified_by"=>$modified_by,"modified_on"=>$modified_on];
        try {
            SqlHelper::update('p_menu', $conn, $data, ['id' => $id]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}