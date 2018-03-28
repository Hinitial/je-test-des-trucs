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
        return $this->render('@App/app/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $message = (new \Swift_Message($form['titre']->getData()))
                ->setFrom('oc.projet.super@gmail.com')
                ->setTo($form['email']->getData())
                ->setBody($this->renderView('@App/email/contact.html.twig', array(
                    'nom' => $form['nom']->getData(),
                    'prenom' => $form['prenom']->getData(),
                    'message' => $form['message']->getData()
                )),'text/html');

            $mailer->send($message);

            $this->addFlash('notice', 'Formulaire bien envoyé');
        }

        return $this->render('@App/app/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}