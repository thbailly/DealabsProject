<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    private $plainPassword;

    /**
     * @ORM\ManyToMany(targetEntity=Deal::class, mappedBy="userSave")
     */
    private $savedDeals;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\OneToMany(targetEntity=Badge::class, cascade={"persist","remove"}, mappedBy="user")
     */
    private $badges;

    /**
     * @ORM\OneToMany(targetEntity=Alert::class, cascade={"persist","remove"}, mappedBy="user", orphanRemoval=true)
     */
    private $alerts;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbVote;


    /**
     * @ORM\OneToMany(targetEntity=ApiToken::class, mappedBy="user")
     */
    private $apiTokens;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function __construct()
    {
        $this->savedDeals = new ArrayCollection();
        $this->badges = new ArrayCollection();
        $this->nbVote = 0;
        $this->apiTokens = new ArrayCollection();
        $this->alerts = new ArrayCollection();
    }

    public function addDeal(Deal $deal)
    {
        $this->savedDeals->add($deal);
        $deal->addToUserSave($this);
    }

    public function addBadge(Badge $badge)
    {
        $this->badges->add($badge);
        $badge->setUser($this);
    }

    public function getSavedDeals()
    {
        return $this->savedDeals;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getBadges()
    {
        return $this->badges;
    }

    public function getNbVote()
    {
        return $this->nbVote;
    }

    public function setNbVote(int $nbVote)
    {
        $this->nbVote += $nbVote;

        return $this;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
        return $this->active;
    }

    public function getApiTokens()
    {
        return $this->apiTokens;
    }

    public function setApiTokens(?string $apiToken): self
    {
        $this->apiTokens = $apiToken;

        return $this;
    }

    public function addApiToken($token)
    {
        $this->apiTokens->add($token);
    }

    public function getAlerts()
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert)
    {
        $this->alerts->add($alert);
        $alert->setUser($this);
    }

    public function removeAlert(Alert $alert)
    {
        $this->alerts->removeElement($alert);
        $alert->setUser(null);
    }
}
