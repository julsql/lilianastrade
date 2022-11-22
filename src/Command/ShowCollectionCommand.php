<?php

namespace App\Command;

use App\Entity\MyCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'app:show-collection',
    description: 'Affiche les cartes d\'un collection',
)]

class ShowCollectionCommand extends Command
{

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->collRepository = $container->get('doctrine')->getManager()->getRepository(MyCollection::class);
    }

    protected function configure()
    {
        $this
            //->setDescription('Show recommendations for a film')
            ->addArgument('collection', InputArgument::REQUIRED, 'Nom de la collection')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $input->getArgument('collection');

        $coll = $this->collRepository->findOneBy(
            ['name' => $collection]);
        if(!$coll) {
            $output->writeln('unknown collection: ' . $collection);
            exit(1);
        }
        $output->writeln($coll . ':');

        foreach($coll->getCard() as $card) {
            $output->writeln('- '. $card);
        }

        return Command::SUCCESS;
    }
}
