<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Entity\UserCredential;
use App\Domain\User\Enum\CredentialType;

interface UserCredentialRepositoryInterface
{
    public function save(UserCredential $cred): void;

    public function byId(int $id): ?UserCredential;

    public function byTypeAndIdentifier(CredentialType $type, string $identifier): ?UserCredential;

    /** @return list<UserCredential> */
    public function allOfUser(User $user): array;

    public function primaryOfUser(User $user): ?UserCredential;

    public function byTypeAndLogin(string $type, string $login): ?UserCredential;
}
