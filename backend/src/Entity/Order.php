<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\OrderRepository;
use App\State\OrderProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            provider: \App\State\OrderProvider::class,
            normalizationContext: ['groups' => ['order:read']]
        ),
        new Get(
            security: "is_granted('ROLE_USER') and object.getUser() == user or is_granted('ROLE_ADMIN')",
            provider: \App\State\OrderProvider::class,
            normalizationContext: ['groups' => ['order:read', 'order:read:detail']]
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            processor: OrderProcessor::class,
            denormalizationContext: ['groups' => ['order:create']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['order:update']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']]
)]
class Order
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order:read'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['order:read'])]
    private ?string $total = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_SHIPPED, self::STATUS_COMPLETED, self::STATUS_CANCELLED])]
    #[Groups(['order:read', 'order:update'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    #[Groups(['order:read', 'order:read:detail', 'order:create'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = self::STATUS_PENDING;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // establecer el lado propietario a nulo (a menos que ya haya cambiado)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * Calcular el total basado en los items
     */
    public function calculateTotal(): void
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getQuantity() * (float) $item->getPrice();
        }
        $this->total = (string) $total;
    }
}
