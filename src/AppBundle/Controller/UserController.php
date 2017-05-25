<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\ResetPassword;
use AppBundle\Entity\Relationship;
use AppBundle\Form\FollowType;
use AppBundle\Form\UserType;
use AppBundle\Form\EditType;
use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\ForgotPasswordType;

class UserController extends Controller {

	/**
   * @Route("/users", name="user_index")
   */
  public function indexAction (Request $request) {
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
      "SELECT u FROM AppBundle:User u ORDER BY u.id"
    );
    $users = $this->paginate($request, $query);

    return $this->render('users/index.html.twig', [
        'users' => $users,
    ]);
  }

  /**
   * @Route("/user/{id}/following", name="user_following")
   */
  public function followingAction (Request $request, $id) {
    $user = $this->findUser($id);

    $em = $this->getDoctrine()->getManager();

    $following_ids = "SELECT IDENTITY(r.followed)
    FROM AppBundle:Relationship r WHERE r.follower = :id";

    $dql = "SELECT u FROM AppBundle:User u
    WHERE u.id IN ($following_ids) ORDER BY u.createdAt";

    $query = $em->createQuery($dql)->setParameter('id', $user->getId());

    $following = $this->paginate($request, $query);

    $users = $em->createQuery($dql)->setParameter('id', $user->getId())
    ->getResult();

    return $this->render('users/show_follow.html.twig', [
      'title'     => 'Following',
      'user'      => $user,
      'gravatars' => $users,
      'users'     => $following
    ]);
  }

  /**
   * @Route("/user/{id}/followers", name="user_followers")
   */
  public function followersAction (Request $request, $id) {
    $user = $this->findUser($id);

    $em = $this->getDoctrine()->getManager();

    $followers_ids = "SELECT IDENTITY(r.follower)
    FROM AppBundle:Relationship r WHERE r.followed = :id";
    
    $dql = "SELECT u FROM AppBundle:User u
    WHERE u.id IN ($followers_ids) ORDER BY u.createdAt";

    $query = $em->createQuery($dql)->setParameter('id', $user->getId());

    $followers = $this->paginate($request, $query);

    $users = $em->createQuery($dql)->setParameter('id', $user->getId())
    ->getResult();

    return $this->render('users/show_follow.html.twig', [
      'title'     => 'Followers',
      'user'      => $user,
      'gravatars' => $users,
      'users'     => $followers
    ]);
  }

  /**
   * @Route("/user/{id}", name="user_show")
   */
  public function showAction (Request $request, $id) {
    $user = $this->findUser($id);

    $em = $this->getDoctrine()->getManager();

    $microposts = $this->getMicroposts($user, $em, $request);

    $current_user = $this->get('security.token_storage')->getToken()
    ->getUser();

    $relationship = new Relationship();

    $form = $this->createFollowForm($relationship, $id);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $current_user = $this->get('security.token_storage')->getToken()
      ->getUser();

      $relationship->setFollower($current_user);
      $relationship->setCreatedAt(new \DateTime());
      $relationship->setFollowed($user);

      $em = $this->getDoctrine()->getManager();
      $em->persist($relationship);
      $em->flush();

      return $this->render('users/show.html.twig', array(
        'user' => $user,
        'microposts' => $microposts,
        'is_following' => true,
        'form' => $form->createView()
      ));
    }

    $relationship = $this->findRelationship($em, $current_user, $user);

    $isFollowing = is_null($relationship) ? false : true;

    return $this->render('users/show.html.twig', [
        'user' => $user,
        'microposts' => $microposts,
        'is_following' => $isFollowing,
        'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/user/unfollow/{id}", name="user_unfollow")
   */
  public function unfollowAction (Request $request, $id) {
    $user = $this->findUser($id);

    $current_user = $this->get('security.token_storage')->getToken()
    ->getUser();

    $em = $this->getDoctrine()->getManager();

    $relationship = $this->findRelationship($em, $current_user, $user);

    if (!is_null($relationship) {
      $em->remove($relationship);
      $em->flush();
    }

    $microposts = $this->getMicroposts($user, $em, $request);

    $relationship = new Relationship();
    $form = $this->createFollowForm($relationship, $id);

    return $this->render('users/show.html.twig', array(
      'user' => $user,
      'microposts' => $microposts,
      'is_following' => false,
      'form' => $form->createView()
    ));
  }

  /**
   * @Route("/signup", name="signup")
   */
  public function createAction (Request $request) {
    $user = new User;

    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $username = $form['username']->getData();
      $email = $form['email']->getData();
      $plainPassword = $form['plain_password']->getData();

      $encoder = $this->container->get('security.password_encoder');
      $encoded = $encoder->encodePassword($user, $plainPassword);

      $user->setUsername($username);
      $user->setEmail($email);
      $user->setPassword($encoded);

      $activationDigest = $this->sendAccountActivation($user, $encoder);

      $user->setActivationDigest($activationDigest);

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $this->addFlash('notice', 'Check email to activate your account!');

      return $this->redirectToRoute('home_page');
    }

    return $this->render('users/new.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/resend_activation", name="resend_activation")
   */
  public function resendActivationAction (Request $request) {
    $resendActivation = new ResetPassword();

    $form = $this->createForm(ForgotPasswordType::class, $resendActivation);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $email = $form['email']->getData();

      $user = $this->getDoctrine()->getRepository('AppBundle:User')
      ->findOneByEmail($email);

      if (is_object($user)) {
        if ($user->getActivated()) {
          $this->addFlash('notice', 'Your account is already activated');
        } else {
          $encoder = $this->container->get('security.password_encoder');

          $activationDigest = $this->sendAccountActivation($user, $encoder);

          $user->setActivationDigest($activationDigest);

          $em = $this->getDoctrine()->getManager();
          $em->flush();

          $this->addFlash('notice', 'Email with new activation link is sent.');
        }

        return $this->redirectToRoute('home_page');
      } else {
        $this->addFlash('notice', "User with email $email is not registered!");
      }
    }

    return $this->render('users/email_form.html.twig', [
      'form'  => $form->createView(),
      'title' => 'Resend Activation'
    ]);
  }

  /**
   * @Route("/user/edit/{id}", name="user_edit")
   */
  public function editAction (Request $request, $id) {
    $user = $this->findUser($id);

    $user->setUsername($user->getUsername());
    $user->setEmail($user->getEmail());

    $form = $this->createForm(EditType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $encoder = $this->container->get('security.password_encoder');
      $plainPassword = $form['plain_password']->getData();

      if ($encoder->isPasswordValid($user, $plainPassword)) {
        $username = $form['username']->getData();
        $email = $form['email']->getData();

        $user->setUsername($username);
        $user->setEmail($email);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('notice', 'Your profile has been updated.');

        $user_id = $user->getId();

        return $this->redirectToRoute('user_show', array('id' => $user_id));
      } else {
        $this->addFlash('notice', 'The password is incorrect.');
      }
    }

    return $this->render('users/edit.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("user/change_password/{id}", name="user_change_password")
   */
  public function changePasswordAction (Request $request, $id) {
    $user = $this->findUser($id);

    $form = $this->createForm(ChangePasswordType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $encoder = $this->container->get('security.password_encoder');
      $oldPassword = $form['old_password']->getData();

      if ($encoder->isPasswordValid($user, $oldPassword)) {
        $plainPassword = $form['plain_password']->getData();
        $encoded = $encoder->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);

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
  public function deleteAction (Request $request, $id) {
    $user = $this->findUser($id);
    
    if ($id == $this->getUser()->getId()) {
      $this->addFlash('notice', 'You can\'t delete yourself!');
      return $this->redirectToRoute('user_index');
    }

    $em = $this->getDoctrine()->getManager();

    $em->remove($user);
    $em->flush();

    $this->addFlash('notice', 'user deleted');

    return $this->redirect($request->server->get('HTTP_REFERER'));
  }

  /**
   * @Route("/forgot_password", name="forgot_password")
   */
  public function forgotPasswordAction (Request $request) {
    $reset_password = new ResetPassword;

    $form = $this->createForm(ForgotPasswordType::class, $reset_password);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $email = $form['email']->getData();

      $user = $this->getDoctrine()->getRepository('AppBundle:User')
      ->findOneByEmail($email);

      if (is_object($user)) {
        $reset_token = urlencode(random_bytes(22));

        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $reset_token);

        $user->setResetDigest($encoded);
        $user->setResetSentAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $href = $this->makeUrl(
          'reset_password', 'resetToken', $reset_token, $email
        );
        $message = $this->generateMessage('Reset Password', $email,
          'emails/reset_password.html.twig', $user->getUsername(), $href
        );
        $this->get('mailer')->send($message);

        $this->addFlash('notice', 'Check email for reset password link!');

        return $this->redirectToRoute('home_page');
      } else {
        $this->addFlash('notice', 'There\'s no user with such email!');
      }
    }

    return $this->render('users/email_form.html.twig', [
      'form'  => $form->createView(),
      'title' => 'Forgot Password'
    ]);
  }


  private function findUser ($id) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

    if (is_null($user)) {
      throw $this->createNotFoundException("User $id doesn't exist.");
    }

    return $user;
  }

  private function paginate ($request, $query) {
    $paginator = $this->get('knp_paginator');

    return $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      10
    );
  }

  private function makeUrl ($path, $tokenName, $token, $email) {
    return $this->get('router')->generate($path, array(
      $tokenName => $token,
      'email' => $email
    ), UrlGeneratorInterface::ABSOLUTE_URL);
  }

  private function generateMessage ($subject, $email, $view, $name, $href) {
    return \Swift_Message::newInstance()
    ->setSubject($subject)
    ->setFrom('sample_app@example.com')
    ->setTo($email)
    ->setBody($this->renderView($view, array(
      'name' => $name,
      'href' => $href
    )), 'text/html');
  }

  private function sendAccountActivation ($user, $encoder) {
    $activationToken  = urlencode(random_bytes(22));
    $activationDigest = $encoder->encodePassword($user, $activationToken);

    $email    = $user->getEmail();
    $username = $user->getUsername();

    $href = $this->makeUrl(
      'account_activation', 'activationToken', $activationToken, $email
    );
    $message = $this->generateMessage('Account Activation', $email,
      'emails/registration.html.twig', $username, $href
    );
    $this->get('mailer')->send($message);

    return $activationDigest;
  }

  private function getMicroposts ($user, $em, $request) {
    $dql = "SELECT m FROM AppBundle:Micropost m
    WHERE m.user = :id ORDER BY m.createdAt DESC";

    $query = $em->createQuery($dql)->setParameter('id', $user->getId());

    return $this->paginate($request, $query);
  }

  private function createFollowForm ($relationship, $id) {
    return $this->createForm(FollowType::class, $relationship, array(
      'action' => $this->generateUrl('user_show', array('id' => $id))
    ));
  }

  private function findRelationship ($em, $current_user, $user) {
    $query = $em->createQuery(
      'SELECT r
      FROM AppBundle:Relationship r
      WHERE r.follower = :current_user_id AND
      r.followed = :user_id'
    )->setParameters(array(
      'current_user_id' => $current_user->getId(),
      'user_id' => $user->getId()
    ));

    return $query->getOneOrNullResult();
  }

}