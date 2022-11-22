<?php

namespace App\Command;

use App\Entity\Card;
use App\Entity\MyCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'app:add-card',
    description: 'Add a card with or without a collection',
)]
class AddCardCommand extends Command
{
    /**
     * @var EntityManager : gère les fonctions liées à la persistence
     */
    private $em;
    private $myCollectionRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->em = $container->get('doctrine')->getManager();
        $this->myCollectionRepository = $container->get('doctrine')->getManager()->getRepository(MyCollection::class);
    }

    protected function configure()
    {
        $this
            ->addArgument('card', InputArgument::REQUIRED, 'Nom de la carte')
            ->addArgument('collection', InputArgument::OPTIONAL, 'Collection (optionnelle) de la carte')
            ->addArgument('owner', InputArgument::OPTIONAL, 'Propriétaire de la carte')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mycoll = $input->getArgument('collection');
        $cardname = $input->getArgument('card');
        $owner = $input->getArgument('owner');

        // Chargement en mémoire d'un film existant dans la base
        $coll = $this->myCollectionRepository->findOneBy(
            ['name' => $mycoll]);

        // Création d'une instance en mémoire
        $card = new Card();
        $card->setName($cardname);

        // Ajout en mémoire dans la collection des recommendations de ce film
        if ($mycoll) {
            // Collection -> Test si elle existe

            if (!$coll) {
                // Collection n'existe pas
                if (!$owner) {
                    // on n'a pas le propriétaire : erreur
                    $output->writeln('unknown collection: ' . $mycoll);
                    exit(1);
                } else {
                    // On a le propriétaire : création
                    $coll = new MyCollection();
                    $coll->setName($mycoll);
                }
            }
            $coll->addCard($card);
            // marque l'instance comme "à sauvegarder" en base
            $this->em->persist($coll);
        }
        $this->em->persist($card);

        // génère les requêtes en base
        $this->em->flush();

        return Command::SUCCESS;
    }
}
