<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase {

  public function createApplication () {
    return require __DIR__.'/../app/app.php';
  }

  public function test_should_get_signup () {
  	$client = $this->createClient();
	  $crawler = $client->request('GET', '/signup');

	  $this->assertTrue($client->getResponse()->isSuccessful());
	  $this->assertCount(1, $crawler->filter('title:contains("Sign up")'));
  }

}