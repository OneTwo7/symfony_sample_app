<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditType extends AbstractType {

  public function buildForm (FormBuilderInterface $builder, array $options) {
    $builder
    ->add('username', TextType::class, array(
    	'attr' => array('class' => 'form-control')
    ))
    ->add('email', TextType::class, array(
    	'attr' => array('class' => 'form-control')
    ))
    ->add('plain_password', PasswordType::class, array(
      'label' => 'password',
      'attr' => array('class' => 'form-control')
    ))
    ->add('save', SubmitType::class, array(
    	'label' => 'Update',
    	'attr' => array('class' => 'btn btn-primary')
    ));
  }

  public function configureOptions (OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => User::class,
    ));
  }

}