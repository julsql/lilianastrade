<?php

namespace App\Controller;

use App\Entity\Merchant;
use App\Entity\MyCollection;
use App\Form\MyCollectionType;
use App\Repository\MyCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/collection')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MyCollectionController extends AbstractController
{
    #[Route('/', name: 'app_my_collection_index')]
    public function index(MyCollectionRepository $collectionRepository): Response
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            $deck = $collectionRepository->findAll();
        }
        else {
            $deck = array();
            $user = $this->getUser();
            if ($user and $user->getMerchant()) {
                $merchant = $user->getMerchant();
                $deck = $collectionRepository->findBy([
                    'owner' => $merchant
                ]);
            }
        }
        return $this->render('my_collection/index.html.twig', [
            'myCollections' => $deck
        ]);
    }

    #[Route('/new/{id}', name: 'app_my_collection_new', methods: ['GET', 'POST'])]
    #[IsGranted('MANAGE', 'merchant')]
    public function new(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
    {

        $myCollection = new MyCollection();
        $myCollection->setOwner($merchant);
        $form = $this->createForm(MyCollectionType::class, $myCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($myCollection);
            $entityManager->flush();

            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'collection ajoutée');
            // $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()
            // or to $this->get('session')->getFlashBag()->add();

            return $this->redirectToRoute('app_merchant_show_id', array(
                'id' => $merchant->id,
            ), Response::HTTP_SEE_OTHER);
        }

        return $this->render('my_collection/new.html.twig', [
            'myCollection' => $myCollection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_my_collection_show', methods: ['GET'])]
    public function show(MyCollection $myCollection): Response
    {
        $hasAccess = $this->isGranted('MANAGE', $myCollection);
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de voir la collection d'un autre marchand");
        }

        return $this->render('my_collection/show.html.twig', [
            'myCollection' => $myCollection,
            'connect' => $hasAccess,
        ]);
    }

    #[Route('/{id_merchant}/edit/{id_collection}', name: 'app_my_collection_edit', methods: ['GET', 'POST'])]
    #[IsGranted('MANAGE', 'myCollection')]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id_collection')] MyCollection $myCollection,
        #[MapEntity(id: 'id_merchant')] Merchant $merchant,
        MyCollectionRepository $myCollectionRepository,
    ): Response
    {
        $form = $this->createForm(MyCollectionType::class, $myCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $myCollectionRepository->save($myCollection, true);

            return $this->redirectToRoute('app_my_collection_show', array(
                'id' => $myCollection->getId(),
            ), Response::HTTP_SEE_OTHER);
        }

        return $this->render('my_collection/edit.html.twig', [
            'myCollection' => $myCollection,
            'form' => $form,
            'connect' => true
        ]);
    }

    #[Route('/{id}', name: 'app_my_collection_delete', methods: ['POST'])]
    #[IsGranted('MANAGE', 'myCollection')]
    public function delete(Request $request, MyCollection $myCollection, MyCollectionRepository $myCollectionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$myCollection->getId(), $request->request->get('_token'))) {
            $myCollectionRepository->remove($myCollection, true);
        }

        return $this->redirectToRoute('app_my_collection_index', [], Response::HTTP_SEE_OTHER);
    }
}
