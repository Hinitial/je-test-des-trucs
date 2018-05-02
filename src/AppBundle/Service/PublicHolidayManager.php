<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 02/05/18
 * Time: 16:08
 */

namespace AppBundle\Service;


class PublicHolidayManager
{
    public function getPublicHolidayTab($year){
        $tab = array(
            'nouvel_ans' => new \DateTime($year.'-01-01'),
            'dimanche_paque' => $this->get_easter_datetime($year),
            'lundi_paque' => $this->get_easter_datetime($year)->modify('+1 day'),
            'jeudi_ascension' => $this->get_easter_datetime($year)->modify('+39 day'),
            'dimanche_pentecote' => $this->get_easter_datetime($year)->modify('+49 day'),
            'lundi_pentecote' => $this->get_easter_datetime($year)->modify('+50 day'),
            'fete_travail' => new \DateTime($year.'-05-01'),
            '8mai' => new \DateTime($year.'-05-08'),
            'fete_nationale' => new \DateTime($year.'-07-14'),
            'assomption' => new \DateTime($year.'-08-15'),
            'toussaint' => new \DateTime($year.'-11-01'),
            'armistice' => new \DateTime($year.'-11-11'),
            'noel' => new \DateTime($year.'-12-25'),
        );
        return $tab;
    }

    public function get_easter_datetime($year) {
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);

        return $base->add(new \DateInterval("P{$days}D"));
    }
}