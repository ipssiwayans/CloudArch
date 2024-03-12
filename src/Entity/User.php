<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: "L'adresse email est déjà utilisée")]
class User implements UserInterface , PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private ?int $street_number = null;

    #[ORM\Column(length: 255)]
    private ?string $street_address = null;

    #[ORM\Column(length: 255)]
    private ?string $postal_code = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column]
    private ?int $total_storage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $registration_date = null;

    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $number_invoices;

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $number_file;

    public function __construct()
    {
        $this->number_invoices = new ArrayCollection();
        $this->number_file = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getStreetNumber(): ?int
    {
        return $this->street_number;
    }

    public function setStreetNumber(int $street_number): static
    {
        $this->street_number = $street_number;

        return $this;
    }

    public function getStreetAddress(): ?string
    {
        return $this->street_address;
    }

    public function setStreetAddress(string $street_address): static
    {
        $this->street_address = $street_address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getTotalStorage(): ?int
    {
        return $this->total_storage;
    }

    public function setTotalStorage(int $total_storage): static
    {
        $this->total_storage = $total_storage;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $registration_date): static
    {
        $this->registration_date = $registration_date;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getNumberInvoices(): Collection
    {
        return $this->number_invoices;
    }

    public function addNumberInvoice(Invoice $numberInvoice): static
    {
        if (!$this->number_invoices->contains($numberInvoice)) {
            $this->number_invoices->add($numberInvoice);
            $numberInvoice->setUserId($this);
        }

        return $this;
    }

    public function removeNumberInvoice(Invoice $numberInvoice): static
    {
        if ($this->number_invoices->removeElement($numberInvoice)) {
            // set the owning side to null (unless already changed)
            if ($numberInvoice->getUserId() === $this) {
                $numberInvoice->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getNumberFile(): Collection
    {
        return $this->number_file;
    }

    public function addNumberFile(File $numberFile): static
    {
        if (!$this->number_file->contains($numberFile)) {
            $this->number_file->add($numberFile);
            $numberFile->setUserId($this);
        }

        return $this;
    }

    public function removeNumberFile(File $numberFile): static
    {
        if ($this->number_file->removeElement($numberFile)) {
            // set the owning side to null (unless already changed)
            if ($numberFile->getUserId() === $this) {
                $numberFile->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
