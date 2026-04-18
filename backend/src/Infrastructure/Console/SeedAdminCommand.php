<?php

namespace App\Infrastructure\Console;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:seed-admin',
    description: 'Inserta el usuario administrador por defecto si no existe.',
)]
class SeedAdminCommand extends Command
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = 'admin@entrypass.com';

        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser) {
            $io->info(sprintf('El usuario administrador (%s) ya existe.', $email));
            return Command::SUCCESS;
        }

        $user = new User(
            Uuid::v4()->toRfc4122(),
            'Admin EntryPass',
            $email,
            '',
            ['ROLE_ADMIN']
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'Admin123!');
        $user->updatePassword($hashedPassword);

        $this->userRepository->save($user);

        $io->success(sprintf('Usuario administrador (%s) creado exitosamente.', $email));

        return Command::SUCCESS;
    }
}
