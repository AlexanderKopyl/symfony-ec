<?php

declare(strict_types=1);

namespace App\Interface\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthController
{
    #[Route('/api/ping', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['pong' => true, 'ts' => time()]);
    }
}
