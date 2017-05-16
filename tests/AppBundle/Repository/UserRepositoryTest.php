<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp () {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testDoctrine () {
        $users = $this->em
            ->getRepository('AppBundle:User')
            ->findByUsername('Greg')
        ;

        $this->assertTrue(true);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown () {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

}