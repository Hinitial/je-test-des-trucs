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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketingController extends Controller
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
        $bookingManager->throwException('step_1');
        $booking = $bookingManager->getBooking();
        return $bookingManager->getReponse(InformationType::class);
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     * @throws \AppBundle\Exceptions\SessionNotFoundException
     * @throws \Exception
     */
    public function paymentAction(BookingManager $bookingManager)
    {
        $bookingManager->throwException('step_2');
        $booking = $bookingManager->getBooking();
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
        $bookingManager->throwException('step_2');
        $stripeManager->initPayment();
        try {
            $stripeManager->makePayment();
            $booking = $bookingManager->getBooking();
            $bookingManager->setLastInformation($booking);
            $bookingManager->insertBooking($booking);
            $mailManager->mailTicketing($bookingManager->getBooking());
            return $this->redirectToRoute('billetterie_confirmation');
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
        $bookingManager->throwException('step_3');
        $booking =  $bookingManager->getBooking();
        $bookingManager->clearBooking();
        return $this->render('billetterie/confirmation.html.twig', array(
            'code' => $booking->getBookingCode(),
            'email' => $booking->getEmail()
        ));
    }
}