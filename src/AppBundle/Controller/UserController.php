<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\ResetPassword;

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

    $form = $this->makeForm($user, 'Sign up');

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $username = $form['username']->getData();
      $email = $form['email']->getData();
      $plain_password = $form['plain_password']->getData();

      $activationToken = $user->generateToken();

      $user->setUsername($username);
      $user->setEmail($email);
      $user->setPlainPassword($plain_password);
      $user->setActivationToken($activationToken);

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $href = $this->get('router')->generate('account_activation', array(
        'activationToken' => $activationToken,
        'email' => $email
      ), UrlGeneratorInterface::ABSOLUTE_URL);
      $message = \Swift_Message::newInstance()
        ->setSubject('Sample App | Account Activation')
        ->setFrom('sample_app@example.com')
        ->setTo("$email")
        ->setBody($this->renderView('emails/registration.html.twig',
            array('name' => $username, 'href' => $href)),
            'text/html'
      );
      $this->get('mailer')->send($message);

      $this->addFlash('notice', 'Check email to activate your account!');

      return $this->redirectToRoute('home_page');
    }

    return $this->render('users/new.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("user/edit/{id}", name="user_edit")
   */
  public function editAction (Request $request, $id) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

    if (is_null($user)) {
      throw $this->createNotFoundException("User $id doesn't exist.");
    }

    $user->setUsername($user->getUsername());
    $user->setEmail($user->getEmail());

    $form = $this->makeForm($user, 'Update');

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $username = $form['username']->getData();
      $email = $form['email']->getData();
      $plain_password = $form['plain_password']->getData();

      $user->setUsername($username);
      $user->setEmail($email);
      $user->setPlainPassword($plain_password);

      $em = $this->getDoctrine()->getManager();
      $em->flush();

      $this->addFlash('notice', 'Your profile has been updated.');

      $user_id = $user->getId();

      return $this->redirectToRoute('user_show', array('id' => $user_id));
    }

    return $this->render('users/edit.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("user/change_password/{id}", name="user_change_password")
   */
  public function changePasswordAction (Request $request, $id) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

    if (is_null($user)) {
      throw $this->createNotFoundException("User $id doesn't exist.");
    }

    $form = $this->createFormBuilder($user)
    ->add('old_password', PasswordType::class, array(
      'attr' => array('class' => 'form-control'),
      'label' => 'Old password'))
    ->add('plain_password', RepeatedType::class, array(
      'type' => PasswordType::class,
      'invalid_message' => 'The password fields must match.',
      'options' => array('attr' => array('class' => 'form-control')),
      'required' => true,
      'first_options' => array('label' => 'New password'),
      'second_options' => array('label' => 'Password confirmation')
    ))
    ->add('save', SubmitType::class, array('label' => 'Change password',
    'attr' => array('class' => 'btn btn-primary')))->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $oldPassword = $form['old_password']->getData();

      if ($user->validateToken($oldPassword, 'password')) {
        $plain_password = $form['plain_password']->getData();

        $user->setPlainPassword($plain_password);
        $user->setUpdatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('notice', 'Your password has been changed.');

        $user_id = $user->getId();

        return $this->redirectToRoute('user_show', array('id' => $user_id));
      } else {
        $this->addFlash('notice', 'Old password is incorrect.');
      }
    }

    return $this->render('users/change_password.html.twig', [
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

      if ($id == $this->getUser()->getId()) {
        $this->addFlash('notice', 'You can\'t delete yourself!');
        return $this->redirectToRoute('user_index');
      }

      $em->remove($user);
      $em->flush();

      $this->addFlash('notice', 'user deleted');

      return $this->redirectToRoute('user_index');
  }

  /**
   * @Route("/forgot_password", name="forgot_password")
   */
  public function forgotPasswordAction (Request $request) {
    $reset_password = new ResetPassword;

    $form = $this->createFormBuilder($reset_password)
    ->add('email', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('save', SubmitType::class, array('label' => 'Submit',
    'attr' => array('class' => 'btn btn-primary')))->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $email = $form['email']->getData();

      $user = $this->getDoctrine()->getRepository('AppBundle:User')
      ->findOneByEmail($email);

      if (is_object($user)) {
        $reset_token = $user->generateToken();

        $user->encodeResetDigest($reset_token);
        $user->setResetToken($reset_token);
        $user->setResetSentAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $href = $this->get('router')->generate('reset_password', array(
          'resetToken' => $reset_token,
          'email' => $email
        ), UrlGeneratorInterface::ABSOLUTE_URL);
        $message = \Swift_Message::newInstance()
          ->setSubject('Sample App | Reset Password')
          ->setFrom('sample_app@example.com')
          ->setTo("$email")
          ->setBody($this->renderView('emails/reset_password.html.twig',
              array('name' => $username, 'href' => $href)),
              'text/html'
        );
        $this->get('mailer')->send($message);

        $this->addFlash('notice', 'Check email for reset password link!');

        return $this->redirectToRoute('home_page');
      } else {
        $this->addFlash('notice', 'There\'s no user with such email!');
      }
    }

    return $this->render('users/forgot_password.html.twig', [
      'form' => $form->createView()
    ]);
  }

  private function makeForm ($user, $submit_text) {
    $form = $this->createFormBuilder($user)
    ->add('username', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('email', TextType::class,
      array('attr' => array('class' => 'form-control')))
    ->add('plain_password', RepeatedType::class, array(
      'type' => PasswordType::class,
      'invalid_message' => 'The password fields must match.',
      'options' => array('attr' => array('class' => 'form-control')),
      'required' => true,
      'first_options' => array('label' => 'Password'),
      'second_options' => array('label' => 'Password confirmation')
    ))
    ->add('save', SubmitType::class, array('label' => $submit_text,
    'attr' => array('class' => 'btn btn-primary')))->getForm();

    return $form;
  }

}