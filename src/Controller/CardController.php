<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/card')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CardController extends AbstractController
{
    #[Route('/', name: 'app_card_index', methods: ['GET'])]
    public function index(CardRepository $cardRepository): Response
    {
        $card = new Card();
        if ($this->isGranted('ROLE_ADMIN')) {
            $card = $cardRepository->findAll();
        }
        else {
            if ($this->getUser()) {
            $merchant = $this->getUser()->getMerchant();
            $card = $cardRepository->findMerchantCard($merchant);}
        }
        return $this->render('card/index.html.twig', [
            'cards' => $card,
        ]);
    }

    #[Route('/new', name: 'app_card_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CardRepository $cardRepository, EntityManagerInterface $entityManager): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('MANAGE', $card, "Vous ne pouvez ajouter une carte qu'à vos propres collections !");

            $entityManager->persist($card);
            $entityManager->flush();

            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'carte ajoutée');
            // $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()
            // or to $this->get('session')->getFlashBag()->add();

            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        $hasAccess = $this->isGranted('MANAGE', $card);
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder aux cartes d'un autre marchand !");
        }

        return $this->render('card/show.html.twig', [
            'card' => $card,
            'connect' => $hasAccess,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_edit', methods: ['GET', 'POST'])]
    #[IsGranted('MANAGE', 'card')]
    public function edit(Request $request, Card $card, CardRepository $cardRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // la collection est modifiable dans le formulaire : on revérifie le propriétaire
            $this->denyAccessUnlessGranted('MANAGE', $card, "Vous ne pouvez pas déplacer une carte dans la collection d'un autre marchand !");
            $entityManager->persist($card);
            $entityManager->flush();

            $this->addFlash('message', 'carte ajoutée');

            //$cardRepository->save($card, true);

            return $this->redirectToRoute('app_card_show', array(
                'id' => $card->id), Response::HTTP_SEE_OTHER);
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form,
            'connect' => true
        ]);
    }

    #[Route('/{id}', name: 'app_card_delete', methods: ['POST'])]
    #[IsGranted('MANAGE', 'card')]
    public function delete(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $cardRepository->remove($card, true);
        }

        return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
    }
}
