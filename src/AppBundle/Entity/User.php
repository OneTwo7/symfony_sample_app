<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Encoder\CustomEncoder;

/**
 * AppUser
 *
 * @ORM\Table(name="users", indexes={@ORM\Index(name="email_idx", columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \Serializable {

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
        $raw = $this->getPlainPassword();
        $salt = random_bytes(22);
        $this->setPassword($encoder->encodePassword($raw, $salt));
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
     * @ORM\Column(type="string", length=54)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 54,
     *      maxMessage = "Your username is too long (max: 50 characters)"
     * )
     */
    private $username;

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
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *      "this.getPlainPassword() === this.getPasswordConfirmation()",
     *      message="Password confirmation doesn't match password!"
     * )
     */
    private $passwordConfirmation;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

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
     * Set username
     *
     * @param string $username
     *
     * @return AppUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
     * Set password
     *
     * @param string $password
     *
     * @return AppUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPlainPassword ($plainPassword) {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword () {
        return $this->plainPassword;
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

    public function getRoles () {
        return array('ROLE_USER');
    }

    public function getSalt () {
        return null;
    }

    public function eraseCredentials () {}

    public function serialize () {
        return serialize(array(
            $this->id,
            $this->username
        ));
    }

    public function unserialize ($serialized) {
        list (
            $this->id,
            $this->username
        ) = unserialize($serialized);
    }
}
