<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read'])]
    private ?string $name = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['product:read'])]
    private ?string $description = null;

    #[ORM\Column(type: "float")]
    #[Groups(['product:read'])]
    private ?float $price = null;

    #[ORM\Column(type: "integer")]
    private ?int $stock = null;

    #[ORM\Column(type: "datetime")]
    #[Groups(['product:read'])]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * Relation ManyToOne vers une catégorie (chaque produit appartient à une seule catégorie).
     */
    #[ORM\ManyToOne(targetEntity: "App\Entity\Category")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * Relation ManyToMany inversée : un produit peut appartenir à plusieurs commandes.
     */
    #[ORM\ManyToMany(mappedBy: "products", targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        $this->orders->removeElement($order);
        return $this;
    }

    
     // Décrémente le stock du produit d'une quantité donnée.
     
    public function decreaseStock(int $quantity): void
    {
        $this->stock -= $quantity;
    }
}
