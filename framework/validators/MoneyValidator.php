<?php
namespace app\framework\validators;

use yii\validators\Validator;

class MoneyValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!in_array($model->$attribute, ['USA', 'Web'])) {
            $this->addError($model, $attribute, 'The country must be either "USA" or "Web".');
        }
    }
}