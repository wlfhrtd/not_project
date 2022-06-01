<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Form\PaginationType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CustomerRepository $customerRepository): Response
    {
        // pagination
        $paginationForm = $this->createForm(PaginationType::class);
        $paginationForm->handleRequest($request);

        $paginationOffset = max(0, $request->query->getInt('offset', 0));

        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {

            $paginationOffset = ($paginationForm->get('page')->getData() - 1) * CustomerRepository::PAGINATOR_PER_PAGE;
        }

        $paginator = $customerRepository->getCustomerPaginator($paginationOffset);

        return $this->render('customer/index.html.twig', [
            'paginationForm' => $paginationForm->createView(),
            'customers' => $paginator,
            'paginationCap' => ceil(count($paginator) / CustomerRepository::PAGINATOR_PER_PAGE),
            'previous' => $paginationOffset - CustomerRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $paginationOffset + CustomerRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $customerRepository->hide($customer, true);
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
