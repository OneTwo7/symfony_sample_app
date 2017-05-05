<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends Controller {

	/**
   * @Route("/users", name="user_index")
   */
  public function indexAction (Request $request) {
    $users = $this->getDoctrine()->getRepository('AppBundle:User')
    ->findAll();
    return $this->render('users/index.html.twig', [
        'users' => $users,
    ]);
  }

  /**
   * @Route("/user/{id}", name="user_show")
   */
  public function showAction ($id) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')
    ->find($id);

    return $this->render('users/show.html.twig', [
        'user' => $user
    ]);
  }

  /**
   * @Route("/signup", name="signup")
   */
  public function createAction (Request $request) {
    $user = new User;

    $form = $this->createFormBuilder($user)
    ->add('username', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('email', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('plain_password', PasswordType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('password_confirmation', PasswordType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('save', SubmitType::class, array('label' => 'Create user',
    'attr' => array('class' => 'btn btn-primary')))->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $username = $form['username']->getData();
      $email = $form['email']->getData();
      $plain_password = $form['plain_password']->getData();
      $password_confirmation = $form['password_confirmation']->getData();

      $user->setUsername($username);
      $user->setEmail($email);
      $user->setPlainPassword($plain_password);
      $user->setPasswordConfirmation($password_confirmation);

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $this->addFlash('notice', 'Welcome to the Sample App!');

      $user_id = $user->getId();

      $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
      $this->get('security.token_storage')->setToken($token);
      $this->get('session')->set('_security_main', serialize($token));

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
      $user = $em->getRepository('AppBundle:User')->find($id);

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