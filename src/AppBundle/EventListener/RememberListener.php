<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RememberListener {

  public function onKernelController (FilterControllerEvent $event) {
  	$request = $event->getRequest();

  	if ($request->cookies->has('REMEMBERME')) {
  		return;
  	}
  }

}