<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class DoctrineEventRepository extends ServiceEntityRepository implements EventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        // Le decimos a Doctrine que este repositorio gestiona la entidad Event del Dominio
        parent::__construct($registry, Event::class);
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Event
    {
        return $this->find($id);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}