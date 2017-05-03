<?php

namespace Tests\AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\AppUser;

class AppUserTest extends WebTestCase {

	public function testUserValidity () {
		$validator = Validation::createValidator();

		$user = new AppUser;

		$user->setName("");
		$user->setEmail("");

		$violations = $validator->validate($user);

		$this->assertEquals(0, count($violations));
  }

}