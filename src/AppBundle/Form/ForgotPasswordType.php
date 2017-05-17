<?php

namespace AppBundle\Form;

use AppBundle\Entity\ResetPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ForgotPasswordType extends AbstractType {

  public function buildForm (FormBuilderInterface $builder, array $options) {
    $builder
    ->add('email', TextType::class, array(
      'attr' => array('class' => 'form-control')
    ))
    ->add('save', SubmitType::class, array(
      'label' => 'Submit',
      'attr' => array('class' => 'btn btn-primary')
    ));
  }

  public function configureOptions (OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => ResetPassword::class,
    ));
  }

}