<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 27/04/18
 * Time: 16:21
 */

namespace AppBundle\Service;


use AppBundle\Entity\Ticket;

class PriceTicketManager
{
    const PRIX_GRATUIT = 0;
    const PRIX_NORMAL = 16;
    const PRIX_SENIOR = 12;
    const PRIX_TARIF_REDUIT = 10;
    const PRIX_ENFANT = 8;

    const AGE_GRATUIT_MAX = 4;
    const AGE_ENFANT_MAX = 12;
    const AGE_SENIOR_MIN = 60;

    const LABEL_GRATUIT = 'Gratuit';
    const LABEL_NORMAL = 'Normal';
    const LABEL_SENIOR = 'Sénior';
    const LABEL_TARIF_REDUIT = 'Tarif réduit';
    const LABEL_ENFANT = 'Enfant';

    public function __construct(){
    }

    /**
     * Retourne le prix d'un billet,à partir de lapolitique budgetetaire actuel
     * @return int Le prix du billet
     */
    public function getTicketPrice(Ticket $ticket){
        $age = $ticket->getAge();
        $price = 0.00;
        if ($age <= self::AGE_GRATUIT_MAX){
            $price = self::PRIX_GRATUIT;
        }
        elseif ($age <= self::AGE_ENFANT_MAX){
            $price = self::PRIX_ENFANT;
        }
        elseif ($ticket->getReducPrice()){
            $price = self::PRIX_TARIF_REDUIT;
        }
        elseif ($age >= self::AGE_SENIOR_MIN){
            $price = self::PRIX_SENIOR;
        }
        else{
            $price = self::PRIX_NORMAL;
        }
        $ticket->setPrice($price);
    }
}