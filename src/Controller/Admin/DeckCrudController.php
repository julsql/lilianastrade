<?php

namespace App\Controller\Admin;

use App\Entity\Deck;
use App\Entity\MyCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DeckCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Deck::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('owner'),
            BooleanField::new('published')
                ->onlyOnForms()
                ->hideWhenCreating(),
            TextField::new('description'),

            AssociationField::new('cards')
                ->onlyOnDetail()
                ->setTemplatePath('admin/fields/collection_card.html.twig'),

            AssociationField::new('cards')
                ->onlyOnForms()
                // on ne souhaite pas gérer l'association entre les
                // [objets] et la [galerie] dès la crétion de la
                // [galerie]
                ->hideWhenCreating()
                ->setTemplatePath('admin/fields/collection_card.html.twig')
                // Ajout possible seulement pour des [objets] qui
                // appartiennent même propriétaire de l'[inventaire]
                // que le [createur] de la [galerie]
                ->setQueryBuilder(
                    function (QueryBuilder $queryBuilder) {
                        // récupération de l'instance courante de [galerie]
                        $currentDeck = $this->getContext()->getEntity()->getInstance();
                        $owner = $currentDeck->getOwner();
                        $merchantId = $owner->getId();
                        // charge les seuls [objets] dont le 'owner' de l'[inventaire] est le [createur] de la galerie
                        $queryBuilder->leftJoin('entity.collection', 'i')
                            ->leftJoin('i.owner', 'm')
                            ->andWhere('m.id = :merchant_id')
                            ->setParameter('merchant_id', $merchantId);
                        return $queryBuilder;
                    }
                ),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ;
    }
}
