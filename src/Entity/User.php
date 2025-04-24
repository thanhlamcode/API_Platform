<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\LoginController;
use App\Controller\RegisterController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/user/register',
            inputFormats: ['json' => ['application/json']],
            controller: RegisterController::class,
            output: false
        ),
        new Post(
            uriTemplate: '/user/login',
            inputFormats: ['json' => ['application/json']],
            controller: LoginController::class,
            output: false
        )
    ]
)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    private string $username;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'json')]
    #[Groups(['read'])] // CHỈ cho phép hiển thị khi output
    private array $roles = ['ROLE_USER'];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

//    public function setRoles(array $roles): self
//    {
//        $this->roles = $roles;
//        return $this;
//    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return $this->username;
    }

    public function setPassword($hashedPassword)
    {
        return $this->password = $hashedPassword;
    }

    public function setRoles(array $array)
    {
        return $this->roles = $array;
    }

    public function getPassword(): ?string
    {
        // TODO: Implement getPassword() method.
        return $this->password;
    }
}