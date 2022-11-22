<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Card::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('Mycollection'),
            TextField::new('name'),
            AssociationField::new('type')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getType()->toArray()); // ici getBodyShapes()
                }),
            AssociationField::new('color')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getColor()->toArray()); // ici getBodyShapes()
                }),
            AssociationField::new('mana')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getMana()->toArray()); // ici getBodyShapes()
                }),
            AssociationField::new('edition')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getEdition()->toArray()); // ici getBodyShapes()
                }),
            AssociationField::new('rarity')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getRarity()->toArray()); // ici getBodyShapes()
                })
        ];
    }

    public function configureActions(Actions $actions): Actions
    {

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ;
    }
}
