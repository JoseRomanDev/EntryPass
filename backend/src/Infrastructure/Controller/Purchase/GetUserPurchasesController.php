<?php

namespace App\Infrastructure\Controller\Purchase;

use App\Domain\Repository\PurchaseRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\Purchase;
use App\Domain\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetUserPurchasesController extends AbstractController
{
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private UserRepositoryInterface     $userRepository
    ) {}

    #[Route('/api/purchases/my', name: 'api_get_my_purchases', methods: ['GET'])]
    public function myPurchases(): JsonResponse
    {
        $userEmail = $this->getUser()->getUserIdentifier();
        $user      = $this->userRepository->findByEmail($userEmail);

        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no encontrado'], 404);
        }

        $purchases = $this->purchaseRepository->findByUser($user);

        $data = array_map(function (Purchase $purchase) {
            return [
                'id'          => $purchase->getId(),
                'eventId'     => $purchase->getEvent()->getId(),
                'eventTitle'  => $purchase->getEvent()->getTitle(),
                'eventDate'   => $purchase->getEvent()->getDate()->format('d-m-Y H:i'),
                'quantity'    => $purchase->getQuantity(),
                'totalPrice'  => $purchase->getTotalPrice(),
                'status'      => $purchase->getStatus(),
                'purchasedAt' => $purchase->getPurchasedAt()->format('d-m-Y H:i:s'),
                'tickets'     => array_map(
                    fn(Ticket $t) => [
                        'id'         => $t->getId(),
                        'qrCodeHash' => $t->getQrCodeHash(),
                        'isUsed'     => $t->isUsed(),
                    ],
                    $purchase->getTickets()->toArray()
                ),
            ];
        }, $purchases);

        return new JsonResponse($data, 200);
    }
}
