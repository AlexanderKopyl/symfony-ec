<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SmsConfirmDTO
{
    #[Assert\NotBlank] public string $phone;
    #[Assert\NotBlank] #[Assert\Length(min: 4, max: 8)] public string $code;
    public function __construct(string $phone, string $code)
    {
        $this->phone = $phone;
        $this->code  = $code;
    }
}
