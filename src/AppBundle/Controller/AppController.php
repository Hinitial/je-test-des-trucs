<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/03/18
 * Time: 00:44
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function contactAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createFormBuilder($contact)
            ->add('nom',        TextType::class, array('required' => true))
            ->add('prenom',     TextType::class, array('required' => true))
            ->add('email',      EmailType::class, array('required' => true))
            ->add('titre',      TextType::class, array('required' => true))
            ->add('message',    TextareaType::class, array('required' => true))
            ->add('envoyer',    SubmitType::class)
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->addFlash('notice', 'Formulaire bien envoyé');
            }
        }

        return $this->render('@App/app/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}