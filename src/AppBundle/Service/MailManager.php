<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 26/04/18
 * Time: 15:35
 */

namespace AppBundle\Service;


use AppBundle\Entity\Contact;
use AppBundle\Entity\Booking;

class MailManager
{
    protected $mailer;
    protected $twig_Environment;
    protected $mail_sender;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig_Environment, $mail_sender){
        $this->mailer = $mailer;
        $this->twig_Environment = $twig_Environment;
        $this->mail_sender = $mail_sender;
    }

    /**
     * @param Contact $contact
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function mailContact(Contact $contact){
        $mail = new  \Swift_Message($contact->getTitre());
        $mail = $mail
            ->setFrom($contact->getEmail())
            ->setTo($this->mail_sender)
            ->setContentType('text/html')
            ->setBody($this->twig_Environment->render('email/contact.html.twig', array(
                'nom' => $contact->getNom(),
                'prenom' => $contact->getPrenom(),
                'message' => $contact->getMessage()
            )));

        $this->mailer->send($mail);
    }

    /**
     * @param Booking $ticketing
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function mailTicketing(Booking $ticketing){
        $mail = new  \Swift_Message('Votre Reservation pour le Musée du louvre');
        $mail = $mail
            ->setFrom($this->mail_sender)
            ->setTo($ticketing->getEmail())
            ->setContentType('text/html')
            ->setBody($this->twig_Environment->render('email/reservation.html.twig', array(
                'logoMusee' => $mail->embed(\Swift_Image::fromPath('images/Logo.jpg')),
                'logoSombre' => $mail->embed(\Swift_Image::fromPath('images/louvre-1eravril.jpg'))
            )));

        $this->mailer->send($mail);
    }
}