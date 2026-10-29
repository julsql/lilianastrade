<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_card_index');
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
        yield MenuItem::linkToRoute('Back to the website', 'fas fa-home', 'app_home');
        yield MenuItem::linkTo(CardCrudController::class, 'Cards', 'fas fa-map-marker-alt');
        yield MenuItem::linkTo(MyCollectionCrudController::class, 'Collection', 'fas fa-comments');
        yield MenuItem::linkTo(MerchantCrudController::class, 'Merchant', 'fas fa-comments');
        yield MenuItem::linkTo(DeckCrudController::class, 'Decks', 'fas fa-comments');
    }
}
