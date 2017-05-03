<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Encoder\CustomEncoder;

/**
 * AppUser
 *
 * @ORM\Table(name="app_user", indexes={@ORM\Index(name="email_idx", columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppUserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AppUser {

    public function verify ($str) {
        $encoder = new CustomEncoder;
        $encoded = $this->getPasswordDigest();
        return $encoder->isPasswordValid($encoded, $str, 'salt');
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prepareEntity () {
        $this->setEmail(strtolower($this->getEmail()));
        $this->setUpdatedAt(new \DateTime());

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function encodePassword () {
        $encoder = new CustomEncoder;
        $raw = $this->getPassword();
        $salt = random_bytes(22);
        $this->setPasswordDigest($encoder->encodePassword($raw, $salt));
    }

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
     * @ORM\Column(name="name", type="string", length=54)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 54,
     *      maxMessage = "Your name is too long (max: 50 characters)"
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Your email is too long (max: 255 characters)"
     * )
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min=6,
     *      minMessage = "Your password is too short (min: 6 characters)",
     *      max=4096,
     *      maxMessage = "Your password is too long (max: 4096 characters)"
     * )
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *      "this.getPassword() === this.getPasswordConfirmation()",
     *      message="Password and password confirmation don't match!"
     * )
     */
    private $passwordConfirmation;

    /**
     * @var string
     *
     * @ORM\Column(name="password_digest", type="string", length=64)
     */
    private $passwordDigest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


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
     * Set name
     *
     * @param string $name
     *
     * @return AppUser
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return AppUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set passwordDigest
     *
     * @param string $passwordDigest
     *
     * @return AppUser
     */
    public function setPasswordDigest($passwordDigest)
    {
        $this->passwordDigest = $passwordDigest;

        return $this;
    }

    /**
     * Get passwordDigest
     *
     * @return string
     */
    public function getPasswordDigest()
    {
        return $this->passwordDigest;
    }

    public function setPassword ($password) {
        $this->password = $password;

        return $this;
    }

    public function getPassword () {
        return $this->password;
    }

    public function setPasswordConfirmation ($passwordConfirmation) {
        $this->passwordConfirmation = $passwordConfirmation;

        return $this;
    }

    public function getPasswordConfirmation () {
        return $this->passwordConfirmation;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AppUser
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

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return AppUser
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

