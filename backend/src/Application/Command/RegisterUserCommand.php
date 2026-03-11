<?php

namespace App\Application\Command;

readonly class RegisterUserCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $plainPassword,
        public array $roles = ['ROLE_USER']
    ){}
}


?>