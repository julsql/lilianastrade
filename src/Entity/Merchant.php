<?php

namespace App\Entity;

use App\Repository\MerchantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MerchantRepository::class)]
class Merchant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: MyCollection::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $myCollection;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Deck::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $decks;

    #[ORM\OneToOne(mappedBy: 'merchant', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->myCards = new ArrayCollection();
        $this->myCollection = new ArrayCollection();
        $this->decks = new ArrayCollection();
    }

    public function __toString() {
        return $this->pseudo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, MyCollection>
     */
    public function getMyCollection(): Collection
    {
        return $this->myCollection;
    }

    public function addMyCollection(MyCollection $myCollection): self
    {
        if (!$this->myCollection->contains($myCollection)) {
            $this->myCollection->add($myCollection);
            $myCollection->setOwner($this);
        }

        return $this;
    }

    public function removeMyCollection(MyCollection $myCollection): self
    {
        if ($this->myCollection->removeElement($myCollection)) {
            // set the owning side to null (unless already changed)
            if ($myCollection->getOwner() === $this) {
                $myCollection->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Deck>
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(Deck $deck): self
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
            $deck->setOwner($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): self
    {
        if ($this->decks->removeElement($deck)) {
            // set the owning side to null (unless already changed)
            if ($deck->getOwner() === $this) {
                $deck->setOwner(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getMerchant() !== $this) {
            $user->setMerchant($this);
        }

        $this->user = $user;

        return $this;
    }

}
