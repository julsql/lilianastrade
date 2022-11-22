<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Merchant;
use App\Form\DeckType;
use App\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/deck')]
class DeckController extends AbstractController
{
    #[Route('/', name: 'app_deck_index', methods: ['GET'])]
    public function index(DeckRepository $deckRepository): Response
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            $deck = $deckRepository->findAll();
        }
        else {
            $deck = $deckRepository->findBy([
            'published' => true,
            ]);
            $user = $this->getUser();
            if ($user and $user->getMerchant()) {
                $merchant = $user->getMerchant();
                $deck2 = $deckRepository->findBy([
                    'published' => false,
                    'owner' => $merchant
                ]);
                $deck = array_merge($deck, $deck2);
            }
        }
        return $this->render('deck/index.html.twig', [
            'decks' => $deck
        ]);
    }

    #[Route('/new/{id}', name: 'app_deck_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DeckRepository $deckRepository, Merchant $merchant, EntityManagerInterface $entityManager): Response
    {
        $deck = new Deck();
        $deck->setOwner($merchant);
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deck);
            $entityManager->flush();

            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'bian ajouté');
            // $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()
            // or to $this->get('session')->getFlashBag()->add();

            return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deck/new.html.twig', [
            'deck' => $deck,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_deck_show", methods={"GET"})
     */
    public function show(Deck $deck): Response
    {
        $hasAccess = false;
        $canModify = false;
        if($this->isGranted('ROLE_ADMIN')) {
            $canModify = true;
            $hasAccess = true;
        }
        else if($deck->isPublished()) {
            $hasAccess = true;
        }
        else {
            $user = $this->getUser();
            if( $user ) {
                $merchant = $user->getMerchant();
                if ( $merchant &&  ($merchant == $deck->getOwner()) ) {
                    $hasAccess = true;
                    $canModify = true;
                }
            }
        }
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à un deck non publique !");
        }
        return $this->render('deck/show.html.twig', [
            'deck' => $deck,
            'connect' => $canModify,
        ]);
    }



    /**
     * @Route("/{deck_id}/card/{card_id}", name="app_deck_card_show", methods={"GET"})
     * @ParamConverter("deck", options={"id" = "deck_id"})
     * @ParamConverter("card", options={"id" = "card_id"})
     */

    public function CardShow(Deck $deck, Card $card): Response
    {
        if(! $deck->getCards()->contains($card)) {
            throw $this->createNotFoundException("Cette carte n'est pas dans ce deck");
        }

        $hasAccess = false;
        $canModify = false;
        if($deck->isPublished()) {
            $hasAccess = true;
        }
        else if($this->isGranted('ROLE_ADMIN')) {
            $canModify = true;
            $hasAccess = true;
        }

            $user = $this->getUser();
            if( $user ) {
                $member = $user->getMerchant();
                if ( $member &&  ($member == $deck->getOwner()) ) {
                    $hasAccess = true;
                    $canModify = true;
                }
            }
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette carte");
        }

        return $this->render('deck/card_show.html.twig', [
            'card' => $card,
            'deck' => $deck,
            'connect' => $canModify
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deck_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deck $deck, DeckRepository $deckRepository): Response
    {
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);
        $canModify = false;
        if($this->isGranted('ROLE_ADMIN')) {
        $canModify = true;
        }
        $user = $this->getUser();
        if( $user ) {
            $member = $user->getMerchant();
            if ( $member &&  ($member == $deck->getOwner()) ) {
                $canModify = true;
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $deckRepository->save($deck, true);

            return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deck/edit.html.twig', [
            'deck' => $deck,
            'form' => $form,
            'connect' => $canModify,
        ]);
    }

    #[Route('/{id}', name: 'app_deck_delete', methods: ['POST'])]
    public function delete(Request $request, Deck $deck, DeckRepository $deckRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deck->getId(), $request->request->get('_token'))) {
            $deckRepository->remove($deck, true);
        }

        return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
    }
}
