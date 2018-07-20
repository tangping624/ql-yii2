<?php
namespace app\modules\pub\utils;

use app\framework\utils\DateTimeHelper;

class DateUtility {
    /*
     * 获取日期所在月份的第一天
     */

    public static function getMonthFirstDate($date) {
        return DateTimeHelper::format($date, "Y-m") . "-01";
    }

    /*
     * 获取日期所在月份的最后一天
     */

    public static function getMonthEndDate($date) {
        $lastMonthFirstDate = DateUtility::getLastMonthFirstDate($date);
        return date('Y-m-d', strtotime('-1 day', strtotime($lastMonthFirstDate)));
    }

    /*
     * 获取当前日期下月第一天
     */

    public static function getLastMonthFirstDate($date) {
        return date('Y-m-d', strtotime('+1 month', strtotime(DateUtility::getMonthFirstDate($date))));
    }

    /*
     * 获取当前日期下月最后一天
     */

    public static function getLastMonthEndDate($date) {
        return DateUtility::getMonthEndDate(DateUtility::getLastMonthFirstDate($date));
    }

    /*
     * 获取当前日期的天
     */

    public static function getDateDay($date) {
        return ((int) substr($date, 8, 2));
    }

    /*
     * 获取前一天
     */

    public static function getPreDate($date) {
        return date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }

    /*
     *  获取后一天
     */

    public static function getAfterDate($date) {
        return date('Y-m-d', strtotime('+1 day', strtotime($date)));
    }

    /*
     * 获取当前日期加一个月，php 中1月31 + 1月 = 3月3日，此处理为 2月28日
     */

    public static function getDateAddOneMonth($date) {
        $lastmonthenddate = DateUtility::getLastMonthEndDate($date);
        $day = DateUtility::getDateDay($date);
        if ($day >= DateUtility::getDateDay($lastmonthenddate)) {
            return $lastmonthenddate;
        } else {
            if (strlen($day) == 1) {
                $day = "0" . $day;
            }
            return DateUtility::getPreDate(DateTimeHelper::format($lastmonthenddate, "Y-m") . '-' . $day);
        }
    }

    /*
     * 获取两个时间之间的天数
     */

    public static function getDateDiffDay($bgndate, $enddate) {
        if (empty($bgndate) || empty($enddate)) {
            throw new \InvalidArgumentException('$bgnDate-$enddate');
        }
        $tempbgndate = min([$bgndate, $enddate]);
        $tempenddate = max([$bgndate, $enddate]);
        return round((strtotime($tempenddate) - strtotime($tempbgndate)) / 3600 / 24) + 1;
    }

    /*
     * 获取1年的天数
     */

    public static function getYearDays($date) {
        if (empty($date)) {
            throw new \InvalidArgumentException('$date');
        }
        $year = substr($date, 0, 4);
        return round((strtotime($year . "-12-31") - strtotime($year . "-01-01")) / 3600 / 24) + 1;
    }

    /*
     * 时间格式化
     */

    public static function format($format = "Y-m-d H:i:s", $timezone = 'PRC') {
        date_default_timezone_set($timezone); 
        return date($format, time());
    }
    /*
     * 时间增加一天
     */
    public static function getAddOneDay($date){
        return strtotime('2 day', $date);
    }
    
}
