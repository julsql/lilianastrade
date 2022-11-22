<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subType')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ["persist"])]
    private Collection $subType;

    #[ORM\ManyToMany(targetEntity: Card::class, mappedBy: 'type')]
    private Collection $card;

    public function __construct()
    {
        $this->subType = new ArrayCollection();
        $this->card = new ArrayCollection();
    }

    public function __toString() {
        if ($this->parent and $this->parent->name != "Normal") {
            return $this->parent . ' ' . $this->name;
        }
        return $this->name;
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubType(): Collection
    {
        return $this->subType;
    }

    public function addSubType(self $subType): self
    {
        if (!$this->subType->contains($subType)) {
            $this->subType->add($subType);
            $subType->setParent($this);
        }

        return $this;
    }

    public function removeSubType(self $subType): self
    {
        if ($this->subType->removeElement($subType)) {
            // set the owning side to null (unless already changed)
            if ($subType->getParent() === $this) {
                $subType->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCard(): Collection
    {
        return $this->card;
    }

    public function addCard(Card $card): self
    {
        if (!$this->card->contains($card)) {
            $this->card->add($card);
            $card->addType($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->card->removeElement($card)) {
            $card->removeType($this);
        }

        return $this;
    }
}
