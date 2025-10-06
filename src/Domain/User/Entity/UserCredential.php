<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Shared\Contract\TimestampAwareInterface;
use App\Domain\Shared\Trait\Timestamp;
use App\Domain\User\Enum\CredentialType; // <─ ВАЖНО: импорт enum
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(
    name: 'user_credentials',
    indexes: [
        new ORM\Index(name: 'idx_cred_user', columns: ['user_id']),
        new ORM\Index(name: 'idx_cred_login', columns: ['login']),
    ],
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_cred_type_login', columns: ['type', 'login']),
    ]
)]
#[UniqueEntity('login', message: 'user.unique_field', groups: ['default', 'unique_email'])]
class UserCredential implements TimestampAwareInterface
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'credentials')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    // БЫЛО: string + константы TYPE_*
    // СТАЛО: enum (Doctrine 3 маппит в VARCHAR)
    #[ORM\Column(length: 16, enumType: CredentialType::class)]
    private CredentialType $type;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $login;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resetTokenExpired = null;

    public function __construct(CredentialType $type, User $user, string $login)
    {
        $this->type = $type;
        $this->user = $user;
        $this->login = $login;
        $this->stampOnCreate();
    }

    // getters/setters
    public function id(): ?int
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function type(): CredentialType
    {
        return $this->type;
    }

    public function login(): string
    {
        return $this->login;
    }

    public function password(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $hash): void
    {
        $this->password = $hash;
        $this->stampOnUpdate();
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function markVerified(): void
    {
        $this->isVerified = true;
        $this->stampOnUpdate();
    }

    public function verificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $code): void
    {
        $this->verificationCode = $code;
        $this->stampOnUpdate();
    }

    public function resetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $token, ?\DateTimeImmutable $expiresAt): void
    {
        $this->resetToken = $token;
        $this->resetTokenExpired = $expiresAt;
        $this->stampOnUpdate();
    }
}
