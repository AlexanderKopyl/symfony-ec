<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

use Symfony\Component\Serializer\Annotation as Serializer;

class TokenPair
{
    /**
     * @var string
     */
    #[Serializer\SerializedName('token')]
    #[Serializer\Groups(['jwt_token_view'])]
    private string $jwtToken;

    /**
     * @var string
     */
    #[Serializer\SerializedName('refresh_token')]
    #[Serializer\Groups(['jwt_token_view'])]
    private string $refreshToken;

    public function __construct(string $jwtToken, string $refreshToken)
    {
        $this->jwtToken     = $jwtToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getJwtToken(): string
    {
        return $this->jwtToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
