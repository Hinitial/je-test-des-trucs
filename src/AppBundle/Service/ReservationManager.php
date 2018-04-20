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
        foreach ($reservation->getBillets() as $billet){
            $billet->setPays('FR');
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function EnvoyerEmail(){
        $reservation = $this->getReservation();
        $mail = (new \Swift_Message('Votre Reservation pour le Musée du louvre'))
            ->setFrom('oc.projet.super@gmail.com')
            ->setTo($reservation->getEmail())
            ->setContentType('text/html')
            ->setBody($this->template->render('billetterie/email.html.twig'));

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

    public function clearReservation(){
        $this->session->remove('reservation');
    }
}