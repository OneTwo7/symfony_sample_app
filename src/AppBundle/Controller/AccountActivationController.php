<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AccountActivationController extends Controller {

	/**
   * @Route("/account_activation/{activationToken}?email={email}",
   *				name="account_activation")
   */
  public function accountActivationAction ($activationToken, $email) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')
    ->findOneByEmail($email);

    $activationDigest = $user->getActivationDigest();

    if (!$user->getActivated() &&
        password_verify($activationToken, $activationDigest)) {

    	$user->setActivated(true);

    	$em = $this->getDoctrine()->getManager();
      $em->flush();
    } else {
    	$this->addFlash('notice', 'Incorrect activation link!');
    	return $this->redirectToRoute('home_page');
    }

    $this->addFlash('notice', 'Account activated!');

    $user_id = $user->getId();

    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
    $this->get('security.token_storage')->setToken($token);
    $this->get('session')->set('_security_main', serialize($token));

    return $this->redirectToRoute('user_show', array('id' => $user_id));
  }

  /**
   * @Route(
   *    "/reset_password/{resetToken}?email={email}", name="reset_password"
   * )
   */
  public function resetPasswordAction (Request $request, $resetToken, $email) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')
    ->findOneByEmail($email);

    $resetDigest = $user->getResetDigest();

    if ($user->getResetSentAt() < 7200 &&
        password_verify($resetToken, $resetDigest)) {
    	$form = $this->createFormBuilder($user)
	    ->add('plain_password', RepeatedType::class, array(
	      'type' => PasswordType::class,
	      'invalid_message' => 'The password fields must match.',
	      'options' => array('attr' => array('class' => 'form-control')),
	      'required' => true,
	      'first_options' => array('label' => 'New password'),
	      'second_options' => array('label' => 'Password confirmation')
	    ))
	    ->add('save', SubmitType::class, array('label' => 'Reset password',
	    'attr' => array('class' => 'btn btn-primary')))->getForm();

	    $form->handleRequest($request);

	    if ($form->isSubmitted() && $form->isValid()) {

	      $plainPassword = $form['plain_password']->getData();
        $encoded = $this->encode($user, $plainPassword);

        $user->setPassword($encoded);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('notice', 'Your password has been reset.');

        $token = new UsernamePasswordToken($user, null, 'main',
        																	 $user->getRoles());
		    $this->get('security.token_storage')->setToken($token);
		    $this->get('session')->set('_security_main', serialize($token));

        $user_id = $user->getId();

        return $this->redirectToRoute('user_show', array('id' => $user_id));
      }

      return $this->render('activation_and_reset/password_form.html.twig', [
	      'form' => $form->createView()
	    ]);
    } else {
    	$this->addFlash('notice', 'Incorrect reset password link!');
    	return $this->redirectToRoute('home_page');
    }
  }

  private function encode ($user, $raw) {
    $encoder = $this->container->get('security.password_encoder');
    return $encoder->encodePassword($user, $raw);
  }

}