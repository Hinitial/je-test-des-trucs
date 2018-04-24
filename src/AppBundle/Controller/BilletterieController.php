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
use AppBundle\Service\ReservationManager;
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
        $reservationManager->throwException();
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
    public function paiementAction(ReservationManager $reservationManager)
    {
        $reservationManager->throwException();
        return $reservationManager->getReponse();
    }

    /**
     * @Route(
     *     "/checkout",
     *     name="order_checkout",
     *     methods="POST"
     * )
     * @param ReservationManager $reservationManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function checkoutAction(ReservationManager $reservationManager)
    {
        $reservationManager->throwException();
        \Stripe\Stripe::setApiKey("sk_test_rTE16Sgt73ezOF1XCqy76TLg");

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create a charge: this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => ($reservationManager->getReservation()->getPrixReservation())*100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Musee du louvre"
            ));

            $reservationManager->insertReservation();
            $reservationManager->EnvoyerEmail();
            $this->addFlash("success","Paiement validé");

            return $this->redirectToRoute("billetterie_confirmation");

        } catch(\Stripe\Error\Card $e) {

            $this->addFlash("error","Une erreur est survenue lors du paiement, veillez réessayez");
            return $this->redirectToRoute("billetterie_paiement");
            // The card has been declined
        }

    }

    /**
     * @Route("/billetterie/confirmation", name="billetterie_confirmation")
     * @param ReservationManager $reservationManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmationAction(ReservationManager $reservationManager)
    {
        $reservationManager->throwException();
        $reservationManager->clearReservation();
        return $reservationManager->getReponse();
    }
}