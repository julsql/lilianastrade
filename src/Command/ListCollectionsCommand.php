<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\MyCollection;
use App\Repository\MyCollectionRepository;

#[AsCommand(
    name: 'app:list-collections',
    description: 'Lister les collections',
)]
class ListCollectionsCommand extends Command
{
    /**
     * @var MyCollectionRepository
     */
    private $myCollectionRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->myCollectionRepository = $container->get('doctrine')->getManager()->getRepository(MyCollection::class);
    }
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $colls = $this->myCollectionRepository->findAll();
        if(!$colls) {
            $output->writeln('<comment>no collections found<comment>');
            exit(1);
        }

        foreach($colls as $coll)
        {
            $output->writeln($coll);
        }

        return Command::SUCCESS;
    }
}

