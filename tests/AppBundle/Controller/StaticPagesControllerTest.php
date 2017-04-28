<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StaticPagesControllerTest extends WebTestCase {

	private function checkTitle ($params) {
		$crawler = $params['crawler'];
		$title = 'Sample App';
		if (array_key_exists('title', $params)) {
			$title = $params['title'] . ' | ' . $title;
		}
		$this->assertContains($title, $crawler->filter('title')->text());
	}

  public function testHome () {
      $client = static::createClient();

      $crawler = $client->request('GET', '/');

      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertContains('Welcome to Symfony!', $crawler->filter('#container h1')->text());
      $this->checkTitle(array('crawler' => $crawler));
  }

  public function testHelp () {
      $client = static::createClient();

      $crawler = $client->request('GET', '/help');

      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertContains('help', $crawler->filter('h1')->text());
      $this->checkTitle(array(
      	'crawler' => $crawler,
      	'title'   => 'Help'
      ));
  }

  public function testAbout () {
      $client = static::createClient();

      $crawler = $client->request('GET', '/about');

      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertContains('about', $crawler->filter('h1')->text());
      $this->checkTitle(array(
      	'crawler' => $crawler,
      	'title'   => 'About'
      ));
  }

  public function testContact () {
      $client = static::createClient();

      $crawler = $client->request('GET', '/contact');

      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertContains('contact', $crawler->filter('h1')->text());
      $this->checkTitle(array(
      	'crawler' => $crawler,
      	'title'   => 'Contact'
      ));
  }

}