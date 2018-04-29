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
    public function indexAction(BookingManager $bookingManager)
    {
        $bookingManager->initSession();
        return $bookingManager->getReponse(ReservationType::class);
    }

    /**
     * @Route("/billetterie/information", name="billetterie_information")
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     * @throws \Exception
     */
    public function informationAction(BookingManager $bookingManager)
    {
        $bookingManager->throwException();
        $bookingManager->verifyStep('step_1');
        return $bookingManager->getReponse(InformationType::class);
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     * @throws \Exception
     */
    public function paymentAction(BookingManager $bookingManager, StripeManager $stripeManager)
    {
        $bookingManager->throwException();
        $bookingManager->verifyStep('step_2');
        $stripeManager->initPublicKey();
        return $bookingManager->getReponse();
    }

    /**
     * @Route(
     *     "/checkout",
     *     name="order_checkout",
     *     methods="POST"
     * )
     * @param BookingManager $bookingManager
     * @param StripeManager $stripeManager
     * @param MailManager $mailManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function checkoutAction(BookingManager $bookingManager, StripeManager $stripeManager, MailManager $mailManager)
    {
        $bookingManager->throwException();
        $stripeManager->initPayment();
        try {
            $stripeManager->makePayment();
            $bookingManager->setLastInformation();
            $bookingManager->insertBooking();
            $mailManager->mailTicketing($bookingManager->getBooking());
            $stripeManager->clearPublicKey();
            return $this->redirectToRoute("billetterie_confirmation");
        } catch(\Stripe\Error\Card $e) {
            return $this->redirectToRoute("billetterie_paiement");
        }
    }

    /**
     * @Route("/billetterie/confirmation", name="billetterie_confirmation")
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     * @throws \Exception
     */
    public function confirmationAction(BookingManager $bookingManager)
    {
        $bookingManager->throwException();
        $bookingManager->verifyStep('step_3');
        $bookingManager->clearBooking();
        return $bookingManager->getReponse();
    }
}