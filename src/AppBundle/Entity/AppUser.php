<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppUserRepository")
 * @UniqueEntity(fields="email",message="Cet email est déjà utilisé")
 * @UniqueEntity(fields="username",message="Ce Nom d\'utilisateur est déjà pris")
 */
class AppUser implements AdvancedUserInterface,\Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255,unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string",length=255,unique=true)
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * @Assert\NotBlank(message="merci de remplir ce champs")
     * @Assert\Length(max=255)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string",length=64)
     * 
     */
    private $password;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $AccountConfirmationRequestedAt;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $AccountConfirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $passwordResetRequestedAt;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $passwordResetToken;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->roles = [];
        $this->isActive = false;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */ 
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * 
     */ 
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of roles
     */ 
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */ 
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }
    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }
    public function eraseCredentials()
    {
        
    }
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        ));
    }
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        )= unserialize($serialized);
    }

    /**
     * Get the value of AccountConfirmationToken
     *
     * @return  string
     */ 
    public function getAccountConfirmationToken()
    {
        return $this->AccountConfirmationToken;
    }

    /**
     * Set the value of AccountConfirmationToken
     *
     * @param    $AccountConfirmationToken
     *
     * @return  self
     */ 
    public function setAccountConfirmationToken( $AccountConfirmationToken)
    {
        $this->AccountConfirmationToken = $AccountConfirmationToken;

        return $this;
    }

    /**
     * Get the value of AccountConfirmationRequestedAt
     *
     */ 
    public function getAccountConfirmationRequestedAt()
    {
        return $this->AccountConfirmationRequestedAt;
    }

    /**
     * Set the value of AccountConfirmationRequestedAt
     *
     * @param  $AccountConfirmationRequestedAt
     *
     * @return  self
     */ 
    public function setAccountConfirmationRequestedAt( $AccountConfirmationRequestedAt)
    {
        $this->AccountConfirmationRequestedAt = $AccountConfirmationRequestedAt;

        return $this;
    }

    /**
     * Set the value of isActive
     *
     * @return  self
     */ 
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the value of isActive
     */ 
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Get the value of passwordResetToken
     *
     * @return  string
     */ 
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * Set the value of passwordResetToken
     *
     * @param    $passwordResetToken
     *
     * @return  self
     */ 
    public function setPasswordResetToken( $passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    /**
     * Get the value of passwordResetRequestedAt
     *
     * @return  
     */ 
    public function getPasswordResetRequestedAt()
    {
        return $this->passwordResetRequestedAt;
    }

    /**
     * Set the value of passwordResetRequestedAt
     *
     * @param    $passwordResetRequestedAt
     *
     * @return  self
     */ 
    public function setPasswordResetRequestedAt( $passwordResetRequestedAt)
    {
        $this->passwordResetRequestedAt = $passwordResetRequestedAt;

        return $this;
    }
}