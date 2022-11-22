<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Card;
use App\Repository\CardRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'app:list-cards',
    description: 'Lister les cartes dans notre base de données',
)]
class ListCardsCommand extends Command
{
    /**
     * @var CardRepository
     */
    private $cardsRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->cardsRepository = $container->get('doctrine')->getManager()->getRepository(Card::class);
    }
    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cards = $this->cardsRepository->findAll();
        if(!$cards) {
            $output->writeln('<comment>no cards found<comment>');
            exit(1);
        }

        foreach($cards as $card)
        {
            $output->writeln($card);
        }

        return Command::SUCCESS;
    }
}
