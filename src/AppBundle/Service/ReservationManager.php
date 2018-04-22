<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 09/04/18
 * Time: 13:59
 */

namespace AppBundle\Service;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class ReservationManager
{
    const NOM_SESSION = 'reservation';

    protected $mailer;
    protected $session;
    protected $template;
    protected $entity;
    protected $formFactory;
    protected $requestStack;
    protected $router;


    public function __construct(
        \Swift_Mailer $mailer,
        SessionInterface $session,
        \Twig_Environment $twig_Environment,
        EntityManagerInterface $entity,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        RouterInterface $router){

        $this->mailer = $mailer;
        $this->session = $session;
        $this->template = $twig_Environment;
        $this->entity = $entity;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->router = $router;

    }


    /**
     * Mets en Session l'objet Reservation
     */
    public function initSession(){
        if (!($this->session->has(self::NOM_SESSION))) {
            $this->session->set(self::NOM_SESSION, new Reservation());
        }
    }

    /**
     * Genere les Billet
     */
    public function generateBillet(){
        $reservation = $this->getReservation();
        $reservation
            ->setCodeReservation($this->generateCodeReservation())
            ->setDateReservation(new \DateTime());
        foreach ($reservation->getBillets() as $billet){
            $reservation->removeBillet($billet);
        }
        for ($i=0;$i<($reservation->getNbreBillet());$i++){
            $billet = new Billet();
            $billet->setPays('FR');
            $reservation->addBillet($billet);
        }
    }

    /**
     * @param null $formType
     * @return RedirectResponse|Response
     */
    public function getReponse($formType = null){
        if ($formType !== null){
            $form = ($this->formFactory->create($formType, $this->getReservation()));

            $form->handleRequest($this->requestStack->getCurrentRequest());

            if($form->isSubmitted() && $form->isValid()){
                return $this->ActionForm();
            }

            return $this->renderBilletterie($form);
        }
        else{
            return $this->renderBilletterie();
        }
    }

    /**
     * @return RedirectResponse
     */
    public function ActionForm(){
        $route = $this->requestStack->getCurrentRequest()->get('_route');
        switch ($route){
            case 'homepage_billetterie':
                $this->generateBillet();
                return new RedirectResponse($this->router->generate('billetterie_information'));
                break;
            case 'billetterie_information':
                return new RedirectResponse($this->router->generate('billetterie_paiement'));
                break;
            default:
        }
    }

    /**
     * @param FormInterface|null $form
     * @return Response
     */
    public function renderBilletterie(FormInterface $form = null){
        $route = $this->requestStack->getCurrentRequest()->get('_route');
        $nameTemplate = '';
        switch ($route){
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
            if ($form !== null){
                return new Response($this->template->render(('billetterie/' . $nameTemplate . '.html.twig'), array(
                    'form' => $form->createView()
                )));
            }
            else{
                return new Response($this->template->render(('billetterie/' . $nameTemplate . '.html.twig')));
            }

        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        }
    }

    /**
     * @return mixed Retourne un Objet Reservation enregistrer dans la session
     */
    public function getReservation(){
        return ($this->session->get(self::NOM_SESSION));
    }

    /**
     * Génère un code de réservation
     * @return string Code de reservation généré
     */
    public function generateCodeReservation(){
        $code = '';
        $pool = array_merge(range(0,9),range('A', 'Z'));

        for($i=0; $i < 10; $i++) {
            $code .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $code;
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function EnvoyerEmail(){
        $reservation = $this->getReservation();
        $mail = new  \Swift_Message('Votre Reservation pour le Musée du louvre');
        $mail = $mail
            ->setFrom('oc.projet.super@gmail.com')
            ->setTo($reservation->getEmail())
            ->setContentType('text/html')
            ->setBody($this->template->render('billetterie/email.html.twig', array(
                'logoMusee' => $mail->embed(\Swift_Image::fromPath('images/Logo.jpg')),
                'logoSombre' => $mail->embed(\Swift_Image::fromPath('images/louvre-1eravril.jpg'))
            )));

        $this->mailer->send($mail);
    }

    /**
     *
     */
    public function insertReservation(){
        $reservation = $this->getReservation();
        $this->entity->persist($reservation);
        foreach ($reservation->getBillets() as $billet){
            $this->entity->persist($billet);
        }
        $this->entity->flush();
    }

    /**
     *
     */
    public function clearReservation(){
        $this->session->remove(self::NOM_SESSION);
    }
}