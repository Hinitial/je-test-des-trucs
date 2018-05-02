<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 09/04/18
 * Time: 13:59
 */

namespace AppBundle\Service;


use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use AppBundle\Exceptions\SessionNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookingManager
{
    const NOM_SESSION = 'reservation';

    protected $session;
    protected $template;
    protected $entity;
    protected $formFactory;
    protected $requestStack;
    protected $router;
    protected $priceTicketManager;
    protected $validation;


    public function __construct(
        SessionInterface $session,
        \Twig_Environment $twig_Environment,
        EntityManagerInterface $entity,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        RouterInterface $router,
        PriceTicketManager $priceTicketManager,
        ValidatorInterface $validation)
    {

        $this->session = $session;
        $this->template = $twig_Environment;
        $this->entity = $entity;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->priceTicketManager = $priceTicketManager;
        $this->validation = $validation;

    }


    /**
     * Mets en Session l'objet Booking
     */
    public function initSession()
    {
        if (!($this->session->has(self::NOM_SESSION))) {
            $this->session->set(self::NOM_SESSION, new Booking());
        }
    }


    /**
     * @param $step
     * @throws \Exception
     */
    public function verifyStep($step)
    {
        $errors = $this->validation->validate($this->getBooking(), null, array($step));
        if (count($errors) > 0) {
            throw new \Exception('Something went wrong!');
        }
    }

    /**
     * @return mixed Retourne un Objet Reservation enregistrer dans la session
     */
    public function getBooking()
    {
        return ($this->session->get(self::NOM_SESSION));
    }

    /**
     */
    public function setLastInformation()
    {
        $booking = $this->getBooking();
        $booking
            ->setBookingCode($this->generateBookingCode())
            ->setBookingDate(new \DateTime());
    }

    /**
     * Génère un code de réservation
     * @return string Code de reservation généré
     */
    public function generateBookingCode()
    {
        $code = '';
        $pool = array_merge(range(0, 9), range('A', 'Z'));

        for ($i = 0; $i < 10; $i++) {
            $code .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $code;
    }

    /**
     * @param null $formType
     * @return RedirectResponse|Response
     */
    public function getReponse($formType = null)
    {
        if ($formType !== null) {
            $form = ($this->formFactory->create($formType, $this->getBooking()));

            $form->handleRequest($this->requestStack->getCurrentRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                return $this->ActionForm();
            }

            return $this->renderTicketing($form);
        } else {
            return $this->renderTicketing();
        }
    }

    /**
     * @return RedirectResponse
     */
    public function ActionForm()
    {
        $route = $this->requestStack->getCurrentRequest()->get('_route');
        switch ($route) {
            case 'homepage_billetterie':
                $this->generateTickets();
                return new RedirectResponse($this->router->generate('billetterie_information'));
                break;
            case 'billetterie_information':
                $this->setPrice();
                return new RedirectResponse($this->router->generate('billetterie_paiement'));
                break;
            default:
        }
    }

    /**
     * Genere les Ticket
     */
    public function generateTickets()
    {
        $booking = $this->getBooking();
        foreach ($booking->getTickets() as $ticket) {
            $booking->removeTicket($ticket);
        }
        for ($i = 0; $i < ($booking->getTicketNumber()); $i++) {
            $ticket = new Ticket();
            $ticket->setCountry('FR');
            $booking->addTicket($ticket);
        }
    }

    /**
     */
    public function setPrice()
    {
        $booking = $this->getBooking();
        foreach ($booking->getTickets() as $ticket) {
            $this->priceTicketManager->getTicketPrice($ticket);
        }
    }

    /**
     * @param FormInterface|null $form
     * @return Response
     */
    public function renderTicketing(FormInterface $form = null)
    {
        $route = $this->requestStack->getCurrentRequest()->get('_route');
        $nameTemplate = '';
        switch ($route) {
            case 'homepage_billetterie':
                $nameTemplate = 'index';
                break;
            case 'billetterie_information':
                $nameTemplate = 'information';
                break;
            case 'billetterie_paiement':
                $nameTemplate = 'paiement';
                break;
            case 'billetterie_confirmation':
                $nameTemplate = 'confirmation';
                break;
            default:

        }
        try {
            if ($form !== null) {
                return new Response($this->template->render(('billetterie/' . $nameTemplate . '.html.twig'), array(
                    'form' => $form->createView()
                )));
            } else {
                return new Response($this->template->render(('billetterie/' . $nameTemplate . '.html.twig')));
            }

        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        }
    }

    /**
     * @throws SessionNotFoundException
     */
    public function throwException()
    {
        if (!($this->session->has(self::NOM_SESSION))) {
            throw new SessionNotFoundException('Session not exist');
        }
    }

    /**
     *
     */
    public function insertBooking()
    {
        $booking = $this->getBooking();
        $this->entity->persist($booking);
        foreach ($booking->getTickets() as $ticket) {
            $this->entity->persist($ticket);
        }
        $this->entity->flush();
    }

    /**
     *
     */
    public function clearBooking()
    {
        $this->session->remove(self::NOM_SESSION);
    }
}