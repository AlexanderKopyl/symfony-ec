<?php

declare(strict_types=1);

namespace App\Domain\Shared\Trait;

use Doctrine\ORM\Mapping as ORM;

trait Timestamp
{
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** вызывай из конструктора сущности */
    public function stampOnCreate(): void
    {
        $now             = new \DateTimeImmutable('now');
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /** используем при апдейте */
    public function stampOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }
}
