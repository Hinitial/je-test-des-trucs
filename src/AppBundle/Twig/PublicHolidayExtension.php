<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 02/05/18
 * Time: 17:12
 */

namespace AppBundle\Twig;


use AppBundle\Service\PublicHolidayManager;

class PublicHolidayExtension extends \Twig_Extension
{
    protected $holidayManager;

    public function __construct(PublicHolidayManager $holidayManager){
        $this->holidayManager = $holidayManager;
    }

    /**
     * Retourne la liste des jours fériés d'une année pécise
     * @param $year
     * @return array
     * @throws \Exception
     */
    public function getPublicHolidayTab($year){
        return $this->holidayManager->getPublicHolidayTab($year);
    }

    /**
     * Retourne la premier date d'un mois et d'une année précis
     * @param $month
     * @param $year
     * @return null
     * @throws \Exception
     */
    public function getFirstDate($month, $year){
        foreach ($this->getPublicHolidayTab($year) as $date){
            if ($date->format('n') == $month){
                return $date;
            }
        }
        return null;
    }

    /**
     * Retourne l'année actuel
     * @return string
     */
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