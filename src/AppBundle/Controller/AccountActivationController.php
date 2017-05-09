<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AccountActivationController extends Controller {

	/**
   * @Route("/account_activation/{activationToken}?email={email}",
   					name="account_activation")
   */
  public function accountActivationAction ($activationToken, $email) {
    $user = $this->getDoctrine()->getRepository('AppBundle:User')
    ->findOneByEmail($email);

    if (!$user->getActivated() && $user->activate($activationToken)) {
    	$user->setActivated(true);

    	$em = $this->getDoctrine()->getManager();
      $em->persist($user);
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

}