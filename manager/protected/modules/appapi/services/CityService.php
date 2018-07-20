<?php
namespace app\modules\appapi\services;
use app\modules\appapi\repositories\CityRepository;

use app\modules\ServiceBase;
class CityService extends ServiceBase {

    private $_cityRepository;
    
    public function __construct(CityRepository $cityRepository)
    {
        $this->_cityRepository = $cityRepository;
    }



    public function locCityByName()
    {
        return $this->_cityRepository->locCityByName();
    }
}
