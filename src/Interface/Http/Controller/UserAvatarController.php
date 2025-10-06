<?php

declare(strict_types=1);

namespace App\Interface\Http\Controller;

use App\Domain\User\Entity\User;
use App\Domain\User\Entity\UserAvatar;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserAvatarController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepositoryInterface $users
    ) {
    }

    #[Route('/api/users/{id}/avatar', methods: ['POST'])]
    public function upload(int $id, Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->users->byId($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('picture');
        if (!$file) {
            return new JsonResponse(['error' => 'picture is required'], 400);
        }

        $avatar = $user->avatar() ?? new UserAvatar($user);
        $avatar->setPictureFile($file);

        if (!$user->avatar()) {
            $user->setAvatar($avatar);
            $this->em->persist($avatar);
        }

        $this->em->flush();

        return new JsonResponse([
            'url' => $user->avatarUrl($request->getSchemeAndHttpHost()),
        ], 201);
    }
}
