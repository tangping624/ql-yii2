<?php
namespace app\services;

use app\entities\AppDbScriptChild;
use app\entities\AppDbScriptEntity;
use app\entities\AppVersion;
use app\entities\AppDownloadUrl;
use app\entities\AppWebVersion;

class AppVersionService
{
    /**获取版本信息
     * @param $platform
     * @return mixed
     */
    public static function getAppVersionInfo($platform)
    {
        return AppVersion::find()
            ->select('version_code,update_info,update_type')
            ->where(['is_active' => 1, 'platform' => $platform])
            ->orderBy('release_date desc')
            ->createCommand()
            ->queryOne();
    }

    /**获取下载地址
     * @param $platform
     * @param $app_code
     * @return mixed
     */
    public static function getAppDownloadUrl($platform)
    {
        return AppDownloadUrl::find()
            ->select('download_url')
            ->where(['platform' => $platform])
            ->scalar();
    }

    /**
     * 获取web版本信息
     * @param $platform {string} 平台
     * @param $app_code {string} 应用代码
     * @param $app_version_code {string} app版本
     * @return array
     */
    public static function getWebVersionInfo($platform, $app_version_code)
    {
        $appVersionColumn = strtoupper($platform) === 'IOS' ?
            'app_version_code_ios' : 'app_version_code_android';

        return AppWebVersion::find()
            ->select('version_code,download_url')
            ->where([
                'is_deleted' => 0,
                $appVersionColumn => $app_version_code 
            ])
            ->orderBy('release_date desc')
            ->asArray()
            ->one();
    }

    /**
     * 获取app数据库升级脚本信息
     * @param $app_code
     * @param $current_db_version_code
     * @param $target_db_version_code
     * @return mixed
     */

    public static function getAppDbScript( $current_db_version_code, $target_db_version_code)
    {
        $data = AppDbScriptEntity::find()
            ->select('s.id,s.version_code,s.scene,s.table_name,s.column_name,s.sql_text')
            ->from(' m_app_db_script s ') 
            ->andWhere(
                ' s.version_code > :code_start
                    and s.version_code <= :code_end
                    and s.is_deleted=0
                ',
                [
                    ':code_start' => $current_db_version_code,
                    ':code_end' => $target_db_version_code
                ]
            )
            ->orderBy('s.sort')
            ->createCommand()
            ->queryAll();

        return self::rebuildAppDbScriptData($data);
    }




    /**
     * 根据脚本id获取子脚本Sql数组
     * @param $scriptId string 脚本id
     * @return array
     */
    private static function getChildScriptSQLsByScriptId($scriptId)
    {
        return AppDbScriptChild::find()
            ->select('sql_text')
            ->where(['script_id' => $scriptId, 'is_deleted' => 0])
            ->orderBy('sort')
            ->column();
    }


    /**
     * 重新构建app数据库升级脚本信息
     * @param $app_db_script_data
     * @return array
     */
    private static function rebuildAppDbScriptData($app_db_script_data)
    {
        //针对列操作的场景
        $column_operate_scene_list = [
            'add_column', 'drop_column', 'alter_column', 'data_update'
        ];

        $result = [];
        foreach ($app_db_script_data as $value) {

            // 处理sql数组
            $SQLs = $value['sql_text'];
            if (empty($SQLs)) {
                // 如果sql列为空则通过从表获取sql
                $SQLs = self::getChildScriptSQLsByScriptId($value['id']);
                if (empty($SQLs)) {
                    continue;
                }
            } else {
                $SQLs = explode(';', trim($value['sql_text'], ';'));
            }

            $temp = [
                'sql' => $SQLs
            ];

            // 处理sql条件
            $table_name = $value['table_name'];
            $column_name = $value['column_name'];
            if (!empty($table_name) && !empty($column_name)) {
                if (in_array($value['scene'], $column_operate_scene_list)) {
                    if ($value['scene'] == 'add_column') {
                        $temp['exec_if_not_exists_column'] = [
                            'table_name' => $table_name,
                            'column_name' => $column_name
                        ];
                    } else {
                        $temp['exec_if_exists_column'] = [
                            'table_name' => $table_name,
                            'column_name' => $column_name
                        ];
                    }
                }
            }

            $result[] = $temp;
        }

        return $result;
    }
}