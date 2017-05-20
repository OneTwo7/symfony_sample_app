<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface as OFI;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Relationship;

class LoadRelationshipData extends AbstractFixture implements OFI {

  public function load (ObjectManager $manager) {
    $names = array(
      "John",
      "Mary",
      "Ann",
      "Tim"
    );

    $relationships = array();

    foreach ($names as $name) {
      $admin = $this->getReference('admin');
      $other = $this->getReference($name);

      $relationship = new Relationship();
      $relationship
      ->setFollower($other)
      ->setFollowed($admin)
      ->setCreatedAt(new \DateTime());

      $relationships[] = $relationship;

      $anotherRelationship = new Relationship();
      $anotherRelationship
      ->setFollower($admin)
      ->setFollowed($other)
      ->setCreatedAt(new \DateTime());

      $relationships[] = $anotherRelationship;
    }

    foreach ($relationships as $relationship) {
    	$manager->persist($relationship);
    }

    $manager->flush();
  }

  public function getOrder () {
    return 3;
  }

}