<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Micropost
 *
 * @ORM\Table(name="microposts", indexes={@ORM\Index(name="micropost_idx", columns={"user_id", "created_at"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MicropostRepository")
 */
class Micropost {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=140)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 140,
     *      maxMessage = "Micropost is too long (max: 140 characters)"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(
     *      maxSize="5M",
     *      mimeTypes={"image/jpg", "image/jpeg", "image/gif",
     "image/png"})
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="microposts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set content
     *
     * @param string $content
     *
     * @return Micropost
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return Micropost
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Micropost
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Micropost
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

