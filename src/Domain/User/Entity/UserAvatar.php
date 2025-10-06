<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Shared\Contract\TimestampAwareInterface;
use App\Domain\Shared\Trait\Timestamp;
use App\Domain\User\Trait\PictureTrait;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[ORM\Table(name: 'user_avatars')]
#[Vich\Uploadable]
class UserAvatar implements TimestampAwareInterface
{
    use PictureTrait;
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'avatar')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->stampOnCreate();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }
}
