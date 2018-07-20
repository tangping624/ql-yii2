<?php
namespace app\modules\home\services;
use app\modules\home\repositories\CityRepository;

use app\modules\ServiceBase;
class CityService extends ServiceBase {

    private $_cityRepository;
    
    public function __construct(CityRepository $cityRepository)
    {
        $this->_cityRepository = $cityRepository;
    }

    public function getCity()
    {
        return $this->_cityRepository->getCity();
    }

    public function getCityByName($name)
    {
        return $this->_cityRepository->getCityByName($name);
    }

    public function locCityByName($name)
    {
        return $this->_cityRepository->locCityByName($name);
    }
}
