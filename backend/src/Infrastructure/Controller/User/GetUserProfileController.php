<?php

namespace App\Infrastructure\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Domain\Entity\User;


class GetUserProfileController extends AbstractController
{
    #[Route('/api/me', name: 'api_user_me', methods: ['GET'])]
    public function __invoke(): JsonResponse{
    //**@var UserInterface $user */
    $user = $this->getUser();

    if (!$user) {
            return new JsonResponse(['message' => 'Token no válido o usuario no encontrado'], 401);
        }

    return new JsonResponse([
        'email' => $user->getUserIdentifier(),
        'roles' => $user->getRoles(),
        'name' => $user->getName()
    ]);
    }
}


?>