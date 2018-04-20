<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/03/18
 * Time: 00:38
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Reservation;
use AppBundle\Form\InformationType;
use AppBundle\Form\ReservationType;
use AppBundle\Service\ReservationManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BilletterieController extends Controller
{
    /**
     * @Route("/billetterie", name="homepage_billetterie")
     */
    public function indexAction(Request $request, ReservationManager $reservationManager)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $reservationManager->setSession($reservation);

            return $this->redirectToRoute('billetterie_information');
        }

        return $this->render('billetterie/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/billetterie/information", name="billetterie_information")
     */
    public function informationAction(Request $request)
    {
        $reservation = $request->getSession()->get('reservation');

        $form = $this->createForm(InformationType::class, $reservation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('reservation', $reservation);

            return $this->redirectToRoute('billetterie_paiement');
        }

        return $this->render('billetterie/information.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     */
    public function paiementAction(ReservationManager $reservationManager)
    {
        return $this->render('billetterie/paiement.html.twig');
    }

    /**
     * @Route(
     *     "/checkout",
     *     name="order_checkout",
     *     methods="POST"
     * )
     */
    public function checkoutAction(ReservationManager $reservationManager)
    {
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
     */
    public function confirmationAction(ReservationManager $reservationManager)
    {
        $reservationManager->clearReservation();
        return $this->render('billetterie/confirmation.html.twig');
    }
}