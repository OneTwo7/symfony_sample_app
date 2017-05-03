<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\AppUser;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends Controller {

	/**
   * @Route("/users", name="user_index")
   */
  public function indexAction (Request $request) {
    $users = $this->getDoctrine()->getRepository('AppBundle:AppUser')
    ->findAll();
    return $this->render('users/index.html.twig', [
        'users' => $users,
    ]);
  }

  /**
   * @Route("/user/{id}", name="user_show")
   */
  public function showAction ($id) {
    $user = $this->getDoctrine()->getRepository('AppBundle:AppUser')
    ->find($id);

    $verified = $user->verify("foobar");

    return $this->render('users/show.html.twig', [
        'user' => $user,
        'verified' => $verified
    ]);
  }

  /**
   * @Route("/signup", name="signup")
   */
  public function createAction (Request $request) {
    $user = new AppUser;

    $form = $this->createFormBuilder($user)
    ->add('name', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('email', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('password', PasswordType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('password_confirmation', PasswordType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('save', SubmitType::class, array('label' => 'Create user',
    'attr' => array('class' => 'btn btn-primary')))->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $name = $form['name']->getData();
      $email = $form['email']->getData();
      $password = $form['password']->getData();
      $password_confirmation = $form['password_confirmation']->getData();

      $user->setName($name);
      $user->setEmail($email);
      $user->setPassword($password);
      $user->setPasswordConfirmation($password_confirmation);

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $this->addFlash('notice', 'Welcome to the Sample App!');

      $user_id = $user->getId();

      return $this->redirectToRoute('user_show', array('id' => $user_id));
    }

    return $this->render('users/new.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/user/delete/{id}", name="user_delete")
   */
  public function deleteAction ($id) {
      $em = $this->getDoctrine()->getManager();
      $user = $em->getRepository('AppBundle:AppUser')->find($id);

      if (is_null($user)) {
          throw $this->createNotFoundException(
              "user $id does not exist"
          );
      }

      $em->remove($user);
      $em->flush();

      $this->addFlash('notice', 'user deleted');

      return $this->redirectToRoute('user_index');
  }

}