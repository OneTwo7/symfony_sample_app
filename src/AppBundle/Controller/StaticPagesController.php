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

    // PROTOFEED
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
      'SELECT m
      FROM AppBundle:Micropost m
      WHERE m.user = :id
      ORDER BY m.createdAt DESC'
    )->setParameter('id', $user->getId());

    $microposts = $query->getResult();

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
        $pictureDirectory = $this->getParameter('pictures_directory');

        $s3 = $this->container->get('amazon_storage');
        $pic = $s3->uploadImage($picture, $pictureName, $pictureDirectory);

        $micropost->setPicture($pic);
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