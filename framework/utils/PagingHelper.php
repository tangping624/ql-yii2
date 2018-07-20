<?php
namespace app\framework\utils;

class PagingHelper
{
    public static function getSkip($page, $pageSize)
    {
        return $pageSize * ($page - 1);
    }
}