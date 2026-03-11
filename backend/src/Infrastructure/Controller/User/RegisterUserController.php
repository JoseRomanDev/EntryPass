<?php

namespace App\Infrastructure\Controller\User;

use App\Application\Command\RegisterUserCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class RegisterUserController extends AbstractController
{
    #[Route('/api/register', name: 'api_register_user', methods: ['POST'])]
    public function __invoke(Request $request, MessageBusInterface $bus): JsonResponse
    {
        
        $data = json_decode($request->getContent(), true);

        $id = Uuid::v4()->toRfc4122();

        $roles =$data['roles'] ?? ['ROLE_USER'];


        $command = new RegisterUserCommand(
            $id,
            $data['name'],
            $data['email'],
            $data['password'],
            $roles
        );

        $bus->dispatch($command);

        return new JsonResponse(['message' => 'User Registered sucessfully', 'id'=>$id], Response::HTTP_CREATED);
    }
}