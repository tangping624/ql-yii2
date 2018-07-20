<?php

/*定义常量*/

/** -------- 会员类型 -----------**/
define('MEMBER_TYPE_PTHY', '普通会员');
define('MEMBER_TYPE_TZR', '同住人');
define('MEMBER_TYPE_YZ', '业主');
define('BIZ_CODE_SIGNIN_POINT', 'SignInPoint');

/** ----------积分说明 ------------------**/
define('MEMBER_POINT_SIGIN_TEXT', '微信登录积分');
define('MEMBER_POINT_AUTH_TEXT', '身份认证积分');
define('MEMBER_POINT_RECOMMEND_TEXT', '推荐入会积分');

/** ---------- 房产认证状态 ---------------- */
define('HOUSE_APPROVAL_STATUS_TODO', '待审核');
define('HOUSE_APPROVAL_STATUS_PASS', '已通过');
define('HOUSE_APPROVAL_STATUS_REFUSE', '已拒绝');

/** ---------- 项目级别level ----------------- */
define('PROJECT_LEVEL_BUILDING', 1);//楼盘
define('PROJECT_LEVEL_SUB_BUILDING', 2);//分期楼盘

/**
 * 缺省机构
 */
define('SUPER_ORGANIZATION_ID', '11b11db4-e907-4f1f-8835-b9daab6e1f23');

define('EARTH_RADIUS', 6371);

define('SMS_IDENTIFYING_CODE', 31742);