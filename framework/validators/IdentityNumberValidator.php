<?php
namespace app\framework\validators;

use yii\validators\Validator;

/**
 * http://www.yiiframework.com/doc-2.0/guide-input-validation.html
 * 证件号验证 *
 */
class IdentityNumberValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        return;
    }

    /**
    public function validateAttribute($model, $attribute)
    {
        if (!in_array($model->$attribute, ['USA', 'Web'])) {
        $this->addError($model, $attribute, 'The country must be either "USA" or "Web".');
        }
    }
     */
}