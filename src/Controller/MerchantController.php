<?php

namespace App\Controller;

use App\Entity\Merchant;
use App\Repository\DeckRepository;
use App\Repository\MerchantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/merchant")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class MerchantController extends AbstractController
{

    #[Route('/list', name: 'app_merchant_index')]
    public function index(MerchantRepository $merchantRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à la liste des marchands !");
        }
        return $this->render('merchant/index.html.twig', [
            'merchants' => $merchantRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_merchant_show', methods: ['GET'])]
    public function show(DeckRepository $deckRepository): Response
    {
        $privateDeck = array();
        $user = $this->getUser();
        $admin = $this->isGranted('ROLE_ADMIN');
        if($user)  {
            $merchant = $user->getMerchant();
            $privateDeck = $deckRepository->findBy(
                [
                    'published' => false,
                    'owner' => $merchant,

                ]);
        }
        $deck = array_merge($privateDeck, $deckRepository->findBy(['published' => true, 'owner' =>$merchant]));

        $hasAccess = $admin || ($this->getUser()->getMerchant() == $merchant);
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à la page d'un autre marchand !");
        }

        return $this->render('merchant/show.html.twig', [
            'merchant' => $merchant,
            'decks' => $deck,
            'admin' => $admin
        ]);
    }

    #[Route('/{id}', name: 'app_merchant_show_id', methods: ['GET'])]
    public function showId(Merchant $merchant, DeckRepository $deckRepository): Response
    {
        $admin = $this->isGranted('ROLE_ADMIN');
        $deck = $deckRepository->findBy([
            'owner' => $merchant
        ]);

        $hasAccess = $admin || ($this->getUser()->getMerchant() == $merchant);
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à la page d'un autre marchand !");
        }

        return $this->render('merchant/show.html.twig', [
            'merchant' => $merchant,
            'decks' => $deck,
            'admin' => $admin
        ]);
    }
}
