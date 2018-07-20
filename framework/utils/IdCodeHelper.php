<?php
/**
 * Created by PhpStorm.
 * User: zhangsl04
 * Date: 2016/2/26
 * Time: 14:28
 */
namespace app\framework\utils;

class IdCodeHelper
{
    /**
     * 根绝身份证提取生日日期
     * @param $cardID
     * @return string
     */
    public static function getBirthdayFromCardNum($cardID)
    {
        $cardID = trim($cardID);

        // 校验
        if (empty($cardID) || (strlen($cardID) != 18 && strlen($cardID) != 15)) {
            return '';
        }

        $birth = '';
        if (strlen($cardID) == 18) {
            $birth = substr($cardID, 6, 8);
        } else if (strlen($cardID) == 15) {
            $birth = '19' . substr($cardID, 6, 6);
        }
        return substr($birth, 0, 4) . '-' . substr($birth, 4, 2) . '-' . substr($birth, 6, 2);
    }

    /**
     * 根据身份证号码提取性别
     * @param $cardID
     * @return string 男\女\未知
     */
    public static function getSexFromCardNum($cardID)
    {
        $cardID = trim($cardID);

        $sex = 0;
        if (strlen($cardID) == 18) {
            $sex = intval(substr($cardID, 16, 1));
        } else if (strlen($cardID) == 15) {
            $sex = intval(substr($cardID, 14, 1));
        }
        $rtn = $sex > 0 ? ($sex % 2 == 0 ? 2 : 1) : 0;
        if ($rtn == 1) {
            return '男';
        } elseif ($rtn == 2) {
            return '女';
        } else {
            return '未知';
        }
    }

    /**
     * @param $idCard
     * @return 成功返回18位id|string
     */
    public static function getAnotherStyle($idCard)
    {
        $idCard = trim($idCard);

        $len = strlen($idCard);
        if ($len != 15 && $len != 18) {
            return $idCard;
        }

        if ($len == 15) {
            return self::idCode15to18($idCard);
        }

        return substr($idCard, 0, 6) . substr($idCard, 8, 9);
    }

    /**
     * @param $idCard
     * @param bool|true $returnFalse
     * @return bool|string
     */
    public static function idCode15to18($idCard, $returnFalse = true)
    {
        $idCard = trim($idCard);

        $len = strlen($idCard);
        if ($len != 15 && $len != 18) {
            return $returnFalse ? false : $idCard;
        }

        if ($len == 18) {
            //末位码校验
            if (substr($idCard, 17) !== self::getCheckCode($idCard)) {
                return $returnFalse ? false : $idCard;
            }

            return $idCard;
        }

        $idCard18 = substr ( $idCard, 0, 6 ) . "19" . substr ( $idCard, 6 );

        return $idCard18 . self::getCheckCode($idCard18);
    }

    /**
     * 检验身份证的合法性
     * @param $idCode
     */
    public static function checkValid($idCode)
    {
        $idCode = trim($idCode);

        $len = strlen($idCode);
        if ($len != 18 && $len != 15) {
            return false;
        }

        if ($len == 15 && !preg_match('/^[0-9]{15}$/', $idCode) || $len == 18 && !preg_match('/^[0-9]{17}[0-9X]$/', $idCode)) {
            return false;
        }

        //末位校验码检验
        if ($len == 18 && self::getCheckCode($idCode) != substr($idCode, 17, 1)) {
            return false;
        }

        return true;
    }

    private static function getCheckCode($idCard)
    {
        $idCard = trim($idCard);

        $len = strlen($idCard);
        if ($len < 17) {
            return false;
        }

        $W = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2,1];
        $A = ["1","0","X","9","8","7","6","5","4","3","2"];
        $s = 0;
        for($i = 0; $i < 17; $i++) {
            $s += intval(substr ( $idCard, $i, 1 )) * $W [$i];
        }

        return $A [$s % 11];
    }
}
