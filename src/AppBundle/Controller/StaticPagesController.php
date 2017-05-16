<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Micropost;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StaticPagesController extends Controller {

  /**
   * @Route("/", name="home_page")
   */
  public function homeAction (Request $request) {
    $user = $this->get('security.token_storage')->getToken()->getUser();

    if (is_null($user) || $user === 'anon.') {
      return $this->render('static_pages/home.html.twig');
    }

    // FEED
    $em = $this->getDoctrine()->getManager();
    $user_id = $user->getId();
    $following_ids = "SELECT IDENTITY(r.followed) FROM AppBundle:Relationship r
                      WHERE r.follower = $user_id";

    $query = $em->createQuery(
      "SELECT m
      FROM AppBundle:Micropost m
      WHERE m.user IN ($following_ids) OR m.user = :id
      ORDER BY m.createdAt DESC"
    )->setParameter('id', $user_id);

    $paginator = $this->get('knp_paginator');

    $microposts = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      10
    );

    $micropost = new Micropost();

    $form = $this->createFormBuilder($micropost)
    ->add('content', TextareaType::class,
      array('label' => false, 'attr' => array(
        'class' => 'form-control',
        'rows' => '4',
        'placeholder' => 'Compose new micropost...'
      )))
    ->add('save', SubmitType::class, array('label' => 'Post',
    'attr' => array('class' => 'btn btn-block btn-primary')))
    ->add('picture', FileType::class, array(
      'label' => false, 'required' => false
    ))
    ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $content = $form['content']->getData();
      $picture = $micropost->getPicture();

      if (!is_null($picture)) {
        $pictureName = md5(uniqid()).'.'.$picture->guessExtension();

        if ($this->container->get('kernel')->getEnvironment() == "dev") {
          $picture->move(
            $this->getParameter('pictures_directory'),
            $pictureName
          );
          $pictureName = 'uploads/pictures/' . $pictureName;
        } else {
          $s3 = $this->container->get('amazon_storage');
          $pictureName = $s3->uploadImage($picture, $pictureName);
        } 

        $micropost->setPicture($pictureName);
      }

      $micropost->setContent($content);
      $micropost->setUser($user);

      $em->persist($micropost);
      $em->flush();

      $this->addFlash('notice', 'Micropost created!');

      return $this->redirectToRoute('home_page', [
        'form' => $form->createView(),
        'user' => $user,
        'microposts' => $microposts
      ]);
    }

    return $this->render('static_pages/home.html.twig', [
      'form' => $form->createView(),
      'user' => $user,
      'microposts' => $microposts
    ]);
  }

  /**
   * @Route("/help", name="help_page")
   */
  public function helpAction () {
    return $this->render('static_pages/help.html.twig');
  }

  /**
   * @Route("/about", name="about_page")
   */
  public function aboutAction () {
    return $this->render('static_pages/about.html.twig');
  }

  /**
   * @Route("/contact", name="contact_page")
   */
  public function contactAction () {
    return $this->render('static_pages/contact.html.twig');
  }

}