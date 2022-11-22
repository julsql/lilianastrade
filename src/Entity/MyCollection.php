<?php

namespace App\Entity;

use App\Repository\MyCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MyCollectionRepository::class)]
class MyCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'myCollection', targetEntity: Card::class, cascade: ["persist"])]
    public Collection $card;

    #[ORM\ManyToOne(inversedBy: 'myCollection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Merchant $owner = null;

    public function __construct()
    {
        $this->card = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
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

    public function getId(): ?int
    {
        return $this->id;
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
            $card->setMyCollection($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->card->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getMyCollection() === $this) {
                $card->setMyCollection(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Merchant
    {
        return $this->owner;
    }

    public function setOwner(?Merchant $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
