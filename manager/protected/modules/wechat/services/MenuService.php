<?php
/**
 * 微信自定义菜单
 * User: OceanDeng(denghg@mysoft.com.cn)
 * Date: 2015/5/7
 * Time: 14:19
 */

namespace app\modules\wechat\services;

use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\helper\ButtonFactory;
use app\framework\weixin\proxy\fw\Menu;
use app\modules\api\services\BizCacheManager;
use app\modules\ServiceBase;
use app\modules\wechat\repositories\AccountRepository;
use app\modules\wechat\repositories\MenuRepository;
use app\entities\PMenu;

class MenuService extends ServiceBase
{

    /**
     * @var MenuRepository
     */
    private $_menuRepository;

    /**
     * @var AccountRepository
     */
    private $_accountRepository;


    /**
     * @param MenuRepository $menuRepository
     * @param AccountRepository $accountRepository
     */
    public function __construct(MenuRepository $menuRepository, AccountRepository $accountRepository)
    {
        $this->_menuRepository = $menuRepository;
        $this->_accountRepository = $accountRepository;
    }

    /**
     * @param $original_id 微信原始ID(p_account)
     * @return Menu
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function getWeiXinMenu($original_id)
    {
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        if (!isset($accessTokenRepository)) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }
        $accessTokenHelper = new AccessTokenHelper($original_id, $accessTokenRepository);
        $weiXinMenu = new Menu($accessTokenHelper);
        return $weiXinMenu;
    }

    /**
     * 新增菜单
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $menuInfo
     * @return bool
     * @throws \Exception
     */
    public function addMenu($menuInfo)
    {
        return $this->_menuRepository->addMenu($menuInfo);
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
        return $this->_menuRepository->getMaxMenu($account_id, $parent_id);
    }

    public function publishMenu($account_id)
    {
        //微信企业号：对应的唯一标识符,每个公众号\公众号\订阅号一个
        $original_id = $this->_accountRepository->getWeChatOriginalId($account_id);
        \Yii::info('WeiXin Menu：第一步 获取微信的原始ID ' . $original_id);
        $weiXinMenu = $this->getWeiXinMenu($original_id);

        //验证菜单是否设置响应动作
        $menuData = $this->_menuRepository->getMenuList($account_id);
        $error = "";
        foreach ($menuData as $row) {
            if ($row["level"] == 1) {
                if (empty($row["type"]) || empty($row["content"])) {
                    $error .= "菜单" . $row["name"] . "请设置动作<br/>";
                }
            } else {
                $haveChild = false;
                $parentId = $row["id"];
                foreach ($menuData as $childRow) {
                    if ($childRow["parent_id"] == $parentId) {
                        $haveChild = true;
                        break;
                    }
                }

                if ($haveChild == false) {
                    if (empty($row["type"]) || empty($row["content"])) {
                        $error .= "菜单" . $row["name"] . "请设置动作<br/>";
                    }
                }
            }

        }

        if ($error != "") {
            throw new \Exception($error);
        }

        //1、先删除微信菜单
        $weiXinMenu->delete();
        \Yii::info('WeiXin Menu：第二步 删除菜单 ');
        //2、将本地数据库的菜单数据转成为微信端的菜单格式
        $menuList = $this->getMenuList($account_id);
        $weiXinMenuData = $this->weiXinMenuTree($menuList, $original_id);
        //3、创建微信菜单
        \Yii::info('WeiXin Menu：第三步 创建菜单 ' . json_encode($weiXinMenuData));
        $result = $weiXinMenu->create($weiXinMenuData);
        \Yii::info('WeiXin Menu：第四步 创建菜单成功 ' . json_encode($result));
        return $result;
    }

    /**
     * 将本地数据库的菜单数据转成为微信端的菜单格式
     * @param $menuList 本地数据库菜单数据
     * @param $original_id 微信公众号原始ID
     * @return array|null 微信端菜单
     * @throws \yii\base\NotSupportedException
     */
    private function weiXinMenuTree($menuList, $original_id)
    {
        $weiXinMenuObject = null;
        foreach ($menuList as $menuInfo) {
            //首层菜单（含有子菜单）
            if (isset($menuInfo["items"])) {
                $menuButton = ButtonFactory::create("menu");
                $menuButton->name = $menuInfo["name"];
                $menuButton->sub_button = $this->weiXinMenuTree($menuInfo["items"], $original_id);
                $weiXinMenuObject[] = $menuButton;
            } else {
                if ($menuInfo["type"] == "链接") {
                    $menuButton = ButtonFactory::create("view");
                    $menuButton->name = $menuInfo["name"];
                    $menuButton->url = $menuInfo["content"];
                    $weiXinMenuObject[] = $menuButton;
                } else {
                    $menuButton = ButtonFactory::create("click");
                    $menuButton->name = $menuInfo["name"];
                    $menuButton->key = $menuInfo["id"];
                    $weiXinMenuObject[] = $menuButton;
                }
            }
            //清楚缓存
            BizCacheManager::clearReplayCache($original_id, "menu", $menuInfo["id"]);
        }
        return $weiXinMenuObject;
    }

    /**
     * 重命名菜单
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $menuName
     * @param $id
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function renameMenu($menuName, $id, $modified_by, $modified_on)
    {
        return $this->_menuRepository->renameMenu($menuName, $id, $modified_by, $modified_on);
    }

    /**
     * 删除菜单
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $id
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function deleteMenu($id, $modified_by, $modified_on)
    {
        return $this->_menuRepository->deleteMenu($id, $modified_by, $modified_on);
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
        return $this->_menuRepository->setMenuJumpPage($id, $jumpPageUrl, $modified_by, $modified_on);
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
    public function setMenuSendMessage($id, $type, $content, $modified_by, $modified_on)
    {
        return $this->_menuRepository->setMenuSendMessage($id, $type, $content, $modified_by, $modified_on);
    }

    /**
     * 获取菜单信息
     * @param $account_id
     * @param $id
     * @return mixed
     */
    public function getMenuInfo($account_id, $id)
    {
        return $this->_menuRepository->getMenuInfo($account_id, $id);
    }

    /**
     * 根据公众号ID获得菜单列表
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $account_id 公众号ID
     * @return array
     */
    public function getMenuList($account_id)
    {
        $data = $this->_menuRepository->getMenuList($account_id);
        $data = $this->formatToArrayTree($data);
        return $data;
    }

    public function validateMenu($menuInfo)
    {
        if (empty($menuInfo->account_id)) {
            throw new \Exception("account_id值无效");
        }

        if ($menuInfo->level < 0 || $menuInfo->level > 1) {
            throw new \Exception("level值无效");
        }

        if ($menuInfo->level == 0 && !empty($menuInfo->parent_id)) {
            throw new \Exception("level或parent_id值不匹配");
        }

        if ($menuInfo->level == 1 && empty($menuInfo->parent_id)) {
            throw new \Exception("level或parent_id值不匹配");
        }

        if (!empty($menuInfo->parent_id)) {
            $menuData = $this->_menuRepository->findMenuById($menuInfo->parent_id);
        }

        if (isset($menuData) && $menuData === false) {
            throw new \Exception("父级菜单不存在，请刷新后重试");
        }

        if (isset($menuData) && !empty($menuData['parent_id'])) {
            throw new \Exception("数据错误，最多只允许两级");
        }

        $menuRows = $this->_menuRepository->getMenuList($menuInfo->account_id);

        $currentLevelItemsCount = count($this->filterArrayByLevel($menuRows, $menuInfo->level, $menuInfo->parent_id));

        if ($menuInfo->level == 0 && $currentLevelItemsCount > 2) {
            throw new \Exception("数据错误，一级菜单最多3个，请刷新后重试");
        }

        if ($menuInfo->level == 1 && $currentLevelItemsCount > 4) {
            throw new \Exception("数据错误，二级菜单最多5个，请刷新后重试");
        }
    }

    private function filterArrayByLevel($input, $level, $parentId)
    {
        $rtn = [];
        foreach ($input as $e) {
            if ($e['level'] == 0 && $e['level'] == $level) {
                $rtn[] = $e;
                continue;
            }

            if ($e['level'] == 1 && $e['level'] == $level && strtolower($e['parent_id']) == strtolower($parentId)) {
                $rtn[] = $e;
                continue;
            }
        }

        return $rtn;
    }


    /**
     * 将列表数组格式化为层级结构的数组
     * @param $tree
     * @param string $rootId
     * @return array
     */
    private function formatToArrayTree($tree, $rootId = '0')
    {
        $result = [];
        foreach ($tree as $leaf) {
            $leaf['items'] = null;
            $parentId = (isset($leaf['parent_id']) ? $leaf['parent_id'] : '0');
            if ($parentId == $rootId) {
                foreach ($tree as $subLeaf) {
                    if ($subLeaf['parent_id'] == $leaf['id']) {
                        $leaf['items'] = $this->formatToArrayTree($tree, $leaf['id']);
                    }
                }
                $result[] = $leaf;
            }
        }
        return $result;
    }

    /**
     * 设置菜单的排序
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $ids id集合：id按序排序逗号分隔从前端传入
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     * @return bool
     * @throws \Exception
     */
    public function setMenuSort($ids, $modified_by, $modified_on)
    {
        return $this->_menuRepository->setMenuSort($ids, $modified_by, $modified_on);
    }

    /**
     * 重设动作
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @param $id
     * @param $modified_by 修改者ID
     * @param $modified_on 修改时间
     */
    public function resetAction($id, $modified_by, $modified_on)
    {
        return $this->_menuRepository->resetAction($id, $modified_by, $modified_on);
    }
}