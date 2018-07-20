<?php
namespace app\framework\settings;

use yii\db\Query;

use app\framework\db\EntityBase;
use app\framework\db\SqlHelper;
use app\framework\settings\interfaces\SettingsProviderInterface;

class SettingsProvider implements SettingsProviderInterface
{
    /**
     * @inheritdoc
     */
    public function upset($key, $value, $description)
    {
        $conn = EntityBase::getDb();
        $query = new Query();
        $row = $query->from('config')->where(['name' => $key])
            ->select('value')
            ->one($conn);

        if ($row == false) {
            $result = SqlHelper::insert('config', $conn, ['name' => $key,
                'value' => $value,
                'description' => $description,
                'created_by' => 'system',
                'modified_by' => 'system'
            ]);
        } else {
            $result = SqlHelper::update('config', $conn, ['value' => $value, 'description' => $description,
                'created_by' => 'system',
                'modified_by' => 'system'
            ], ['name' => $key]);
        }
        return $result > 0;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        if (empty($key)) {
            return null;
        }

        $conn = EntityBase::getDb();
        $query = new Query();
        $dataRow = $query->from('config')->where(['name' => $key])->select(['name', 'value', 'description'])
            ->one($conn);
        if ($dataRow == false) {
            return null;
        } else {
            return $dataRow;
        }
    }
}