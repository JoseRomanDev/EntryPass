<?php

namespace App\Infrastructure\Console;

use App\Domain\Entity\Event;
use App\Domain\Entity\User;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:seed-demo',
    description: 'Inserta eventos de prueba y un usuario estándar para demostración.',
)]
class SeedDemoCommand extends Command
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventRepositoryInterface $eventRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Sembrar Usuario Estándar
        $userEmail = 'user@entrypass.com';
        $existingUser = $this->userRepository->findByEmail($userEmail);

        if (!$existingUser) {
            $user = new User(
                Uuid::v4()->toRfc4122(),
                'Jose Usuario',
                $userEmail,
                '',
                ['ROLE_USER']
            );
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'User123!');
            $user->updatePassword($hashedPassword);
            $this->userRepository->save($user);
            $io->note(sprintf('Usuario de prueba creado: %s / User123!', $userEmail));
        }

        // 2. Sembrar Eventos
        $eventsData = [
            [
                'title' => 'Rock Arena Festival 2026',
                'description' => 'El mayor festival de rock del año con bandas internacionales y ambiente increíble.',
                'date' => new \DateTimeImmutable('2026-07-15 19:00:00'),
                'price' => 55.50,
                'capacity' => 500,
                'category' => 'Festivales'
            ],
            [
                'title' => 'Conferencia Tech Future',
                'description' => 'Descubre las tendencias en IA, Blockchain y Web3 con expertos de todo el mundo.',
                'date' => new \DateTimeImmutable('2026-09-20 09:00:00'),
                'price' => 120.00,
                'capacity' => 200,
                'category' => 'Otros'
            ],
            [
                'title' => 'El Lago de los Cisnes',
                'description' => 'Disfruta de la obra clásica en el Teatro Nacional con una puesta en escena de lujo.',
                'date' => new \DateTimeImmutable('2026-11-05 20:30:00'),
                'price' => 35.00,
                'capacity' => 150,
                'category' => 'Teatro'
            ],
            [
                'title' => 'Concierto Sinfónico: Star Wars',
                'description' => 'La mítica banda sonora de John Williams interpretada por la Filarmónica.',
                'date' => new \DateTimeImmutable('2026-06-10 21:00:00'),
                'price' => 45.00,
                'capacity' => 1000,
                'category' => 'Conciertos'
            ]
        ];

        foreach ($eventsData as $data) {
            $event = new Event(
                Uuid::v4()->toRfc4122(),
                $data['title'],
                $data['description'],
                $data['date'],
                $data['price'],
                $data['capacity'],
                $data['category']
            );
            $this->eventRepository->save($event);
        }

        $io->success('Datos de demostración (usuario y eventos) cargados correctamente.');

        return Command::SUCCESS;
    }
}
