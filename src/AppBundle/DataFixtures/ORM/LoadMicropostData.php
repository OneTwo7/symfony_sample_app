<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Micropost;

class LoadUserData implements FixtureInterface {

  public function load (ObjectManager $manager) {
    $userAdmin = new User();
    $userAdmin
    ->setUsername('admin')
    ->setEmail('example@railstutorial.org')
    ->setPlainPassword('foobar')
    ->setAdmin(true)
    ->setActivated(true);

    $users = array();
    for ($i = 0; $i < 50; $i++) {
    	$user = new User();
    	$user
    	->setUsername('user-' . $i)
	    ->setEmail('user-' . $i . '@example.com')
	    ->setPlainPassword('foobar')
	    ->setActivated(true);

	    $users[] = $user;
    }

    $manager->persist($userAdmin);

    foreach ($users as $user) {
    	$manager->persist($user);
    }

    $micropost = new Micropost();
    $micropost->setContent('Hello, everyone!');
    $micropost->setUser($userAdmin);

    $manager->persist($micropost);
    $manager->flush();
  }

}