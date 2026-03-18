<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Purchase;
use App\Domain\Entity\User;
use App\Domain\Repository\PurchaseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 */
class DoctrinePurchaseRepository extends ServiceEntityRepository implements PurchaseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function save(Purchase $purchase): void
    {
        $this->getEntityManager()->persist($purchase);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Purchase
    {
        return $this->find($id);
    }

    /** @return Purchase[] */
    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['purchasedAt' => 'DESC']);
    }
}
