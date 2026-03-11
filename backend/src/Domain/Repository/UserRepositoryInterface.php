<?php

namespace App\Domain\Repository;
use App\Domain\Entity\User;

interface UserRepositoryInterface{
    public function save(User $user):void;
    public function findAll():array;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
}
?>
