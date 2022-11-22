<?php

namespace App\Entity;

use App\Repository\ManaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManaRepository::class)]
class Mana
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $value = null;

    #[ORM\ManyToMany(targetEntity: Card::class, mappedBy: 'mana')]
    private Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function __toString() {
        return strval($this->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->addMana($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            $card->removeMana($this);
        }

        return $this;
    }
}
