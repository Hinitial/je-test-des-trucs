<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 26/04/18
 * Time: 10:48
 */

namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class StripeManager
{
    const NAME_SESSION_PK = 'pk_stripe';

    protected $bookingManager;
    protected $requestStack;
    protected $session;
    protected $stripePublic;
    protected $stripePrivate;

    public function __construct(
        BookingManager $bookingManager,
        RequestStack $requestStack,
        SessionInterface $session,
        $stripe_public,
        $stripe_private){

        $this->bookingManager = $bookingManager;
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->stripePublic = $stripe_public;
        $this->stripePrivate = $stripe_private;
    }

    /**
     *
     */
    public function initPublicKey(){
        $this->session->set(self::NAME_SESSION_PK, $this->stripePublic);
    }

    /**
     *
     */
    public function initPayment(){
        \Stripe\Stripe::setApiKey($this->stripePrivate);
    }

    /**
     *
     */
    public function makePayment(){
            $token = $this->requestStack->getCurrentRequest()->request->get('stripeToken');
            $charge = \Stripe\Charge::create(array(
                "amount" => ($this->bookingManager->getBooking()->getBookingPrice())*100,
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Musee du louvre"
            ));
    }

    /**
     *
     */
    public function clearPublicKey(){
        $this->session->remove(self::NAME_SESSION_PK);
    }
}