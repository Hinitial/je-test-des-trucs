<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 26/04/18
 * Time: 10:48
 */

namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class StripeManager
{
    protected $reservationManager;
    protected $requestStack;
    protected $session;
    protected $router;

    public function __construct(
        ReservationManager $reservationManager,
        RequestStack $requestStack,
        SessionInterface $session,
        RouterInterface $router){

        $this->reservationManager = $reservationManager;
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->router = $router;
    }

    public function initPayment(){
        \Stripe\Stripe::setApiKey("sk_test_rTE16Sgt73ezOF1XCqy76TLg");
    }

    public function makePayment(){
            $token = $this->requestStack->getCurrentRequest()->request->get('stripeToken');
            $charge = \Stripe\Charge::create(array(
                "amount" => ($this->reservationManager->getReservation()->getPrixReservation())*100,
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Musee du louvre"
            ));
    }
}