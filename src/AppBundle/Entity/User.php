<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Micropost", mappedBy="user",
     cascade={"remove", "persist", "refresh", "merge", "detach"})
     * @ORM\OrderBy({"createdAt"="desc"})
     */
    private $microposts;

    /**
     * @ORM\OneToMany(targetEntity="Relationship", mappedBy="follower",
     cascade={"remove", "persist", "refresh", "merge", "detach"})
     */
    private $active_relationships;

    /**
     * @ORM\OneToMany(targetEntity="Relationship", mappedBy="followed",
     cascade={"remove", "persist", "refresh", "merge", "detach"})
     */
    private $passive_relationships;

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
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your password is too short (min: 6 characters)"
     * )
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    private $oldPassword;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $admin = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $activated = false;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $activationDigest;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $resetDigest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reset_sent_at", type="datetime", nullable=true)
     */
    private $resetSentAt;

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

    public function setMicroposts (ArrayCollection $microposts) {
        $this->microposts = $microposts;

        return $this;
    }

    public function getMicroposts () {
        return $this->microposts;
    }

    public function setActiveRelationships (ArrayCollection $active_relationships) {
        $this->active_relationships = $active_relationships;

        return $this;
    }

    public function getActiveRelationships () {
        return $this->active_relationships;
    }

    public function setPassiveRelationships (ArrayCollection $passive_relationships) {
        $this->passive_relationships = $passive_relationships;

        return $this;
    }

    public function getPassiveRelationships () {
        return $this->passive_relationships;
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

    public function setOldPassword ($oldPassword) {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getOldPassword () {
        return $this->oldPassword;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     *
     * @return User
     */
    public function setAdmin ($admin) {
        $this->admin = $admin;

        return $this;
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
     * Set resetToken
     *
     * @param string $resetToken
     *
     * @return User
     */
    public function setResetToken ($resetToken) {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Get resetToken
     *
     * @return string
     */
    public function getResetToken () {
        return $this->resetToken;
    }

    /**
     * Set resetDigest
     *
     * @param string $resetDigest
     *
     * @return User
     */
    public function setResetDigest ($resetDigest) {
        $this->resetDigest = $resetDigest;

        return $this;
    }

    /**
     * Get resetDigest
     *
     * @return string
     */
    public function getResetDigest () {
        return $this->resetDigest;
    }

    /**
     * Set resetSentAt
     *
     * @param \DateTime $resetSentAt
     *
     * @return User
     */
    public function setResetSentAt ($resetSentAt) {
        $this->resetSentAt = $resetSentAt;

        return $this;
    }

    /**
     * Get resetSentAt
     *
     * @return \DateTime
     */
    public function getResetSentAt () {
        return $this->resetSentAt;
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

    public function gravatar ($size = 80) {
        $email = $this->getEmail();
        return "https://www.gravatar.com/avatar/" . 
                md5(strtolower(trim($email))) . "?s=" . $size;
    }

    public function isFollowing ($user) {
        $rels = $this->getActiveRelationships();
        foreach ($rels as $rel) {
            if ($rel->getFollowed() == $user) {
                return true;
            }
        }
        return false;
    }

}
