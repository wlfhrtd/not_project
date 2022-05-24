<?php

namespace App\Controller;

use App\Entity\Street;
use App\Form\StreetType;
use App\Repository\StreetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/street')]
class StreetController extends AbstractController
{
    #[Route('/', name: 'app_street_index', methods: ['GET'])]
    public function index(StreetRepository $streetRepository): Response
    {
        return $this->render('street/index.html.twig', [
            'streets' => $streetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_street_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StreetRepository $streetRepository): Response
    {
        $street = new Street();
        $form = $this->createForm(StreetType::class, $street);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $streetRepository->add($street, true);

            return $this->redirectToRoute('app_street_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('street/new.html.twig', [
            'street' => $street,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_street_show', methods: ['GET'])]
    public function show(Street $street): Response
    {
        return $this->render('street/show.html.twig', [
            'street' => $street,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_street_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Street $street, StreetRepository $streetRepository): Response
    {
        $form = $this->createForm(StreetType::class, $street);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $streetRepository->add($street, true);

            return $this->redirectToRoute('app_street_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('street/edit.html.twig', [
            'street' => $street,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_street_delete', methods: ['POST'])]
    public function delete(Request $request, Street $street, StreetRepository $streetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$street->getId(), $request->request->get('_token'))) {
            $streetRepository->remove($street, true);
        }

        return $this->redirectToRoute('app_street_index', [], Response::HTTP_SEE_OTHER);
    }
}
