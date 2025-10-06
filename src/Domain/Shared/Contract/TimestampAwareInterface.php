<?php

namespace App\Domain\Shared\Contract;

interface TimestampAwareInterface
{
    public function stampOnCreate(): void;

    public function stampOnUpdate(): void;
}
