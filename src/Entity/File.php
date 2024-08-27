<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $size = null;

    #[ORM\Column(length: 255)]
    private ?string $format = null;

    #[ORM\ManyToOne(inversedBy: 'number_file')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $latestChanges = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(float $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getLatestChanges(): ?\DateTimeInterface
    {
        return $this->latestChanges;
    }

    public function setLatestChanges(?\DateTimeInterface $latestChanges): static
    {
        $this->latestChanges = $latestChanges;

        return $this;
    }

    public function getCreation(): ?\DateTimeInterface
    {
        return $this->creation;
    }

    public function setCreation(\DateTimeInterface $creation): static
    {
        $this->creation = $creation;

        return $this;
    }
}
