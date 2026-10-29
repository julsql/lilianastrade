<?php

namespace App\Controller;

use App\Repository\DeckRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    public function __construct(private string $siteUrl)
    {
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'])]
    public function index(DeckRepository $deckRepository): Response
    {
        $response = $this->render('sitemap/index.xml.twig', [
            'base_url' => rtrim($this->siteUrl, '/'),
            'decks' => $deckRepository->findBy(['published' => true], ['id' => 'ASC']),
        ]);
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return $response;
    }
}
