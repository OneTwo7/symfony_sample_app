<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface as OFI;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface as CAI;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OFI, CAI {

  private $container;

  public function setContainer (ContainerInterface $container = null) {
    $this->container = $container;
  }


  public function load (ObjectManager $manager) {
    $encoder = $this->container->get('security.password_encoder');

    $adminUser = new User();
    $password = $encoder->encodePassword($adminUser, 'foobar');
    $adminUser
    ->setUsername('admin')
    ->setEmail('example@railstutorial.org')
    ->setPassword($password)
    ->setAdmin(true)
    ->setActivated(true);

    $names = array(
      "John",
      "Mary",
      "Ann",
      "Tim"
    );

    $users = array();

    $users[] = $adminUser;

    for ($i = 0; $i < sizeof($names); $i++) {
      $user = new User();
      $password = $encoder->encodePassword($user, 'foobar');
      $user
      ->setUsername($names[$i])
      ->setEmail($names[$i] . '@example.com')
      ->setPassword($password)
      ->setActivated(true);

      $users[] = $user;
    }

    for ($i = 0; $i < 45; $i++) {
    	$user = new User();
      $password = $encoder->encodePassword($user, 'foobar');
    	$user
    	->setUsername('user-' . $i)
	    ->setEmail('user-' . $i . '@example.com')
	    ->setPassword($password)
	    ->setActivated(true);

	    $users[] = $user;
    }

    foreach ($users as $user) {
    	$manager->persist($user);
    }

    $manager->flush();

    $this->addReference('admin', $adminUser);

    for ($i = 0; $i < sizeof($names); $i++) {
      $this->addReference($names[$i], $users[$i + 1]);
    }
  }

  public function getOrder () {
    return 1;
  }

}