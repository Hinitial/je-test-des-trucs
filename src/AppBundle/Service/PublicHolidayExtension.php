<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 02/05/18
 * Time: 17:12
 */

namespace AppBundle\Service;


class PublicHolidayExtension extends \Twig_Extension
{
    protected $holidayManager;

    public function __construct(PublicHolidayManager $holidayManager){
        $this->holidayManager = $holidayManager;
    }

    public function getPublicHolidayTab($year){
        return $this->holidayManager->getPublicHolidayTab($year);
    }

    public function getFirstDate($month, $year){
        foreach ($this->getPublicHolidayTab($year) as $date){
            if ($date->format('n') == $month){
                return $date;
            }
        }
        return null;
    }

    public function getCurrentYear(){
        $currentDay = new \DateTime();
        return $currentDay->format('Y');
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getPublicHoliday', array($this, 'getPublicHolidayTab')),
            new \Twig_SimpleFunction('getFirstDate', array($this, 'getFirstDate')),
            new \Twig_SimpleFunction('getCurrentYear', array($this, 'getCurrentYear')),
        );
    }

    public function getName()
    {
        return 'PublicHoliday';
    }
}