<?php
declare(strict_types=1);

require_once __DIR__ . '/AuthRepository.php';

final class AuthService
{
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->repository->findByEmail($email);
        if (!$user) {
            return null;
        }
        if (!password_verify($password, $user['password'] ?? '')) {
            return null;
        }
        return $user;
    }

    public function emailExists(string $email): bool
    {
        return $this->repository->emailExists($email);
    }

    public function register(string $username, string $email, string $password): int
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->repository->createUser($username, $email, $passwordHash);
    }
}
