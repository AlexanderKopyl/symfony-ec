<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Entity\UserCredential;
use App\Domain\User\Enum\CredentialType;
use App\Domain\User\Repository\UserCredentialRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineUserCredentialRepository extends ServiceEntityRepository implements UserCredentialRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCredential::class);
    }

    public function save(UserCredential $cred): void
    {
        $em = $this->getEntityManager();
        $em->persist($cred);
        $em->flush();
    }

    public function byId(int $id): ?UserCredential
    {
        return $this->find($id);
    }

    public function byTypeAndIdentifier(CredentialType $type, string $identifier): ?UserCredential
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :t AND c.identifier = :i')
            ->setParameter('t', $type)
            ->setParameter('i', $identifier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function allOfUser(User $user): array
    {
        /** @var list<UserCredential> $rows */
        $rows = $this->findBy(['user' => $user], ['primary' => 'DESC', 'id' => 'ASC']);

        return $rows;
    }

    public function primaryOfUser(User $user): ?UserCredential
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :u AND c.primary = true')
            ->setParameter('u', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byTypeAndLogin(string $type, string $login): ?UserCredential
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :t AND c.login = :l')
            ->setParameter('t', $type)->setParameter('l', $login)
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
