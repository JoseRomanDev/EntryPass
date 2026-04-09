<?php

namespace App\Domain\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface {
    private string $id;
    private string $name;
    private string $email;
    private string $password;   
    private array $roles;


    public function __construct(string $id, string $name, string $email, string $password, array $roles = ['ROLE_USER']) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function getId(): string { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getName(): string { return $this->name; }
    public function getPassword(): string { return $this->password; }
    public function getRoles(): array { return array_unique($this->roles); }
    public function getUserIdentifier(): string { return $this->email; }
    public function eraseCredentials(): void { }

    public function updatePassword(string $hashedPassword): void 
    {
        $this->password = $hashedPassword;
    }


}