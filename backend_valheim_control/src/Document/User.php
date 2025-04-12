<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Ramsey\Uuid\Uuid;

#[MongoDB\Document(repositoryClass: \App\Repository\UserRepository::class)]
#[MongoDB\UniqueIndex(keys: ["email" => 1], options: ["unique" => true, "name" => "UNIQ_IDENTIFIER_EMAIL"])]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private ?string $uuid = null;

    #[MongoDB\Field(type: "string")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[MongoDB\Field(type: "collection")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[MongoDB\Field(type: "string")]
    private ?string $password = null;

    #[MongoDB\Field(type: "bool")]
    private ?bool $mustChangePassword = null;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeImmutable $createdAt = null;

    #[MongoDB\Field(type: "date", nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[MongoDB\Field(type: "string")]
    private ?string $apiToken = null;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = Uuid::uuid7()->toString();
        $this->apiToken = bin2hex(random_bytes(20));
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function isMustChangePassword(): ?bool
    {
        return $this->mustChangePassword;
    }

    public function setMustChangePassword(bool $mustChangePassword): static
    {
        $this->mustChangePassword = $mustChangePassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles temporairement, effacez-les ici
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;
        return $this;
    }
}