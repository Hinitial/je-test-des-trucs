<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 28/03/18
 * Time: 12:09
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',        TextType::class, array('required' => true))
            ->add('prenom',     TextType::class, array('required' => true))
            ->add('email',      EmailType::class, array('required' => true))
            ->add('titre',      TextType::class, array('required' => true))
            ->add('message',    TextareaType::class, array('required' => true))
            ->add('envoyer',    SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contact'
        ));
    }
}