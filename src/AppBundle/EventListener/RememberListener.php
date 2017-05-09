<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use AppBundle\Controller\UserController;

class RememberListener {

	/**
   * @var SecurityContext
   */
  protected $context;

  /**
   * @param SecurityContext $context
   */
  public function __construct ($context) {
    $this->context = $context;
  }

  public function onKernelController (FilterControllerEvent $event) {
  	$request 		= $event->getRequest();
  	$controller = $event->getController();
  	$action  		= $controller[1];

  	if ($controller[0] instanceof UserController) {

  		$user = $this->context->getToken()->getUser();

  		if (is_object($user)) {

	  		if ($user->getRoles()[0] === 'ROLE_ADMIN') {
	  			return;
	  		}
	  		
	  		if ($action === 'editAction' or $action === 'changePasswordAction') {
	  			if ($user->getId() != $request->get('id')) {
	  				$request->getSession()->getFlashBag()
	  				->add('notice', 'You don\'t have access to the requested page.');
	  				$event->setController(function () {
	  					return new RedirectResponse('/');
	  				});
	  			}
	  		}

	  		if ($action === 'deleteAction') {
	  			if ($user->getId() != $request->get('id')) {
	  				$request->getSession()->getFlashBag()
	  				->add('notice', 'You can\'t delete another user.');
	  				$event->setController(function () {
	  					return new RedirectResponse('/');
	  				});
	  			}
	  		}

  		}

  	}

  }

}