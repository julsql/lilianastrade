<?php

namespace App\Command;

use App\Entity\Merchant;
use App\Entity\MyCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'app:add-collection',
    description: 'Add a collection',
)]
class AddCollectionCommand extends Command
{

    private $em;
    private $merchantRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->em = $container->get('doctrine')->getManager();
        $this->merchantRepository = $container->get('doctrine')->getManager()->getRepository(Merchant::class);

    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'nom de la collection');
        $this->addArgument('owner', InputArgument::OPTIONAL, 'nom du propriétaire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $owner = $input->getArgument('owner');
        $collname = $input->getArgument('name');

        // Chargement en mémoire d'un film existant dans la base
        $merc = $this->merchantRepository->findOneBy(
            ['pseudo' => $owner]);

        // Création d'une instance en mémoire
        $coll = new MyCollection();
        $coll->setName($collname);

        // Ajout en mémoire dans la collection des recommendations de ce film
        if ($owner) {
            // Collection -> Test si elle existe

            if (!$merc) {
                // Collection n'existe pas : création
                //$output->writeln('unknown collection: ' . $mycoll);
                //exit(1);
                $merc = new Merchant();
                $merc->setPseudo($owner);
            }
            $merc->addMyCollection($coll);
            // marque l'instance comme "à sauvegarder" en base
            $this->em->persist($merc);
        }
        $this->em->persist($coll);

        // génère les requêtes en base
        $this->em->flush();

        return Command::SUCCESS;
    }
}
