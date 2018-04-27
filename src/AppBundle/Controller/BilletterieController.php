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
use AppBundle\Service\BookingManager;
use AppBundle\Service\StripeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class BilletterieController extends Controller
{
    /**
     * @Route("/billetterie", name="homepage_billetterie")
     */
    public function indexAction(BookingManager $reservationManager)
    {
        $reservationManager->initSession();
        return $reservationManager->getReponse(ReservationType::class);
    }

    /**
     * @Route("/billetterie/information", name="billetterie_information")
     */
    public function informationAction(BookingManager $reservationManager)
    {
        $reservationManager->throwException();
        return $reservationManager->getReponse(InformationType::class);
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     */
    public function paiementAction(BookingManager $reservationManager, StripeManager $stripeManager)
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
     * @param BookingManager $reservationManager
     * @param StripeManager $stripeManager
     * @param MailManager $mailManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     */
    public function checkoutAction(BookingManager $reservationManager, StripeManager $stripeManager, MailManager $mailManager)
    {
        $reservationManager->throwException();
        $stripeManager->initPayment();
        try {
            $stripeManager->makePayment();
            $reservationManager->setLastInformation();
            $reservationManager->insertBooking();
            $mailManager->mailTicketing($reservationManager->getTicketing());
            $stripeManager->clearPublicKey();
            return $this->redirectToRoute("billetterie_confirmation");
        } catch(\Stripe\Error\Card $e) {
            return $this->redirectToRoute("billetterie_paiement");
        }
    }

    /**
     * @Route("/billetterie/confirmation", name="billetterie_confirmation")
     * @param BookingManager $reservationManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     */
    public function confirmationAction(BookingManager $reservationManager)
    {
        $reservationManager->throwException();
        $reservationManager->clearBooking();
        return $reservationManager->getReponse();
    }
}