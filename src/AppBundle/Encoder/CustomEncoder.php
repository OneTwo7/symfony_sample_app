<?php

namespace AppBundle\Encoder;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class CustomEncoder extends BasePasswordEncoder {

  public function encodePassword ($raw, $salt) {
    if ($this->isPasswordTooLong($raw)) {
      throw new BadCredentialsException('Invalid password.');
    }

    $options = array(
      'cost' => 12,
      'salt' => $salt
    );

    return password_hash($raw, PASSWORD_BCRYPT, $options);
  }

  public function isPasswordValid ($encoded, $raw, $salt) {
    if ($this->isPasswordTooLong($raw)) {
      return false;
    }

    return password_verify($raw, $encoded);
  }

}