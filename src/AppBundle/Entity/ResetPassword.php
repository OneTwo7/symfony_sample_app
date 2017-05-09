<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ResetPassword
 *
 * @ORM\Table(name="reset_password")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResetPasswordRepository")
 */
class ResetPassword {

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
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
   * Get id
   *
   * @return int
   */
  public function getId () {
    return $this->id;
  }

  public function setEmail ($email) {
    $this->email = $email;

    return $this;
  }

  public function getEmail () {
    return $this->email;
  }

}

