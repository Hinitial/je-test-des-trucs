<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 09/04/18
 * Time: 13:59
 */

namespace AppBundle\Service;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Reservation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReservationManager
{
    protected $mailer;
    protected $session;
    protected $reservation;

    public function __construct(\Swift_Mailer $mailer, SessionInterface $session){
        $this->mailer = $mailer;
        $this->session = $session;
        $this->reservation = new Reservation();
    }

    public function setBilletSession(Reservation $reservation){
//        $this->reservation = $reservation;
//        $this->getReservationSession();
        for ($i=0;$i<($reservation->getNbreBillet());$i++){
            $billet = new Billet();
            $reservation->addBillet($billet);
        }
        $this->setReservationSession($reservation);
    }

    public function isReservation(){
        return $this->session->has('reservation');
    }

    public function setReservationSession($reservation){
        $this->session->set('reservation', $reservation);
    }

    public function getReservationSession(){
        $this->reservation = $this->session->get('reservation');
    }
}