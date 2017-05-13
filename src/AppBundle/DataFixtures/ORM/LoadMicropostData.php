<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Micropost;

class LoadUserData implements FixtureInterface {

  public function load (ObjectManager $manager) {
    $users = $manager->getRepository('AppBundle:User')->findAll();
    $user = $users[0];

    $micropost = new Micropost();
    $micropost->setContent('Hello, everyone!');
    $micropost->setUser($user);

    $manager->persist($micropost);
    $manager->flush();
  }

}