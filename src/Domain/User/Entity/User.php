<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Application\User\DTO\TokenPair;
use App\Domain\Shared\Contract\TimestampAwareInterface;
use App\Domain\Shared\Trait\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TimestampAwareInterface
{
    use Timestamp;

    public const ROLE_USER = 'ROLE_USER';
    public const PROPERTY_PREFIX = 'u';

    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Serializer\Groups(['user_view'])]
    private ?int $id = null;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(name: 'password', type: 'string', length: 80, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(name: 'email', type: 'string', length: 45, nullable: true)]
    #[Assert\Email(message: 'employee.email.assert_message')]
    #[Serializer\Groups(['user_view'])]
    #[Assert\Length(min: 4, minMessage: 'datatype.string.length_min', maxMessage: 'datatype.string.length_max')]
    private ?string $email = null;

    #[ORM\Column(name: 'phone', type: 'string', length: 15, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(min: 12, max: 15, minMessage: 'user.phone.length_min', maxMessage: 'user.phone.length_max')]
    #[Serializer\Groups(['user_view'])]
    private ?string $phone = null;

    #[ORM\Column(name: 'google_id', type: 'string', length: 100, nullable: true)]
    #[Serializer\Groups(['user_view'])]
    private ?string $googleId = null;

    /**
     * @var list<string> $roles
     */
    #[ORM\Column(name: 'roles', type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $verified = false;

    #[ORM\Column(name: 'password_token', type: 'string', length: 50, nullable: true)]
    private ?string $passwordToken = null;

    #[ORM\Column(name: 'password_token_expired', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $passwordTokenExpired = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Serializer\Groups(['user_view'])]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Serializer\Groups(['user_view'])]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Serializer\Groups(['user_view'])]
    private ?string $patronymic = null;

    #[ORM\Column(name: 'birth_date', type: 'date', nullable: true)]
    #[Serializer\Groups(['user_view'])]
    #[Serializer\SerializedName('birthDate')]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(name: 'gender', type: 'smallint', nullable: true)]
    #[Serializer\Groups(['user_view'])]
    private ?int $gender = null;

    private ?string $emailVerificationCode = null;
    private ?string $phoneVerificationCode = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ocCustomerId = null;

    /** @var Collection<int, UserCredential> */
    #[ORM\OneToMany(targetEntity: UserCredential::class, mappedBy: 'user')]
    private Collection $credentials;

    private ?string $lastIp = null;

    #[Serializer\Groups(['jwt_token_view'])]
    private ?TokenPair $newTokens = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $ordersSyncAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastLogin = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['user_view'])]
    private ?int $bonusPoints = 0;

    #[ORM\OneToOne(
        targetEntity: UserAvatar::class,
        mappedBy: 'user',
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private ?UserAvatar $avatar = null;

    public function __construct()
    {
        $this->credentials = new ArrayCollection();
        $this->stampOnCreate();
    }

    // ===== Security =====

    /**
     * @return non-empty-string
     *
     * @phpstan-return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        $email = null !== $this->email ? trim($this->email) : '';
        if ('' !== $email) {
            return $email;
        }

        $phone = null !== $this->phone ? trim($this->phone) : '';
        if ('' !== $phone) {
            return $phone;
        }

        return null !== $this->id ? (string) $this->id : 'user#'.spl_object_id($this);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $hash): void
    {
        $this->password = $hash;
    }

    public function eraseCredentials(): void
    {
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!\in_array(self::ROLE_USER, $roles, true)) {
            $roles[] = self::ROLE_USER;
        }

        return array_values(array_unique($roles));
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): void
    {
        $this->roles = array_values(array_unique($roles));
    }

    // ===== getters =====
    public function id(): ?int
    {
        return $this->id;
    }

    public function email(): ?string
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function googleId(): ?string
    {
        return $this->googleId;
    }

    public function verificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function passwordToken(): ?string
    {
        return $this->passwordToken;
    }

    public function passwordTokenExpired(): ?\DateTimeInterface
    {
        return $this->passwordTokenExpired;
    }

    public function firstname(): ?string
    {
        return $this->firstname;
    }

    public function lastname(): ?string
    {
        return $this->lastname;
    }

    public function patronymic(): ?string
    {
        return $this->patronymic;
    }

    public function birthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function gender(): ?int
    {
        return $this->gender;
    }

    public function ocCustomerId(): ?int
    {
        return $this->ocCustomerId;
    }

    public function ordersSyncAt(): ?\DateTimeImmutable
    {
        return $this->ordersSyncAt;
    }

    public function lastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function bonusPoints(): ?int
    {
        return $this->bonusPoints;
    }

    public function emailVerificationCode(): ?string
    {
        return $this->emailVerificationCode;
    }

    public function setEmailVerificationCode(?string $code): void
    {
        $this->emailVerificationCode = $code;
    }

    public function phoneVerificationCode(): ?string
    {
        return $this->phoneVerificationCode;
    }

    public function setPhoneVerificationCode(?string $code): void
    {
        $this->phoneVerificationCode = $code;
    }

    public function lastIp(): ?string
    {
        return $this->lastIp;
    }

    public function setLastIp(?string $ip): void
    {
        $this->lastIp = $ip;
    }

    public function newTokens(): ?TokenPair
    {
        return $this->newTokens;
    }

    public function setNewTokens(?TokenPair $pair): void
    {
        $this->newTokens = $pair;
    }

    /** @return Collection<int, UserCredential> */
    public function credentials(): Collection
    {
        return $this->credentials;
    }

    // ===== setters (со stampOnUpdate) =====
    public function setEmail(?string $email): void
    {
        $this->email = $email;
        $this->stampOnUpdate();
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
        $this->stampOnUpdate();
    }

    public function setGoogleId(?string $googleId): void
    {
        $this->googleId = $googleId;
        $this->stampOnUpdate();
    }

    public function setVerificationCode(?string $code): void
    {
        $this->verificationCode = $code;
        $this->stampOnUpdate();
    }

    public function setVerified(bool $v): void
    {
        $this->verified = $v;
        $this->stampOnUpdate();
    }

    public function setPasswordToken(?string $token, ?\DateTimeInterface $expiresAt): void
    {
        $this->passwordToken = $token;
        $this->passwordTokenExpired = $expiresAt;
        $this->stampOnUpdate();
    }

    public function setFirstname(?string $v): void
    {
        $this->firstname = $v;
        $this->stampOnUpdate();
    }

    public function setLastname(?string $v): void
    {
        $this->lastname = $v;
        $this->stampOnUpdate();
    }

    public function setPatronymic(?string $v): void
    {
        $this->patronymic = $v;
        $this->stampOnUpdate();
    }

    public function setBirthDate(?\DateTimeInterface $v): void
    {
        $this->birthDate = $v;
        $this->stampOnUpdate();
    }

    public function setGender(?int $v): void
    {
        $this->gender = $v;
        $this->stampOnUpdate();
    }

    public function setOcCustomerId(?int $v): void
    {
        $this->ocCustomerId = $v;
        $this->stampOnUpdate();
    }

    public function setOrdersSyncAt(?\DateTimeImmutable $v): void
    {
        $this->ordersSyncAt = $v;
        $this->stampOnUpdate();
    }

    public function setLastLogin(?\DateTimeImmutable $v): void
    {
        $this->lastLogin = $v;
        $this->stampOnUpdate();
    }

    public function setBonusPoints(?int $v): void
    {
        $this->bonusPoints = $v;
        $this->stampOnUpdate();
    }

    public function addCredential(UserCredential $cred): void
    {
        if (!$this->credentials->contains($cred)) {
            $this->credentials->add($cred);
        }
        $this->stampOnUpdate();
    }

    public function removeCredential(UserCredential $cred): void
    {
        $this->credentials->removeElement($cred);
        $this->stampOnUpdate();
    }

    public function avatar(): ?UserAvatar
    {
        return $this->avatar;
    }

    public function setAvatar(?UserAvatar $avatar): void
    {
        $this->avatar = $avatar;
        $this->stampOnUpdate();
    }

    /** Удобный URL (если аватар есть) */
    public function avatarUrl(?string $base = null): ?string
    {
        return $this->avatar?->pictureUrl($base);
    }
}
