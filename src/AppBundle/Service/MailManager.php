<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 26/04/18
 * Time: 15:35
 */

namespace AppBundle\Service;


use AppBundle\Entity\Contact;
use AppBundle\Entity\Reservation;

class MailManager
{
    protected $mailer;
    protected $twig_Environment;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig_Environment){
        $this->mailer = $mailer;
        $this->twig_Environment = $twig_Environment;
    }

    public function mailContact(Contact $contact){
        $mail = new  \Swift_Message($contact->getTitre());
        $mail = $mail
            ->setFrom('oc.projet.super@gmail.com')
            ->setTo($contact->getEmail())
            ->setContentType('text/html')
            ->setBody($this->twig_Environment->render('email/reservation.html.twig', array(
                'nom' => $contact->getNom(),
                'prenom' => $contact->getPrenom(),
                'message' => $contact->getMessage()
            )));

        $this->mailer->send($mail);
    }

    public function mailReservation(Reservation $reservation){
        $mail = new  \Swift_Message('Votre Reservation pour le Musée du louvre');
        $mail = $mail
            ->setFrom('oc.projet.super@gmail.com')
            ->setTo($reservation->getEmail())
            ->setContentType('text/html')
            ->setBody($this->twig_Environment->render('email/reservation.html.twig', array(
                'logoMusee' => $mail->embed(\Swift_Image::fromPath('images/Logo.jpg')),
                'logoSombre' => $mail->embed(\Swift_Image::fromPath('images/louvre-1eravril.jpg'))
            )));

        $this->mailer->send($mail);
    }
}