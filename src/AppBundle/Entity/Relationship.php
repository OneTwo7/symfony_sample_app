<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Relationship
 *
 * @ORM\Table(name="relationship", indexes={
 *      @ORM\Index(name="follower_idx", columns={"follower_id"}),
 *      @ORM\Index(name="followed_idx", columns={"followed_id"}),
 *  },
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="relationship_idx", columns={
 *          "follower_id", "followed_id"
 *      })
 *  })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RelationshipRepository")
 */
class Relationship {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="active_relationships")
     * @ORM\JoinColumn(name="follower_id", referencedColumnName="id")
     */
    private $follower;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="passive_relationships")
     * @ORM\JoinColumn(name="followed_id", referencedColumnName="id")
     */
    private $followed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set follower
     *
     * @param integer $follower
     *
     * @return Relationship
     */
    public function setFollower($follower)
    {
        $this->follower = $follower;

        return $this;
    }

    /**
     * Get follower
     *
     * @return int
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * Set followed
     *
     * @param integer $followed
     *
     * @return Relationship
     */
    public function setFollowed($followed)
    {
        $this->followed = $followed;

        return $this;
    }

    /**
     * Get followed
     *
     * @return int
     */
    public function getFollowed()
    {
        return $this->followed;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Relationship
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}

