<?php

namespace App\Application\CommandHandler;

use App\Application\Command\RegisterUserCommand;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



#[AsMessageHandler]
readonly class RegisterUserHandler{
    
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ){}

    public function __invoke(RegisterUserCommand $command):void{
        $user = new User(
            $command->id,
            $command->name,
            $command->email,
            '',
            $command->roles
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $command->plainPassword);
        $user->updatePassword($hashedPassword);

        $this->userRepository->save($user);
    }


}

?>