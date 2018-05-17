<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/03/18
 * Time: 00:44
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use AppBundle\Service\MailManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('app/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request, MailManager $mailManager)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailManager->mailContact($contact);

            $this->addFlash('notice', 'Formulaire bien envoyé');
        }

        return $this->render('app/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/mention-legales", name="mention_legales")
     */
    public function mentionLegalesAction()
    {
        return $this->render('app/mention_legale.html.twig');
    }


    /**
     * @Route("/cgv", name="cgv")
     */
    public function cGVAction()
    {
        return $this->render('app/cgv.html.twig');
    }
}