<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionNotin()
    { 
       return $this->renderPartial('404'); 
    }
    public function actionError()
    {
        /* @var $exception \Exception */
        $exception = Yii::$app->getErrorHandler()->exception;
        if (Yii::$app->getRequest()->getIsAjax()) {
            return $this->_convertExceptionToString($exception);
        } else {
            if (YII_ENV != 'release' && YII_ENV != 'prod') {
                $trace = $exception->getTraceAsString();
            } else {
                $trace = '';
            }
            return $this->renderPartial('error', [
                'title' => $exception ? $exception->getMessage() : 'exception',
                    'trace' => $trace,
                ]);
        }
    }


    /**
     * @param \Exception $exception
     * @return string
     */
    private function _convertExceptionToString($exception)
    {
        $summary = $exception->getMessage();

        if (!YII_DEBUG) {
            $message = "Error: {$summary}";
        } else {
            $message = "exception:{$summary}";
            $message .= " '" . get_class($exception) . "' with message '{$summary}' \n\nin "
                . $exception->getFile() . ':' . $exception->getLine() . "\n\n"
                . "Stack trace:\n" . $exception->getTraceAsString();
        }

        return $message;
    }
}
