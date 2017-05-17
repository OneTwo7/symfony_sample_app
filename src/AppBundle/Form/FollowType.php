<?php

namespace AppBundle\Form;

use AppBundle\Entity\Relationship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FollowType extends AbstractType {

  public function buildForm (FormBuilderInterface $builder, array $options) {
    $builder
    ->add('save', SubmitType::class, array(
      'label' => 'Follow',
      'attr' => array('class' => 'btn btn-block btn-primary')
    ));
  }

  public function configureOptions (OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => Relationship::class,
    ));
  }

}