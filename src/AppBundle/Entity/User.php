<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use AppBundle\Encoder\CustomEncoder;

/**
 * AppUser
 *
 * @ORM\Table(name="users", indexes={@ORM\Index(name="email_idx", columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements AdvancedUserInterface, \Serializable {

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
     * @ORM\PreUpdate
     */
    public function encodePassword () {
        $encoder = new CustomEncoder;
        $raw = $this->getPlainPassword();
        $salt = random_bytes(22);
        $this->setPassword($encoder->encodePassword($raw, $salt));
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function encodeActivationDigest () {
        $encoder = new CustomEncoder;
        $raw = $this->getActivationToken();
        $salt = random_bytes(22);
        $this->setActivationDigest($encoder->encodePassword($raw, $salt));
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
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $admin = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $activated = false;

    private $activationToken;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $activationDigest;

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
     * @return string
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
     * @return User
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
     * @return User
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
     * Get admin
     *
     * @return boolean
     */
    public function getAdmin () {
        return $this->admin;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return User
     */
    public function setActivated ($activated) {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean
     */
    public function getActivated () {
        return $this->activated;
    }

    /**
     * Set activationToken
     *
     * @param string $activationToken
     *
     * @return User
     */
    public function setActivationToken ($activationToken) {
        $this->activationToken = $activationToken;

        return $this;
    }

    /**
     * Get activationToken
     *
     * @return string
     */
    public function getActivationToken () {
        return $this->activationToken;
    }

    /**
     * Set activationDigest
     *
     * @param string $activationDigest
     *
     * @return User
     */
    public function setActivationDigest ($activationDigest) {
        $this->activationDigest = $activationDigest;

        return $this;
    }

    /**
     * Get activationDigest
     *
     * @return string
     */
    public function getActivationDigest () {
        return $this->activationDigest;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * @return User
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
        if ($this->getAdmin()) {
            return array('ROLE_ADMIN');
        } else {
            return array('ROLE_USER');
        }
    }

    public function getSalt () {
        return null;
    }

    public function eraseCredentials () {}

    public function isAccountNonExpired () {
        return true;
    }

    public function isAccountNonLocked () {
        return true;
    }

    public function isCredentialsNonExpired () {
        return true;
    }

    public function isEnabled () {
        return $this->getActivated();
    }

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

    // Custom methods

    public function generateToken () {
        $result = array();
        $chars =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_";
        $length = strlen($chars);
        for ($i = 0; $i < 22; $i++) {
            $result[] = $chars[rand(0, $length - 1)];
        }
        return implode("", $result);
    }

    public function activate ($activationToken) {
        $encoder = new CustomEncoder;
        $activationDigest = $this->getActivationDigest();

        return
        $encoder->isPasswordValid($activationDigest, $activationToken, 'salt');
    }

}
