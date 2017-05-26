<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Micropost;
use AppBundle\Form\MicropostType;

class StaticPagesController extends Controller {

  /**
   * @Route("/", name="home_page")
   */
  public function homeAction (Request $request) {
    $user = $this->get('security.token_storage')->getToken()->getUser();

    if (is_null($user) || $user === 'anon.') {
      return $this->render('static_pages/home.html.twig');
    }

    // Get micropost feed
    $em = $this->getDoctrine()->getManager();
    $user_id = $user->getId();

    // Get ids of users that current user is following
    $following_ids = "SELECT IDENTITY(r.followed) FROM AppBundle:Relationship r
                      WHERE r.follower = $user_id";

    // Get microposts of current user and their following
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

    // Create form for new microposts
    $micropost = new Micropost();

    $form = $this->createForm(MicropostType::class, $micropost);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $content = $form['content']->getData();
      $picture = $micropost->getPicture();

      if (!is_null($picture)) {
        // Create unique picture name with correct extension
        $pictureName = md5(uniqid()).'.'.$picture->guessExtension();

        /*Upload picture to Amazon bucket in production or put it
        into local folder otherwise*/
        if ($this->container->get('kernel')->getEnvironment() == "prod") {
          $s3 = $this->container->get('app.amazon_storage');
          $pictureName = $s3->uploadImage($picture, $pictureName);
        } else {
          $picture->move(
            $this->getParameter('pictures_directory'),
            $pictureName
          );
          $pictureName = 'uploads/pictures/' . $pictureName;
        } 

        $micropost->setPicture($pictureName);
      }

      $micropost->setContent($content);
      $micropost->setCreatedAt(new \DateTime());
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