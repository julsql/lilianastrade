<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Entity\MyCollection;
use App\Entity\Merchant;
use App\Entity\Deck;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(CardCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lilianastrade');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addHtmlContentToHead('<script defer src="https://stats.julsql.fr/js/app.js" data-website-id="ee3bf4a2-f93e-4613-b0ac-502ea671034f" data-domains="lilianastrade.julsql.fr"></script>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Back to the website', 'fas fa-home', 'app_home');
        yield MenuItem::linkToCrud('Cards', 'fas fa-map-marker-alt', Card::class);
        yield MenuItem::linkToCrud('Collection', 'fas fa-comments', MyCollection::class);
        yield MenuItem::linkToCrud('Merchant', 'fas fa-comments', Merchant::class);
        yield MenuItem::linkToCrud('Decks', 'fas fa-comments', Deck::class);
    }
}
