<?php

namespace App\Controller;

use App\Entity\Street;
use App\Form\PaginationType;
use App\Form\StreetType;
use App\Repository\StreetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/street')]
class StreetController extends AbstractController
{
    #[Route('/', name: 'app_street_index', methods: ['GET', 'POST'])]
    public function index(Request $request, StreetRepository $streetRepository): Response
    {
        /*
        $duplications = [];
        $streets = $streetRepository->findAll();
        for ($i = 0; $i < count($streets); $i++) {
            for ($j = $i + 1; $j < count($streets); $j++) {

                if ($streets[$i]->getName() === $streets[$j]->getName()) {
                    // dd($streets[$i], $streets[$j]);
                    $duplications[] = $streets[$j];

                }
            }
        }
        // dd($duplications);
        $streetRepository->removeArrayOf($duplications, true);
        */

        // pagination
        $paginationForm = $this->createForm(PaginationType::class);
        $paginationForm->handleRequest($request);

        $paginationOffset = max(0, $request->query->getInt('offset', 0));

        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {

            $paginationOffset = ($paginationForm->get('page')->getData() - 1) * StreetRepository::PAGINATOR_PER_PAGE;
        }

        $paginator = $streetRepository->getStreetPaginator($paginationOffset);

        return $this->render('street/index.html.twig', [
            'paginationForm' => $paginationForm->createView(),
            'streets' => $paginator,
            'paginationCap' => ceil(count($paginator) / StreetRepository::PAGINATOR_PER_PAGE),
            'previous' => $paginationOffset - StreetRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $paginationOffset + StreetRepository::PAGINATOR_PER_PAGE),
        ])->setSharedMaxAge(3600);
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
