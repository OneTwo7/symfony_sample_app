<?php

namespace AppBundle\Form;

use AppBundle\Entity\Micropost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MicropostType extends AbstractType {

  public function buildForm (FormBuilderInterface $builder, array $options) {
    $builder
    ->add('content', TextareaType::class, array(
        'label' => false, 'attr' => array(
        'class' => 'form-control',
        'rows' => '4',
        'placeholder' => 'Compose new micropost...'
      )
    ))
    ->add('save', SubmitType::class, array(
      'label' => 'Post',
      'attr' => array('class' => 'btn btn-block btn-primary')
    ))
    ->add('picture', FileType::class, array(
      'label' => false, 'required' => false
    ));
  }

  public function configureOptions (OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => Micropost::class,
    ));
  }

}