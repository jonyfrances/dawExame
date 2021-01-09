<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity
 */
class Users
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password_digest", type="string", length=255, nullable=true)
     */
    private $passwordDigest;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remember_digest", type="string", length=255, nullable=true)
     */
    private $rememberDigest;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="admin", type="boolean", nullable=true)
     */
    private $admin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="activation_digest", type="string", length=255, nullable=true)
     */
    private $activationDigest;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="activated", type="boolean", nullable=true)
     */
    private $activated;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="activated_at", type="datetime", nullable=true)
     */
    private $activatedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reset_digest", type="string", length=255, nullable=true)
     */
    private $resetDigest;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="reset_sent_at", type="datetime", nullable=true)
     */
    private $resetSentAt;

    /**
     * @var json|null
     *
     * @ORM\Column(name="roles", type="json", nullable=true)
     */
    private $roles;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPasswordDigest(): ?string
    {
        return $this->passwordDigest;
    }

    public function setPasswordDigest(?string $passwordDigest): self
    {
        $this->passwordDigest = $passwordDigest;

        return $this;
    }

    public function getRememberDigest(): ?string
    {
        return $this->rememberDigest;
    }

    public function setRememberDigest(?string $rememberDigest): self
    {
        $this->rememberDigest = $rememberDigest;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(?bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getActivationDigest(): ?string
    {
        return $this->activationDigest;
    }

    public function setActivationDigest(?string $activationDigest): self
    {
        $this->activationDigest = $activationDigest;

        return $this;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(?bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    public function getActivatedAt(): ?\DateTimeInterface
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?\DateTimeInterface $activatedAt): self
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }

    public function getResetDigest(): ?string
    {
        return $this->resetDigest;
    }

    public function setResetDigest(?string $resetDigest): self
    {
        $this->resetDigest = $resetDigest;

        return $this;
    }

    public function getResetSentAt(): ?\DateTimeInterface
    {
        return $this->resetSentAt;
    }

    public function setResetSentAt(?\DateTimeInterface $resetSentAt): self
    {
        $this->resetSentAt = $resetSentAt;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


}
