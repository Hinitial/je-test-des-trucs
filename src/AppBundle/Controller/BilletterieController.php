<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/03/18
 * Time: 00:38
 */

namespace AppBundle\Controller;


use AppBundle\Form\InformationType;
use AppBundle\Form\ReservationType;
use AppBundle\Service\MailManager;
use AppBundle\Service\ReservationManager;
use AppBundle\Service\StripeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class BilletterieController extends Controller
{
    /**
     * @Route("/billetterie", name="homepage_billetterie")
     */
    public function indexAction(ReservationManager $reservationManager)
    {
        $reservationManager->initSession();
        return $reservationManager->getReponse(ReservationType::class);
    }

    /**
     * @Route("/billetterie/information", name="billetterie_information")
     */
    public function informationAction(ReservationManager $reservationManager)
    {
        $reservationManager->throwException();
        return $reservationManager->getReponse(InformationType::class);
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     */
    public function paiementAction(ReservationManager $reservationManager, StripeManager $stripeManager)
    {
        $reservationManager->throwException();
        $stripeManager->initPublicKey();
        return $reservationManager->getReponse();
    }

    /**
     * @Route(
     *     "/checkout",
     *     name="order_checkout",
     *     methods="POST"
     * )
     * @param ReservationManager $reservationManager
     * @param StripeManager $stripeManager
     * @param MailManager $mailManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     */
    public function checkoutAction(ReservationManager $reservationManager, StripeManager $stripeManager, MailManager $mailManager)
    {
        $reservationManager->throwException();
        $stripeManager->initPayment();
        try {
            $stripeManager->makePayment();
//            $reservationManager->insertReservation();
            $mailManager->mailReservation($reservationManager->getReservation());
            $stripeManager->clearPublicKey();
            return $this->redirectToRoute("billetterie_confirmation");
        } catch(\Stripe\Error\Card $e) {
            return $this->redirectToRoute("billetterie_paiement");
        }
    }

    /**
     * @Route("/billetterie/confirmation", name="billetterie_confirmation")
     * @param ReservationManager $reservationManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     */
    public function confirmationAction(ReservationManager $reservationManager)
    {
        $reservationManager->throwException();
        $reservationManager->clearReservation();
        return $reservationManager->getReponse();
    }
}