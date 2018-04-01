<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/03/18
 * Time: 00:38
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Reservation;
use AppBundle\Form\BilletType;
use AppBundle\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BilletterieController extends Controller
{
    /**
     * @Route("/billetterie", name="homepage_billetterie")
     */
    public function indexAction(Request $request)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('reservation', $reservation);

            return $this->redirectToRoute('billetterie_information');
        }

        return $this->render('@App/billetterie/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/billetterie/information", name="billetterie_information")
     */
    public function informationAction()
    {
        $billets = new Billet();
        $form = $this->createForm(BilletType::class, $billets);

        return $this->render('@App/billetterie/information.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/billetterie/paiement", name="billetterie_paiement")
     */
    public function paiementAction()
    {
        return $this->render('@App/billetterie/index.html.twig');
    }

    /**
     * @Route("/billetterie/finalisation", name="finalisation")
     */
    public function finalisationAction()
    {

        return $this->render('@App/billetterie/index.html.twig');
    }
}