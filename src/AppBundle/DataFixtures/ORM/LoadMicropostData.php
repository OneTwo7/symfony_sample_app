<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface as OFI;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Micropost;

class LoadMicropostData extends AbstractFixture implements OFI {

  public function load (ObjectManager $manager) {
    $adminMicroposts = array(
      "Behold my administrative powers!",
      "A witty message",
      "Another witty message",
      "Have anyone seen my banhammer?"
    );

    $genericMicroposts = array(
      "A generic message",
      "Another generic message",
      "Not such a witty message"
    );

    $names = array(
      "John",
      "Mary",
      "Ann",
      "Tim"
    );

    $microposts = array();

    foreach ($adminMicroposts as $micropostContent) {
      $micropost = new Micropost();
      $micropost
      ->setContent($micropostContent)
      ->setCreatedAt($this->getDateTime())
      ->setUser($this->getReference('admin'));

      $microposts[] = $micropost;
    }

    foreach ($names as $name) {
      foreach ($genericMicroposts as $micropostContent) {
        $micropost = new Micropost();
        $micropost
        ->setContent($micropostContent)
        ->setCreatedAt($this->getDateTime())
        ->setUser($this->getReference($name));

        $microposts[] = $micropost;
      }

      $micropost = new Micropost();

      $micropost
      ->setContent("My name is $name. That much I can tell.")
      ->setCreatedAt($this->getDateTime())
      ->setUser($this->getReference($name));

      $microposts[] = $micropost;
    }

    foreach ($microposts as $micropost) {
    	$manager->persist($micropost);
    }

    $manager->flush();
  }

  public function getOrder () {
    return 2;
  }

  private function getDateTime () {
    return (new \DateTime())->sub(new \DateInterval('PT'.mt_rand(1, 100).'H'));
  }

}