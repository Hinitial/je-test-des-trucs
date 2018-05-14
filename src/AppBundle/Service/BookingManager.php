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
     * Verifie une étape précise de la réservation
     * @param $step
     * @throws \Exception
     */
    public function verifyStep($step, $booking)
    {
        $errors = $this->validation->validate($this->getBooking(), null, array($step));
        if (count($errors) > 0) {
            throw new \Exception('Erreur de naviguation');
        }
    }

    /**
     * Retourne Booking
     * @return mixed Retourne un Objet Reservation enregistrer dans la session
     */
    public function getBooking()
    {
        return ($this->session->get(self::NOM_SESSION));
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
     * Obtient une vue à affichée et affectuant les actions requisent
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
     * Execute un action en fonction de la route actuel
     * @return RedirectResponse
     */
    public function ActionForm()
    {
        $route = $this->requestStack->getCurrentRequest()->get('_route');
        switch ($route) {
            case 'homepage_billetterie':
                $this->addTickets($this->getBooking());
                return new RedirectResponse($this->router->generate('billetterie_information'));
                break;
            case 'billetterie_information':
                $this->setTicketPrice($this->getBooking());
                return new RedirectResponse($this->router->generate('billetterie_paiement'));
                break;
            default:
        }
    }

    /**
     * Obtient une vue à affichée
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
     * Ajoute le bon nombre de Tickets dans le Booking
     */
    public function addTickets(Booking $booking)
    {
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
     * Ajoute les prix aux Tickets
     */
    public function setTicketPrice(Booking $booking)
    {
        foreach ($booking->getTickets() as $ticket) {
            $ticket->setPrice($this->priceTicketManager->getTicketPrice($ticket));
        }
    }

    /**
     * Ajoute les dernière information de la réservation
     */
    public function setLastInformation(Booking $booking)
    {
        $booking
            ->setBookingCode($this->generateBookingCode())
            ->setBookingDate(new \DateTime());
    }

    /**
     * Lève une Exception si la Session nexiste pas
     * @throws SessionNotFoundException
     */
    public function throwException($step)
    {
        if (!($this->session->has(self::NOM_SESSION))) {
            throw new SessionNotFoundException('Session not exist');
        }
        $booking = $this->getBooking();
        $this->verifyStep($step,$booking);
    }

    /**
     * Enregistre en base de donnée la Réservation
     */
    public function insertBooking(Booking $booking)
    {
        $this->entity->persist($booking);
        foreach ($booking->getTickets() as $ticket) {
            $this->entity->persist($ticket);
        }
        $this->entity->flush();
    }

    /**
     * Supprime le l'objet Booking de la session
     */
    public function clearBooking()
    {
        $this->session->remove(self::NOM_SESSION);
    }
}