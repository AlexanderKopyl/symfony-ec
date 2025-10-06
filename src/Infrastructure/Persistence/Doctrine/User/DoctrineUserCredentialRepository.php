<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Entity\UserCredential;
use App\Domain\User\Enum\CredentialType;
use App\Domain\User\Repository\UserCredentialRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<UserCredential> */
class DoctrineUserCredentialRepository extends ServiceEntityRepository implements UserCredentialRepositoryInterface
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
        /** @var UserCredential|null $res */
        $res = $this->find($id);
        return $res;
    }

    public function byTypeAndLogin(CredentialType $type, string $login): ?UserCredential
    {
        /** @var UserCredential|null $res */
        $res = $this->createQueryBuilder('c')
            ->andWhere('c.type = :t AND c.login = :l')
            ->setParameter('t', $type)   // enum маппится Doctrine ORM 3 сам
            ->setParameter('l', $login)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $res;
    }

    /** @return list<UserCredential> */
    public function allOfUser(User $user): array
    {
        /** @var list<UserCredential> $rows */
        $rows = $this->findBy(['user' => $user], ['id' => 'ASC']);
        return $rows;
    }

    public function primaryOfUser(User $user): ?UserCredential
    {
        /** @var UserCredential|null $res */
        $res = $this->createQueryBuilder('c')
            ->andWhere('c.user = :u AND c.primary = true')
            ->setParameter('u', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $res;
    }

    // Если кому-то нужен старый метод — просто проксируем (опционально)
    public function byTypeAndIdentifier(CredentialType $type, string $identifier): ?UserCredential
    {
        return $this->byTypeAndLogin($type, $identifier);
    }
}
