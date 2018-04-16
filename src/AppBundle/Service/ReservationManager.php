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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReservationManager
{
    protected $mailer;
    protected $session;
    protected $template;
    protected $entity;

    public function __construct(\Swift_Mailer $mailer, SessionInterface $session, \Twig_Environment $twig_Environment, EntityManagerInterface $entity){
        $this->mailer = $mailer;
        $this->session = $session;
        $this->template = $twig_Environment;
        $this->entity = $entity;
    }

    /**
     * Mets en Session l'objet Reservation
     * @param Reservation $reservation Objet Reservation
     */
    public function setSession(Reservation $reservation){
        $reservation
            ->setCodeReservation($this->generateCodeReservation())
            ->setDateReservation(new \DateTime());
        foreach ($reservation->getBillets() as $billet){
            $reservation->removeBillet($billet);
        }
        for ($i=0;$i<($reservation->getNbreBillet());$i++){
            $billet = new Billet();
            $reservation->addBillet($billet);
        }
        $this->setReservationSession($reservation);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertReservation(){
        $reservation = $this->getReservation();
        $this->entity->persist($reservation);
        foreach ($reservation->getBillets() as $billet){
            $this->entity->persist($billet);
        }
        $this->entity->flush();
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
        elseif ($billet->getTarifReduit()){
            return 10;
        }
        elseif ($age >= 60){
            return 12;
        }
        else{
            return 16;
        }
    }

    /**
     * @param $prix
     * @return string
     */
    public function getStringPromotion($prix){
        switch ($prix){
            case 0:
                return "Gratuit";
            case 8:
                return "Enfant";
            case 10:
                return "Tarif réduit";
            case 12:
                return "Sénior";
            default:
                return "Normal";
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

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function EnvoyerEmail(){
        $reservation = $this->getReservation();
        $tabPrix = array();
        $tabPromotion = array();
        $prixReservation = 0;
        foreach ($reservation->getBillets() as $billet){
            $prixBillet = $this->getPrixBillet($billet);
            array_push($tabPrix, $prixBillet);
            array_push($tabPromotion, $this->getStringPromotion($prixBillet));
            $prixReservation = $prixReservation + $prixBillet;
        }
            $mail = (new \Swift_Message('Votre Reservation pour le Musée du louvre'))
                ->setFrom('oc.projet.super@gmail.com')
                ->setTo($reservation->getEmail())
                ->setContentType('text/html')
                ->setBody($this->template->render('billetterie/email.html.twig', array(
                    'reservation' => $reservation,
                    'tarif' => $tabPrix,
                    'tabPromotion' => $tabPromotion,
                    'prixReservatin' => $prixReservation
                )));

        $this->mailer->send($mail);
    }

    /**
     * Génère un code de réservation
     * @return string Code de reservation généré
     */
    public function generateCodeReservation(){
        $code = '';
        $pool = array_merge(range(0,9),range('A', 'Z'));

        for($i=0; $i < 10; $i++) {
            $code .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $code;
    }
}