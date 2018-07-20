<?php

namespace app\framework\utils;

use DateTime;

class DateTimeHelper
{
    public static function short($time, $split = '/')
    {
        if (!$time) {
            return '';
        }
        return date("Y" . $split . "m" . $split . "d", strtotime($time));
    }

    /**
     * 当前系统时间，精确到秒
     * @param string $timezone 时区
     * @return bool|string
     */
    public static function now($timezone = 'PRC')
    {
        date_default_timezone_set($timezone);

        return date('Y-m-d H:i:s', time());
    }

    /**
     * 当前系统时间
     * @param string $timezone
     * @return string
     */
    public static function nowMicro($timezone = 'PRC')
    {
        date_default_timezone_set($timezone);
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        return $d->format("Y-m-d H:i:s.u");
    }

    /*
    * 时间戳转日期
    */
    public static function timestamp2datetime($stamp, $timezone = 'PRC')
    {
        date_default_timezone_set($timezone);

        return date('Y-m-d H:i:s', $stamp);
    }

    public static function format($time = '', $format = "Y-m-d H:i:s")
    {
        if (!$time) {
            $time = self::now();
        }
        if (!$format) {
            $format = "Y-m-d H:i:s";
        }
        return date($format, strtotime($time));
    }

    /**
     * @param $timestamp1
     * @param string $timestamp2
     * @return string
     */
    public static function diffTimestamp($timestamp1, $timestamp2 = '')
    {
        if (empty($timestamp2)) {
            $timestamp2 = time();
        }

        $startTime = min([$timestamp1, $timestamp2]);
        $endTime = max([$timestamp1, $timestamp2]);

        $timeDiff = $endTime - $startTime;
        $timeDiff_d = floor($timeDiff / 86400);

        $timeDiff -= $timeDiff_d * 86400;
        $timeDiff_h = floor($timeDiff / 3600);

        $timeDiff -= $timeDiff_h * 3600;
        $timeDiff_m = floor($timeDiff / 60);

        return $timeDiff_d . '天' . $timeDiff_h . '小时' . $timeDiff_m . '分';
    }

    /**
     * 生日算年龄
     * @param string $birthday
     * @return bool|null|string
     */
    public static function convertAge($birthday)
    {
        if (empty($birthday)) {
            return null;
        }
        $age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;
        if (date('m', time()) == date('m', strtotime($birthday))) {
            if (date('d', time()) >= date('d', strtotime($birthday))) {
                $age++;
            }
        } elseif (date('m', time()) > date('m', strtotime($birthday))) {
            $age++;
        }
        return $age;
    }

    public static function getWeekdayFromDate($date = null)
    {
        if ($date === null) {
            $date = time();
        }
        if (!is_int($date)) {
            $date = strtotime($date);
        }
        if (!$date) {
            return false;
        }

        $w = ['日', '一', '二', '三', '四', '五', '六'];

        return '星期' . $w[date('w', $date)];
    }

    /**
     * 获得日期天数差
     * @param $date1
     * @param $date2
     * @param bool $ceil
     * @return float
     */
    public static function getDayDiff($date1, $date2, $ceil = true)
    {
        $date1 = self::format($date1, 'Y-m-d');
        $date2 = self::format($date2, 'Y-m-d');
        $date1_stamp = strtotime($date1);
        $date2_stamp = strtotime($date2);
        return $ceil ? ceil(($date2_stamp - $date1_stamp) / 86400) : round(($date2_stamp - $date1_stamp) / 86400);
    }

    /**
     * 日期格式检查(5.3以上版本支持)
     * @param $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
