<?php
namespace app\entities;
/**
 * @todo Description
 * @author fanwq
 */
use app\entities\PEntityBase;

class PMassMsgAuth extends PEntityBase
{
    public static function tableName() {
        parent::tableName();
        return 'p_mass_msg_auth';
    }
}
