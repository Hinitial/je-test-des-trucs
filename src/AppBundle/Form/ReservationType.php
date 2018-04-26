<?php

namespace AppBundle\Form;

use AppBundle\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('required' => true))
            ->add('jourVisite', DateType::class, array('required' => true, 'widget' => 'single_text'))
            ->add('typeBillet', ChoiceType::class, array(
                'choices' => array(
                    'Journée' => 'journee',
                    'Demie-journée' => 'demie_journee'
                )
            ))
            ->add('nbreBillet', IntegerType::class, array('required' => true))
            ->add('suivant', SubmitType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Reservation::class
        ));
    }


}
