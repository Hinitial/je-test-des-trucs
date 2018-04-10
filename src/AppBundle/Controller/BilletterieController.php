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
//        $reservationManager = $this->get('app.reservation.manager');
        $form = $this->createForm(ReservationType::class, $reservation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $reservationManager->setBilletSession($reservation);

            return $this->redirectToRoute('billetterie_information');
        }

        return $this->render('@App/billetterie/index.html.twig', array(
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

        return $this->render('@App/billetterie/information.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     */
    public function paiementAction()
    {
        return $this->render('@App/billetterie/paiement.html.twig');
    }

    /**
     * @Route(
     *     "/checkout",
     *     name="order_checkout",
     *     methods="POST"
     * )
     */
    public function checkoutAction()
    {
        \Stripe\Stripe::setApiKey("sk_test_rTE16Sgt73ezOF1XCqy76TLg");

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create a charge: this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => 1000, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Musee du louvre"
            ));

            $this->addFlash("success","Bravo ça marche !");

            return $this->redirectToRoute("billetterie_confirmation");

        } catch(\Stripe\Error\Card $e) {

            $this->addFlash("error","Snif ça marche pas :(");
            return $this->redirectToRoute("billetterie_paiement");
            // The card has been declined
        }

    }

    /**
     * @Route("/billetterie/confirmation", name="billetterie_confirmation")
     */
    public function confirmationAction()
    {

        return $this->render('@App/billetterie/confirmation.html.twig');
    }
}