<?php

namespace Tests\AppBundle\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SiteLayoutTest extends WebTestCase {

  public function testLayoutLinks () {
    $client = static::createClient();
    $crawler = $client->request('GET', '/');
    $content = 'Welcome to the Sample App';

    $this->assertEquals(200, $client->getResponse()->getStatusCode());
    $this->assertCount(1, $crawler->filter("h1:contains($content)"));
    $this->assertCount(2, $crawler->filter('a[href="/"]'));
    $this->assertCount(1, $crawler->filter('a[href="/help"]'));
    $this->assertCount(1, $crawler->filter('a[href="/about"]'));
    $this->assertCount(1, $crawler->filter('a[href="/contact"]'));
    $this->assertCount(1, $crawler->filter('a[href="/signup"]'));
  }

}