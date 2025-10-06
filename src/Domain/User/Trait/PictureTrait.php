<?php

declare(strict_types=1);

namespace App\Domain\User\Trait;

use App\Domain\User\Entity\UserAvatar;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait PictureTrait
{
    #[Vich\UploadableField(
        mapping: 'user',
        fileNameProperty: 'pictureName',
        size: 'pictureSize',
        mimeType: 'pictureMimeType',
        originalName: 'pictureOriginalName'
    )]
    #[Assert\File(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])]
    private ?File $pictureFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Serializer\Groups(['user_view'])]
    private ?string $pictureName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $pictureSize = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $pictureMimeType = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pictureOriginalName = null;

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): self
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function erasePicture(): void
    {
        $this->pictureFile         = null;
        $this->pictureName         = null;
        $this->pictureSize         = null;
        $this->pictureMimeType     = null;
        $this->pictureOriginalName = null;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    /**
     * @param File|UploadedFile|null $pictureFile
     *
     * @return UserAvatar|PictureTrait
     */
    public function setPictureFile(File|UploadedFile|null $pictureFile): self
    {
        $this->pictureFile = $pictureFile;
        if ($pictureFile !== null) {
            $this->updatedAt = new \DateTimeImmutable('now');
        }

        return $this;
    }

    public function pictureName(): ?string
    {
        return $this->pictureName;
    }

    public function pictureSize(): ?int
    {
        return $this->pictureSize;
    }

    public function pictureMimeType(): ?string
    {
        return $this->pictureMimeType;
    }

    public function pictureOriginalName(): ?string
    {
        return $this->pictureOriginalName;
    }

    /** Удобный accessor для URL */
    public function pictureUrl(?string $base = null): ?string
    {
        if (!$this->pictureName) {
            return null;
        }
        $path = '/uploads/user/'.$this->pictureName; // совпадает с mapping: user

        return $base ? rtrim($base, '/').$path : $path;
    }
}
