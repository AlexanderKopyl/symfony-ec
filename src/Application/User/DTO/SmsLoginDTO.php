<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SmsLoginDTO
{
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\+?\d{10,15}$/')]
    public string $phone;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }
}
