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
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/collection")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
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

            return $this->redirectToRoute('app_merchant_show', array(
                'id' => $merchant->id,
            ), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('my_collection/new.html.twig', [
            'myCollection' => $myCollection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_my_collection_show', methods: ['GET'])]
    public function show(MyCollection $myCollection): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') ||
            ($this->getUser()->getMerchant() == $myCollection->getOwner());
        if(! $hasAccess) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de voir la collection d'un autre marchand");
        }

        return $this->render('my_collection/show.html.twig', [
            'myCollection' => $myCollection,
            'connect' => $hasAccess,
        ]);
    }

    /**
     * @Route("/{id_merchant}/edit/{id_collection}", name="app_my_collection_edit", methods={"GET", "POST"})
     * @ParamConverter("myCollection", options={"id" = "id_collection"})
     * @ParamConverter("merchant", options={"id" = "id_merchant"})
     */
    public function edit(Request $request, MyCollection $myCollection, Merchant $merchant, MyCollectionRepository $myCollectionRepository): Response
    {
        $form = $this->createForm(MyCollectionType::class, $myCollection);
        $form->handleRequest($request);

        $canModify = false;
        if($this->isGranted('ROLE_ADMIN')) {
            $canModify = true;
        }
        $user = $this->getUser();
        if( $user ) {
            $member = $user->getMerchant();
            if ( $member &&  ($member == $myCollection->getOwner()) ) {
                $canModify = true;
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $myCollectionRepository->save($myCollection, true);

            return $this->redirectToRoute('app_my_collection_show', array(
                'id' => $merchant->id,
            ), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('my_collection/edit.html.twig', [
            'myCollection' => $myCollection,
            'form' => $form,
            'connect' => $canModify
        ]);
    }

    #[Route('/{id}', name: 'app_my_collection_delete', methods: ['POST'])]
    public function delete(Request $request, MyCollection $myCollection, MyCollectionRepository $myCollectionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$myCollection->getId(), $request->request->get('_token'))) {
            $myCollectionRepository->remove($myCollection, true);
        }

        return $this->redirectToRoute('app_my_collection_index', [], Response::HTTP_SEE_OTHER);
    }
}
