<?php

namespace app\framework\utils;


class ArrayHelper extends \yii\helpers\ArrayHelper
{
    public static function mergeArray($a, $b)
    {
        foreach ($b as $k => $v) {
            if (is_integer($k)) {
                $a[] = $v;
            } elseif (is_array($v) && isset($a[$k]) && is_array($a[$k])) {
                $a[$k] = self::mergeArray($a[$k], $v);
            } else {
                $a[$k] = $v;
            }
        }
        return $a;
    }

    /**
     * 支持对数组按某个key或属性进行分组
     * @param array $arr
     * @param callable|string $key_selector
     * @return array
     */
    public static function group(array $arr, $key_selector)
    {
        if (!isset($arr))
            return null;

        $isSelector = is_callable($key_selector);
        $result = array();
        foreach ($arr as $i) {
            if ($isSelector) {
                $key = call_user_func($key_selector, $i);
                if ($key) {
                    $result[$key][] = $i;
                }
            } else {
                $key =$i[$key_selector];
                $result[$key][] = $i;
            }
        }
        return $result;
    }

    public static function select(array $arr, callable $selector)
    {
        if (!isset($arr))
            return null;

        $result = array();
        foreach ($arr as $item) {
            $d = call_user_func($selector, $item);
            if (isset($d)) {
                $result[] = $d;
            }
        }
        return $result;
    }

    /**
     * 过滤数组
     * eg:
     * $dict: ['id'=>1, 'name' => 'car', 'price' => 111.00]
     * $allow: ['name', 'type', 'id']
     * return=> ['name'=>'car', 'id'=>1]
     * @param array $dict 输入
     * @param array $allow 有效的字段,为空则直接返回$dict
     * @return array
     */
    public static function filterDict($dict, $allow=[])
    {
        if (empty($dict)) {
            return [];
        }

        if (empty($allow)) {
            return $dict;
        }

        return array_intersect_key($dict, array_flip($allow));
    }

    /**
     * 造成查询条件, 字段值为空字符，则不会在查询结果出现
     * eg:
     * $dict: ['id'=>1, 'name' => '', 'price' => 111.00]
     * $allow: ['name', 'type', 'id']
     * return=> ['id'=>1]
     * @param array $post 输入
     * @param array $allow 有效的字段,为空则直接返回$dict
     * @return array
     */
    public static function toCondition($post, $allow=[])
    {
        if (empty($post)) {
            return [];
        }

        foreach ($post as $k => $v) {
            if ($v == '') {
                unset($post[$k]);
            }
        }

        if (empty($allow)) {
            return $post;
        }

        return array_intersect_key($post, array_flip($allow));
    }

    /**
     * 将数组转化成字典
     * @param array $arr
     * @param string $key
     * @return array
     */
    public static function toDict($arr, $key)
    {
        if (empty($arr)) {
            return [];
        }
        if (empty($key)) {
            throw new \InvalidArgumentException('$key');
        }
        $dict = [];
        foreach ($arr as $item) {
            $dict[$item[$key]] = $item;
        }
        return $dict;
    }

}