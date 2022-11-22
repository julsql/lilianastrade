<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/card")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
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

            $entityManager->persist($card);
            $entityManager->flush();

            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'carte ajoutée');
            // $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()
            // or to $this->get('session')->getFlashBag()->add();

            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/new.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') ||
            ($this->getUser()->getMerchant() == $card->getMyCollection()->getOwner());
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder aux cartes d'un autre marchand !");
        }

        return $this->render('card/show.html.twig', [
            'card' => $card,
            'connect' => $hasAccess,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, CardRepository $cardRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        $canModify = false;
        if($this->isGranted('ROLE_ADMIN')) {
            $canModify = true;
        }
        $user = $this->getUser();
        if( $user ) {
            $member = $user->getMerchant();
            if ( $member &&  ($member == $card->myCollection->getOwner()) ) {
                $canModify = true;
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($card);
            $entityManager->flush();

            $this->addFlash('message', 'carte ajoutée');

            //$cardRepository->save($card, true);

            return $this->redirectToRoute('app_card_show', array(
                'id' => $card->id), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/edit.html.twig', [
            'card' => $card,
            'form' => $form,
            'connect' => $canModify
        ]);
    }

    #[Route('/{id}', name: 'app_card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $cardRepository->remove($card, true);
        }

        return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
    }
}
