<?php

namespace Tests\AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\User;

class UserTest extends WebTestCase {

	public function testUserValidity () {
		$validator = Validation::createValidator();

		$user = new User();

		$user->setUsername("");
		$user->setEmail("");

		$violations = $validator->validate($user);

		$this->assertEquals(0, count($violations));
  }

}