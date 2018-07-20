<?php
namespace app\modules\Ctrip\services;

use app\modules\ctrip\repositories\CtripRepository;
use app\modules\ServiceBase;

class CtripService extends ServiceBase
{

    private $_ctripRepository;

    public function __construct(CtripRepository $ctripRepository)
    {
        $this->_ctripRepository = $ctripRepository;
    }


}
