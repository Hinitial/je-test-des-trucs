<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/03/18
 * Time: 00:38
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class BilletterieController extends Controller
{
    /**
     * @Route("/billetterie", name="homepage_billetterie")
     */
    public function indexAction()
    {
        return $this->render('@App/billetterie/index.html.twig');
    }

    /**
     * @Route("/billetterie/information", name="billetterie_information")
     */
    public function informationAction()
    {
        return $this->render('@App/billetterie/index.html.twig');
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