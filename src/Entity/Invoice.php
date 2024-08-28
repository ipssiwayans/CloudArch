<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $object = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $amount_ht = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?float $unit_price_ht = null;

    #[ORM\Column]
    private ?float $amount_tva = null;

    #[ORM\Column]
    private ?float $total_amount = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAmountHt(): ?float
    {
        return $this->amount_ht;
    }

    public function setAmountHt(float $amount_ht): static
    {
        $this->amount_ht = $amount_ht;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPriceHt(): ?float
    {
        return $this->unit_price_ht;
    }

    public function setUnitPriceHt(?float $unit_price_ht): static
    {
        $this->unit_price_ht = $unit_price_ht;

        return $this;
    }

    public function getAmountTva(): ?float
    {
        return $this->amount_tva;
    }

    public function setAmountTva(float $amount_tva): static
    {
        $this->amount_tva = $amount_tva;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->total_amount;
    }

    public function setTotalAmount(float $total_amount): static
    {
        $this->total_amount = $total_amount;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
