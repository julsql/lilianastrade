<?php

namespace App\Entity;

use App\Repository\EditionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EditionRepository::class)]
class Edition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Card::class, mappedBy: 'edition')]
    private Collection $cards;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function __toString() {
        $mois =array("01"=>"janvier", "02"=> "février", "03"=> "mars",
                     "04"=> "avril", "05"=> "mai", "06"=> "juin",
                     "07"=> "juillet", "08"=> "août", "09"=> "septembre",
                     "10"=> "octobre", "11"=> "novembre", "12"=> "décembre");

        $monmois = date_format($this->date,"m");
        $date = $mois[$monmois] . ' ' . date_format($this->date,"Y");

        return $this->name . " | sortie en " . $date;

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
            $card->addEdition($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            $card->removeEdition($this);
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
