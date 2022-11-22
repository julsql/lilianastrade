<?php


namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'card')]
    public MyCollection $myCollection;

    #[ORM\ManyToMany(targetEntity: Type::class, inversedBy: 'card')]
    private Collection $type;

    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'cards')]
    private Collection $color;

    #[ORM\ManyToMany(targetEntity: Mana::class, inversedBy: 'cards')]
    private Collection $mana;

    #[ORM\ManyToMany(targetEntity: Edition::class, inversedBy: 'cards')]
    private Collection $edition;

    #[ORM\ManyToMany(targetEntity: Rarity::class, inversedBy: 'cards')]
    private Collection $rarity;

    #[ORM\ManyToMany(targetEntity: Deck::class, mappedBy: 'cards')]
    private Collection $decks;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    /**
     * @Vich\UploadableField(mapping="cards", fileNameProperty="imageName")
     * @var File
     */
    private $imageFile;


    public function __construct()
    {
        $this->type = new ArrayCollection();
        $this->color = new ArrayCollection();
        $this->mana = new ArrayCollection();
        $this->edition = new ArrayCollection();
        $this->rarity = new ArrayCollection();
        $this->decks = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMyCollection(): ?MyCollection
    {
        return $this->myCollection;
    }

    public function setMyCollection(?MyCollection $myCollection): self
    {
        $this->myCollection = $myCollection;

        return $this;
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
     * @return Collection<int, Type>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Type $type): self
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        $this->type->removeElement($type);

        return $this;
    }

    /**
     * @return Collection<int, Color>
     */
    public function getColor(): Collection
    {
        return $this->color;
    }

    public function addColor(Color $color): self
    {
        if (!$this->color->contains($color)) {
            $this->color->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): self
    {
        $this->color->removeElement($color);

        return $this;
    }

    /**
     * @return Collection<int, Mana>
     */
    public function getMana(): Collection
    {
        return $this->mana;
    }

    public function addMana(Mana $mana): self
    {
        if (!$this->mana->contains($mana)) {
            $this->mana->add($mana);
        }

        return $this;
    }

    public function removeMana(Mana $mana): self
    {
        $this->mana->removeElement($mana);

        return $this;
    }

    /**
     * @return Collection<int, Edition>
     */
    public function getEdition(): Collection
    {
        return $this->edition;
    }

    public function addEdition(Edition $edition): self
    {
        if (!$this->edition->contains($edition)) {
            $this->edition->add($edition);
        }

        return $this;
    }

    public function removeEdition(Edition $edition): self
    {
        $this->edition->removeElement($edition);

        return $this;
    }

    /**
     * @return Collection<int, Rarity>
     */
    public function getRarity(): Collection
    {
        return $this->rarity;
    }

    public function addRarity(Rarity $rarity): self
    {
        if (!$this->rarity->contains($rarity)) {
            $this->rarity->add($rarity);
        }

        return $this;
    }

    public function removeRarity(Rarity $rarity): self
    {
        $this->rarity->removeElement($rarity);

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
            $deck->addCard($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): self
    {
        if ($this->decks->removeElement($deck)) {
            $deck->removeCard($this);
        }

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

}
