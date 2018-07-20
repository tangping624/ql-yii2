<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class CacheManagerController extends Controller
{
	public function actionRemoveOpenid($openid)
	{
		\app\framework\biz\cache\models\FanCacheObject::remove($openid);
		return 'remove openid: ' . $openid;
	}
}