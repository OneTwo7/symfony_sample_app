<?php

namespace AppBundle\Services;

use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RememberMeOverride extends TokenBasedRememberMeServices {

  protected function onLoginSuccess (Request $request, Response $response, TokenInterface $token) {
    $user = $token->getUser();
    $expires = time() + $this->options['lifetime'];
    $value = $this->generateCookieValue(get_class($user), $user->getEmail(), $expires, $user->getPassword());

    $response->headers->setCookie(
      new Cookie(
        $this->options['name'],
        $value,
        $expires,
        $this->options['path'],
        $this->options['domain'],
        $this->options['secure'],
        $this->options['httponly']
      )
    );
  }

}