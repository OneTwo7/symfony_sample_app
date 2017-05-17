<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordType extends AbstractType {

  public function buildForm (FormBuilderInterface $builder, array $options) {
    $builder
    ->add('plain_password', RepeatedType::class, array(
      'type' => PasswordType::class,
      'invalid_message' => 'The password fields must match.',
      'options' => array('attr' => array('class' => 'form-control')),
      'required' => true,
      'first_options' => array('label' => 'New password'),
      'second_options' => array('label' => 'Password confirmation')
    ))
    ->add('save', SubmitType::class, array(
      'label' => 'Reset password',
      'attr' => array('class' => 'btn btn-primary')
    ));
  }

  public function configureOptions (OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => User::class,
    ));
  }

}