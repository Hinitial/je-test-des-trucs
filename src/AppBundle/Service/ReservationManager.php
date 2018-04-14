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
    protected $template;

    public function __construct(\Swift_Mailer $mailer, SessionInterface $session, \Twig_Environment $twig_Environment){
        $this->mailer = $mailer;
        $this->session = $session;
        $this->template = $twig_Environment;
    }

    public function setBilletSession(Reservation $reservation){
        foreach ($reservation->getBillets() as $billet){
            $reservation->removeBillet($billet);
        }
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

    /**
     * @return mixed Retourne un Objet Reservation enregistrer dans la session
     */
    public function getReservation(){
        return ($this->session->get('reservation'));
    }


    /**
     * @param $index int numero du billet dans la Reservation
     * @return mixed Retourne un Objet Billet enregistrer en session
     */
    public function getBillet($index){
        $reservation = $this->getReservation();
        $billets = $reservation->getBillets();
        return $billets[$index];
    }

    /**
     * @param \DateTime $dateTime Date d'annivairsère
     * @return int Retourne l'age en fonction d'un DateTime
     */
    public function getAge(\DateTime $dateTime){
        $now = new \DateTime();
        $intervale = $now->diff($dateTime);
        $age = $intervale->format('%Y');
        return (int) ($age);

    }

    /**
     * @param $billet billet que l'on veux connaitre de le prix
     * @return int Le prix du billet voulu
     */
    public function getPrixBillet($billet){
        $age = $this->getAge($billet->getDateNaissance());
        if ($age <= 4){
            return 0;
        }
        elseif ($age <= 12){
            return 8;
        }
        elseif ($age >= 60){
            return 12;
        }
        elseif ($billet->getTarifReduit()){
            return 10;
        }
        else{
            return 16;
        }
    }

    /**
     * @return int Le prix total de la réservation
     */
    public function getPrixReservation(){
        $reservation = $this->getReservation();
        $prix = 0;
        foreach ($reservation->getBillets() as $billet){
            $prix = $prix + $this->getPrixBillet($billet);
        }
        return $prix;
    }

    public function EnvoyerEmail(){
        $reservation = $this->getReservation();
        $tabPrix = array();
        foreach ($reservation->getBillets() as $billet){
            array_push($tabPrix, $this->getPrixBillet($billet));
        }
            $mail = (new \Swift_Message('Votre Reservation pour le Musée du louvre'))
                ->setFrom('oc.projet.super@gmail.com')
                ->setTo($reservation->getEmail())
                ->setContentType('text/html')
                ->setBody($this->template->render('billetterie/email.html.twig', array(
                    'jourVisite' => $reservation->getJourVisite(),
                    'billets' => $reservation->getBillets(),
                    'tarif' => $tabPrix,
                    'codeReservation' => $reservation->getCodeReservation()
                )));

        $this->mailer->send($mail);
    }
}