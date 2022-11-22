<?php

namespace App\Form;

use App\Entity\Deck;
use App\Repository\CardRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dump($options);
        $deck = $options['data'] ?? null;
        $merchant = $deck->getOwner();

        $builder
            ->add('nom', null, array("property_path" => "name"))
            ->add('description')
            ->add('publique', CheckboxType::class, array("property_path" => "published"))
            ->add('proprietaire', null, [
                'disabled'   => true,
                "property_path" => "owner"])
            ->add('cartes', EntityType::class, [
                'class' => 'App\Entity\Card',
                'property_path' => "cards",
                'expanded' => true,
                'mapped' => false,
                'query_builder' => function (CardRepository $er) use ($merchant) {
                    return $er->createQueryBuilder('g')
                        ->leftJoin('g.myCollection', 'i')
                        ->andWhere('i.owner = :merchant')
                        ->setParameter('merchant', $merchant)
                        ;
                }])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deck::class,
        ]);
    }
}
