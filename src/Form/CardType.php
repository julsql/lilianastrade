<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CardType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // collections et decks proposés : ceux du propriétaire de la carte (ou du marchand connecté)
        $merchantId = $this->scopeMerchantId($options['data'] ?? null);
        $ownedBy = fn (EntityRepository $er): QueryBuilder => null === $merchantId
            ? $er->createQueryBuilder('e')
            : $er->createQueryBuilder('e')->andWhere('IDENTITY(e.owner) = :merchant')->setParameter('merchant', $merchantId);

        $builder
            ->add('nom', null, array("property_path" => "name"))
            ->add('myCollection', null, ['query_builder' => $ownedBy])
            ->add('type', null, array('expanded' => true))
            ->add('color')
            ->add('mana')
            ->add('edition')
            ->add('rarity')
            ->add('decks', null, ['query_builder' => $ownedBy])
            ->add('imageName', TextType::class,  ['disabled' => true])
            ->add('imageFile', VichImageType::class, ['required' => false])
        ;
    }

    /**
     * Sans restriction uniquement pour un admin créant une carte.
     */
    private function scopeMerchantId(?Card $card): ?int
    {
        if ($owner = $card?->getMyCollection()?->getOwner()) {
            return $owner->getId();
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return null;
        }
        $user = $this->security->getUser();

        // marchand inconnu : aucun choix plutôt que tous
        return $user instanceof User ? ($user->getMerchant()?->getId() ?? 0) : 0;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
