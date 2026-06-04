<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\AppointmentRepository;
use App\State\AppointmentProvider;
use App\State\AppointmentProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ORM\Table(name: '`appointment`')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER') and object.getUser() == user or is_granted('ROLE_ADMIN')",
            provider: AppointmentProvider::class
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            provider: AppointmentProvider::class
        ),
        new Post(security: "is_granted('ROLE_USER')", processor: AppointmentProcessor::class),
        new Put(
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)",
            processor: AppointmentProcessor::class
        ),
        new Patch(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"),
        new Delete(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)")
    ]
)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $customerPhone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $appointmentDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $serviceAddress = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $vehicleMake = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $vehicleModel = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $vehicleYear = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $serviceType = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = 'pending';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(string $customerPhone): static
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    public function getAppointmentDate(): ?\DateTimeInterface
    {
        return $this->appointmentDate;
    }

    public function setAppointmentDate(\DateTimeInterface $appointmentDate): static
    {
        $this->appointmentDate = $appointmentDate;
        return $this;
    }

    public function getServiceAddress(): ?string
    {
        return $this->serviceAddress;
    }

    public function setServiceAddress(string $serviceAddress): static
    {
        $this->serviceAddress = $serviceAddress;
        return $this;
    }

    public function getVehicleMake(): ?string
    {
        return $this->vehicleMake;
    }

    public function setVehicleMake(string $vehicleMake): static
    {
        $this->vehicleMake = $vehicleMake;
        return $this;
    }

    public function getVehicleModel(): ?string
    {
        return $this->vehicleModel;
    }

    public function setVehicleModel(string $vehicleModel): static
    {
        $this->vehicleModel = $vehicleModel;
        return $this;
    }

    public function getVehicleYear(): ?string
    {
        return $this->vehicleYear;
    }

    public function setVehicleYear(?string $vehicleYear): static
    {
        $this->vehicleYear = $vehicleYear;
        return $this;
    }

    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    public function setServiceType(string $serviceType): static
    {
        $this->serviceType = $serviceType;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
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